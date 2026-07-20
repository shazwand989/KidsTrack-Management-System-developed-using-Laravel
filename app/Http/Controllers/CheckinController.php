<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Attendance;
use App\Models\TimerSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    // ============================================
    // 🔥 TIMER FUNCTIONS
    // ============================================

    private function getTimerForToday()
    {
        $today = Carbon::now('Asia/Kuala_Lumpur')->format('l');
        return TimerSetting::where('day_name', 'like', '%' . $today . '%')->first();
    }

    private function getCurrentSlot()
    {
        $timer = $this->getTimerForToday();
        if (!$timer) return null;

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');

        $morningStart = (int) str_replace(':', '', $timer->morning_start);
        $morningEnd = (int) str_replace(':', '', $timer->morning_end);
        $eveningStart = (int) str_replace(':', '', $timer->evening_start);
        $eveningEnd = (int) str_replace(':', '', $timer->evening_end);

        if ($currentTimeInt >= $morningStart && $currentTimeInt <= $morningEnd) {
            return [
                'slot' => 'morning',
                'type' => 'checkin',
                'label' => 'Morning (Check-in)',
                'start' => $timer->morning_start,
                'end' => $timer->morning_end
            ];
        }

        if ($currentTimeInt >= $eveningStart && $currentTimeInt <= $eveningEnd) {
            return [
                'slot' => 'evening',
                'type' => 'checkout',
                'label' => 'Evening (Check-out)',
                'start' => $timer->evening_start,
                'end' => $timer->evening_end
            ];
        }

        return null;
    }

    private function isLateForCheckin()
    {
        $timer = $this->getTimerForToday();
        if (!$timer) return false;

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');
        $morningStart = (int) str_replace(':', '', $timer->morning_start);

        // Late if after morning_start (the configured check-in time window start)
        return $currentTimeInt > $morningStart;
    }

    private function isLateForCheckout()
    {
        $timer = $this->getTimerForToday();
        if (!$timer) return false;

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');
        $eveningEnd = (int) str_replace(':', '', $timer->evening_end);

        // Late if after evening_end (the configured check-out time window end)
        return $currentTimeInt > $eveningEnd;
    }

    private function isWithinGracePeriod($slotType)
    {
        $timer = $this->getTimerForToday();
        if (!$timer) return false;

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');

        if ($slotType === 'checkin') {
            $morningStart = (int) str_replace(':', '', $timer->morning_start);
            $graceEnd = $morningStart + 15; // 15-minute grace period after check-in start
            return $currentTimeInt > $morningStart && $currentTimeInt <= $graceEnd;
        } else {
            $eveningEnd = (int) str_replace(':', '', $timer->evening_end);
            $graceEnd = $eveningEnd + 15; // 15-minute grace period after check-out end
            return $currentTimeInt > $eveningEnd && $currentTimeInt <= $graceEnd;
        }
    }

    // ============================================
    // 🔥 SHOW CHECKIN PAGE
    // ============================================
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $parentId = null;

        // Find parent ID based on role — now user IS the parent/guardian directly
        if (in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            $parentId = $user->id;
        } else {
            // Admin/teacher — use parent_id from query string if provided
            $parentId = $request->query('parent_id');
        }

        if (!$parentId) {
            return redirect()->route('kiosk.index')->with('error', 'No linked parent found.');
        }

        $children = Child::where(function($q) use ($parentId) {
                $q->where('parent_id', $parentId)
                  ->orWhere('second_parent_id', $parentId);
            })
            ->where('is_active', true)
            ->with('classroom')
            ->get();

        // Only show children checked in today and NOT yet checked out
        $checkedInChildren = [];
        foreach ($children as $child) {
            $att = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();
            if ($att && $att->checkin_time && !$att->checkout_time) {
                $child->checkin_time = $att->checkin_time;
                $checkedInChildren[] = $child;
            }
        }

        if (empty($checkedInChildren)) {
            return redirect()->route('kiosk.index')
                ->with('info', 'Tiada anak yang perlu check-out.');
        }

        if (count($checkedInChildren) === 1) {
            return redirect()->route('kiosk.checkin.page', $checkedInChildren[0]->id);
        }

        $parent = \App\Models\User::find($parentId);
        return view('kiosk.checkout-select', [
            'children' => $checkedInChildren,
            'parent' => $parent
        ]);
    }

    public function showCheckinPage($childId)
    {
        try {
            $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
            $user = Auth::user();

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $currentTime = $now->format('h:i A');

            // Get parent — user IS the parent directly
            $parent = $user;
            if (!in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
                $parent = \App\Models\User::whereIn('role', ['parent1'])->first();
            }

            // Get timer setting
            $timerSetting = TimerSetting::where('day_name', 'like', '%' . $now->format('l') . '%')->first();

            // Check attendance
            $todayAttendance = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();

            $hasCheckin = $todayAttendance && $todayAttendance->checkin_time;
            $hasCheckout = $todayAttendance && $todayAttendance->checkout_time;

            // Check if late
            $slot = $this->getCurrentSlot();
            $isLate = $slot && $slot['slot'] === 'morning' ? $this->isLateForCheckin() : false;

            // 🔥🔥🔥 CHECKOUT LOGIC - LENGKAP 🔥🔥🔥
            $canCheckout = false;
            $checkoutMessage = '⏰ Checkout Belum Tersedia';
            $checkoutInfoClass = '';
            $isLateCheckout = false;

            if ($timerSetting) {
                $currentTimeInt = (int) $now->format('Hi');
                $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
                $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);
                $checkoutStartTime = date('H:i', strtotime($timerSetting->evening_start));
                $checkoutEndTime = date('H:i', strtotime($timerSetting->evening_end));

                if ($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt) {
                    $canCheckout = true;
                    $checkoutMessage = '✅ Waktu checkout: ' . $checkoutStartTime . ' - ' . $checkoutEndTime;
                    $checkoutInfoClass = 'active';
                } else if ($currentTimeInt < $eveningStartInt) {
                    $canCheckout = false;
                    $checkoutMessage = '🕐 Checkout bermula pada ' . $checkoutStartTime;
                    $checkoutInfoClass = '';
                } else {
                    $canCheckout = true;
                    $isLateCheckout = true;
                    $checkoutMessage = '⏰ Late Checkout (Melebihi waktu operasi)';
                    $checkoutInfoClass = 'active late';
                }
            }

            // Get user role
            $userRole = $this->getUserRole($user, $child);
            $roleData = $this->getRoleData($userRole);
            $parentName = $this->getParentName($user, $child);

            // Get all children for this parent
            $allChildren = $this->getAllChildren($user, $child);

            // Get checked in children data
            $checkedInData = [];
            $checkedChildren = collect();

            foreach ($allChildren as $c) {
                $att = Attendance::where('child_id', $c->id)
                    ->whereDate('date', $today)
                    ->first();

                if ($att && $att->checkin_time && !$att->checkout_time) {
                    $checkedInData[] = [
                        'name' => $c->name,
                        'classroom' => $c->classroom->name ?? '-',
                        'time' => Carbon::parse($att->checkin_time)->format('h:i A'),
                        'initial' => strtoupper(substr($c->name, 0, 1))
                    ];
                    $checkedChildren->push($c);
                }
            }

            // Add checked_in_today to child for blade
            $child->checked_in_today = $hasCheckin;
            $child->checked_out_today = $hasCheckout;
            if ($hasCheckin) {
                $child->checked_in_time = $todayAttendance->checkin_time;
            }

            // 🔥 SELECTED DATE UNTUK CALENDAR
            $selectedDate = $today;

            return view('kiosk.checkin-page', compact(
                'child',
                'parent',
                'currentTime',
                'isLate',
                'hasCheckin',
                'hasCheckout',
                'canCheckout',
                'checkoutMessage',      // ⭐ TAMBAH
                'checkoutInfoClass',    // ⭐ TAMBAH
                'isLateCheckout',       // ⭐ TAMBAH
                'allChildren',
                'checkedChildren',      // ⭐ TAMBAH
                'checkedInData',
                'userRole',
                'roleData',
                'parentName',
                'timerSetting',
                'now',
                'selectedDate'          // ⭐ TAMBAH
            ));

        } catch (\Exception $e) {
            Log::error('showCheckinPage Error: ' . $e->getMessage());
            return redirect()->route('kiosk.index')
                ->with('error', 'Ralat: ' . $e->getMessage());
        }
    }

    // ============================================
    // 🔥 SUBMIT ATTENDANCE (Check In / Check Out)
    // ============================================
    public function submitAttendance(Request $request)
    {
        try {
            $request->validate([
                'child_id' => 'required|exists:children,id',
                'parent_id' => 'required|exists:users,id',
                'action' => 'required|in:checkin,checkout'
            ]);

            $child = Child::find($request->child_id);
            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $now = Carbon::now('Asia/Kuala_Lumpur');

            if (!$child->classroom_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini belum ditempatkan di mana-mana kelas! Sila hubungi admin.'
                ]);
            }

            if ($request->action == 'checkin') {
                return $this->processCheckin($child, $request->parent_id, $today, $now);
            } else {
                return $this->processCheckout($child, $request->parent_id, $today, $now);
            }

        } catch (\Exception $e) {
            Log::error('submitAttendance Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 🔥 PROCESS CHECKIN
    // ============================================
    private function processCheckin($child, $parentId, $today, $now)
    {
        // Check already checked in — use DATE() to handle timezone
        $existing = Attendance::where('child_id', $child->id)
            ->whereRaw('DATE(date) = ?', [$today])
            ->first();

        if ($existing && $existing->checkin_time) {
            if ($existing->checkout_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini sudah check-in dan check-out hari ini! Tidak boleh check-in lagi.'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Anak ini sudah check-in hari ini pada ' . Carbon::parse($existing->checkin_time)->format('h:i A') . '!'
            ]);
        }

        // 🔥 DOUBLE CHECK: use DATE() to avoid timezone issues
        $alreadyChecked = Attendance::where('child_id', $child->id)
            ->whereRaw('DATE(date) = ?', [$today])
            ->whereNotNull('checkin_time')
            ->exists();

        if ($alreadyChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Anak ini sudah check-in hari ini!'
            ]);
        }

        // Check late
        $isLate = $this->isLateForCheckin();
        $withinGrace = $this->isWithinGracePeriod('checkin');

        // Get parent name — use child's actual parent via hasOneThrough
        $childParent = $child->parent;
        $parentName = $childParent ? $childParent->name : 'Unknown';

        $status = 'present';
        $statusNote = '✅ Check-in On Time';

        if ($isLate && $withinGrace) {
            $status = 'late';
            $statusNote = '⏰ Late (Grace Period)';
        } else if ($isLate && !$withinGrace) {
            $status = 'late';
            $statusNote = '⏰ Late';
        }

        if ($existing) {
            $existing->update([
                'checkin_time' => $now->format('H:i:s'),
                'status' => $status,
                'status_note' => $statusNote,
                'drop_off_by' => $parentName,
                'is_verified' => true
            ]);
        } else {
            Attendance::create([
                'child_id' => $child->id,
                'parent_id' => $parentId,
                'date' => $today,
                'checkin_time' => $now->format('H:i:s'),
                'status' => $status,
                'status_note' => $statusNote,
                'drop_off_by' => $parentName,
                'is_verified' => true
            ]);
        }

        $this->sendTelegramNotification($child, $parentName, 'checkin', $isLate);

        return response()->json([
            'success' => true,
            'message' => $isLate ? '⏰ Check-in berjaya! (Late)' : '✅ Check-in berjaya! (On Time)',
            'child_name' => $child->name,
            'child_classroom' => $child->classroom->name ?? 'Tiada kelas',
            'checkin_time' => $now->format('h:i A'),
            'status_label' => $isLate ? '⏰ Late' : '✅ On Time',
            'is_late' => $isLate,
            'status_note' => $statusNote
        ]);
    }

    // ============================================
    // 🔥 PROCESS CHECKOUT
    // ============================================
    private function processCheckout($child, $parentId, $today, $now)
    {
        $attendance = Attendance::where('child_id', $child->id)
            ->whereRaw('DATE(date) = ?', [$today])
            ->first();

        if (!$attendance || !$attendance->checkin_time) {
            return response()->json([
                'success' => false,
                'message' => 'Anak ini belum check-in hari ini! Sila check-in dahulu.'
            ]);
        }

        if ($attendance->checkout_time) {
            return response()->json([
                'success' => false,
                'message' => 'Anak ini sudah check-out hari ini!'
            ]);
        }

        // Get parent name — use child's actual parent via hasOneThrough
        $childParent = $child->parent;
        $parentName = $childParent ? $childParent->name : 'Unknown';

        // Check late checkout using the same logic as isLateForCheckout
        $isLateCheckout = $this->isLateForCheckout();
        $withinGrace = $this->isWithinGracePeriod('checkout');

        $status = 'checkout';
        $statusNote = '✅ Check-out On Time';

        if ($isLateCheckout && $withinGrace) {
            $status = 'late_checkout';
            $statusNote = '⏰ Late Checkout (Grace Period)';
        } else if ($isLateCheckout && !$withinGrace) {
            $status = 'late_checkout';
            $statusNote = '⏰ Late Checkout';
        }

        $attendance->update([
            'checkout_time' => $now->format('H:i:s'),
            'status' => $status,
            'status_note' => $statusNote,
            'pickup_by' => $parentName,
        ]);

        $this->sendTelegramNotification($child, $parentName, 'checkout', $isLateCheckout);

        return response()->json([
            'success' => true,
            'message' => $isLateCheckout ? '⏰ Check-out berjaya! (Late)' : '✅ Check-out berjaya! (On Time)',
            'child_name' => $child->name,
            'child_classroom' => $child->classroom->name ?? 'Tiada kelas',
            'checkout_time' => $now->format('h:i A'),
            'status_label' => $isLateCheckout ? '⏰ Late' : '✅ On Time',
            'is_late' => $isLateCheckout,
            'status_note' => $statusNote
        ]);
    }

    // ============================================
    // 🔥 CHECKIN ALL
    // ============================================
    public function checkinAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|integer',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            $checkedCount = 0;
            $alreadyCount = 0;
            $lateCount = 0;

            $parent = \App\Models\User::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';

            foreach ($request->child_ids as $childId) {
                $child = Child::find($childId);
                if (!$child) continue;

                $existing = Attendance::where('child_id', $childId)
                    ->whereDate('date', $today)
                    ->first();

                if ($existing && $existing->checkin_time) {
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'already_checked',
                        'time' => Carbon::parse($existing->checkin_time)->format('h:i A')
                    ];
                    $alreadyCount++;
                    continue;
                }

                // Check late for bulk checkin
                $isLate = $this->isLateForCheckin();

                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $isLate ? 'late' : 'present',
                        'status_note' => $isLate ? '⏰ Late check-in' : '✅ Check-in via Checkin All',
                        'drop_off_by' => $parentName,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $childId,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $isLate ? 'late' : 'present',
                        'status_note' => $isLate ? '⏰ Late check-in' : '✅ Check-in via Checkin All',
                        'drop_off_by' => $parentName,
                        'is_verified' => true
                    ]);
                }

                if ($isLate) {
                    $lateCount++;
                } else {
                    $checkedCount++;
                }

                $results[] = [
                    'name' => $child->name,
                    'status' => $isLate ? 'late' : 'checked_in',
                    'time' => $now->format('h:i A')
                ];

                $this->sendTelegramNotification($child, $parentName, 'checkin', $isLate);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ ' . ($checkedCount + $lateCount) . ' anak berjaya check-in!',
                'results' => $results,
                'checked_count' => $checkedCount,
                'late_count' => $lateCount,
                'already_count' => $alreadyCount
            ]);

        } catch (\Exception $e) {
            Log::error('checkinAll Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 🔥 CHECKOUT ALL
    // ============================================
    public function checkoutAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|integer',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            $checkoutCount = 0;
            $alreadyCount = 0;

            $parent = \App\Models\User::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';

            foreach ($request->child_ids as $childId) {
                $child = Child::find($childId);
                if (!$child) continue;

                $attendance = Attendance::where('child_id', $childId)
                    ->whereDate('date', $today)
                    ->first();

                if (!$attendance || !$attendance->checkin_time) {
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'not_checked_in',
                        'time' => ''
                    ];
                    continue;
                }

                if ($attendance->checkout_time) {
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'already_checked',
                        'time' => Carbon::parse($attendance->checkout_time)->format('h:i A')
                    ];
                    $alreadyCount++;
                    continue;
                }

                // Check late checkout for bulk
                $timer = $this->getTimerForToday();
                $isLateCheckout = false;

                if ($timer) {
                    $currentTimeInt = (int) $now->format('Hi');
                    $eveningStartInt = (int) str_replace(':', '', $timer->evening_start);
                    $eveningEndInt = (int) str_replace(':', '', $timer->evening_end);

                    if (!($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt)) {
                        $isLateCheckout = true;
                    }
                }

                $attendance->update([
                    'checkout_time' => $now->format('H:i:s'),
                    'status' => $isLateCheckout ? 'late_checkout' : 'checkout',
                    'status_note' => $isLateCheckout ? '⏰ Late Checkout' : '✅ Check-out via Checkout All',
                    'pickup_by' => $parentName,
                ]);

                $checkoutCount++;
                $results[] = [
                    'name' => $child->name,
                    'status' => 'checkout',
                    'time' => $now->format('h:i A')
                ];

                $this->sendTelegramNotification($child, $request->parent_id, 'checkout', $isLateCheckout);
            }

            return response()->json([
                'success' => true,
                'message' => '✅ ' . $checkoutCount . ' anak berjaya check-out!',
                'results' => $results,
                'checkout_count' => $checkoutCount,
                'already_count' => $alreadyCount
            ]);

        } catch (\Exception $e) {
            Log::error('checkoutAll Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 🔥 HELPER FUNCTIONS
    // ============================================

    private function getUserRole($user, $child)
    {
        if (!$user) return 'guest';

        if (in_array($user->role, ['admin', 'teacher'])) {
            return 'admin';
        }

        // Check if this user is the child's main parent (via hasOneThrough)
        if ($child->parent && $child->parent->id == $user->id) {
            return 'main_parent';
        }

        if ($user->role === 'second_parent' || $user->role === 'parent2') {
            return 'second_parent';
        }

        if ($user->role === 'guardian') {
            if ($child->guardian && $child->guardian->id == $user->id) {
                return 'guardian';
            }
        }

        return 'guest';
    }

    private function getRoleData($role)
    {
        $roleMap = [
            'main_parent' => [
                'class' => 'main-parent',
                'badge_class' => 'main-parent',
                'badge_text' => '👨‍👩‍👦 Main Parent',
                'icon' => '👨‍👩‍👦',
                'display_name' => 'Main Parent',
                'name_class' => 'main',
                'border_class' => 'main-parent-border',
                'avatar_class' => 'main-parent-avatar',
                'tag_class' => 'main-parent-tag',
            ],
            'second_parent' => [
                'class' => 'second-parent',
                'badge_class' => 'second-parent',
                'badge_text' => '👫 Second Parent',
                'icon' => '👫',
                'display_name' => 'Second Parent',
                'name_class' => 'second',
                'border_class' => 'second-parent-border',
                'avatar_class' => 'second-parent-avatar',
                'tag_class' => 'second-parent-tag',
            ],
            'guardian' => [
                'class' => 'guardian',
                'badge_class' => 'guardian',
                'badge_text' => '🛡️ Guardian',
                'icon' => '🛡️',
                'display_name' => 'Guardian',
                'name_class' => 'guardian',
                'border_class' => 'guardian-border',
                'avatar_class' => 'guardian-avatar',
                'tag_class' => 'guardian-tag',
            ],
            'admin' => [
                'class' => 'admin',
                'badge_class' => 'admin',
                'badge_text' => '👑 Admin',
                'icon' => '👑',
                'display_name' => 'Admin',
                'name_class' => 'admin',
                'border_class' => 'admin-border',
                'avatar_class' => 'admin-avatar',
                'tag_class' => 'admin-tag',
            ],
        ];

        return $roleMap[$role] ?? $roleMap['main_parent'];
    }

    private function getParentName($user, $child)
    {
        if (!$user) return 'Parent';

        if (in_array($user->role, ['admin', 'teacher'])) {
            return $user->name ?? 'Admin';
        }

        if ($child->parent && $child->parent->id == $user->id) {
            return $user->name ?? 'Main Parent';
        }

        if ($user->role === 'second_parent' || $user->role === 'parent2') {
            return $user->name ?? 'Second Parent';
        }

        if ($user->role === 'guardian') {
            if ($child->guardian && $child->guardian->id == $user->id) {
                return $user->name ?? 'Guardian';
            }
        }

        return 'Parent';
    }

    private function getAllChildren($user, $currentChild)
    {
        $allChildren = collect();

        if (!$user) {
            $allChildren->push($currentChild);
            return $allChildren;
        }

        if (in_array($user->role, ['admin', 'teacher'])) {
            $allChildren = Child::where('is_active', true)->get();
        } else {
            // Get children linked to this user via guardianships
            $allChildren = Child::whereHas('guardianships', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('is_active', true)->get();
        }

        if (!$allChildren->contains('id', $currentChild->id)) {
            $allChildren->push($currentChild);
        }

        return $allChildren;
    }

    private function getCheckedInData($allChildren, $today)
    {
        $checkedInData = [];

        foreach ($allChildren as $child) {
            $attendance = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();

            if ($attendance && $attendance->checkin_time && !$attendance->checkout_time) {
                $checkedInData[] = [
                    'name' => $child->name,
                    'classroom' => $child->classroom->name ?? '-',
                    'time' => Carbon::parse($attendance->checkin_time)->format('h:i A'),
                    'initial' => strtoupper(substr($child->name, 0, 1))
                ];
            }
        }

        return $checkedInData;
    }

    private function sendTelegramNotification($child, $parentName, $action, $isLate = false)
    {
        // Find the parent via hasOneThrough relationship
        $parent = $child->parent;

        if (!$parent || !$parent->telegram_notification || !$parent->telegram_id) {
            return;
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $statusEmoji = $isLate ? '⚠️' : '✅';
        $statusText = $isLate ? 'LATE' : 'ON TIME';

        $message = "🧸 <b>KidsTrack Notification</b>\n\n";
        $message .= "👶 <b>Child:</b> {$child->name}\n";
        $message .= "🏫 <b>Class:</b> " . ($child->classroom->name ?? 'N/A') . "\n";
        $message .= "👤 <b>Parent:</b> {$parentName}\n";
        $message .= "📅 <b>Date:</b> " . $now->format('d M Y') . "\n";
        $message .= "⏰ <b>Time:</b> " . $now->format('h:i A') . "\n";

        if ($action == 'checkin') {
            $message .= "📥 <b>Action:</b> Check-in\n";
        } else {
            $message .= "📤 <b>Action:</b> Check-out\n";
        }

        $message .= "{$statusEmoji} <b>Status:</b> {$statusText}";

        $this->telegram->sendMessage($parent->telegram_id, $message);
    }
}


