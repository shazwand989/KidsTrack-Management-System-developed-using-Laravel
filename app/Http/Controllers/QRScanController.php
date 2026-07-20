<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Attendance;
use App\Models\TimerSetting;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QRScanController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    // ============================================
    // ðŸ”¥ TIMER FUNCTIONS
    // ============================================

    private function getTimerForToday()
    {
        $today = Carbon::now('Asia/Kuala_Lumpur')->format('l');
        return TimerSetting::where('day_name', 'like', '%' . $today . '%')->first();
    }

    private function getCurrentSlot()
    {
        $timer = $this->getTimerForToday();

        if (!$timer) {
            return null;
        }

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

    private function getTimerSlotInfo()
    {
        $timer = $this->getTimerForToday();

        if (!$timer) {
            return null;
        }

        return [
            'morning' => $timer->morning_start . ' - ' . $timer->morning_end,
            'evening' => $timer->evening_start . ' - ' . $timer->evening_end,
            'day' => $timer->day_name
        ];
    }

    private function isMorningSlot()
    {
        $slot = $this->getCurrentSlot();
        return $slot && $slot['slot'] === 'morning';
    }

    private function isEveningSlot()
    {
        $slot = $this->getCurrentSlot();
        return $slot && $slot['slot'] === 'evening';
    }

    private function isLateForCheckin()
    {
        $timer = $this->getTimerForToday();
        if (!$timer) {
            return false;
        }

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');
        $morningEnd = (int) str_replace(':', '', $timer->morning_end);

        return $currentTimeInt > $morningEnd;
    }

    private function isLateForCheckout()
    {
        $timer = $this->getTimerForToday();
        if (!$timer) {
            return false;
        }

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');
        $eveningEnd = (int) str_replace(':', '', $timer->evening_end);

        return $currentTimeInt > $eveningEnd;
    }

    private function isWithinGracePeriod($slotType)
    {
        $timer = $this->getTimerForToday();
        if (!$timer) {
            return false;
        }

        $currentTime = Carbon::now('Asia/Kuala_Lumpur');
        $currentTimeInt = (int) $currentTime->format('Hi');

        if ($slotType === 'checkin') {
            $morningEnd = (int) str_replace(':', '', $timer->morning_end);
            $graceEnd = $morningEnd + 15;
            return $currentTimeInt > $morningEnd && $currentTimeInt <= $graceEnd;
        } else {
            $eveningEnd = (int) str_replace(':', '', $timer->evening_end);
            $graceEnd = $eveningEnd + 15;
            return $currentTimeInt > $eveningEnd && $currentTimeInt <= $graceEnd;
        }
    }

    public static function getRoleDataStatic($role)
    {
        $roleMap = [
            'parent1' => ['badge_class' => 'main-parent', 'badge_text' => 'ðŸ‘¨â€ðŸ‘©â€ðŸ‘¦ Main Parent', 'icon' => 'ðŸ‘¨â€ðŸ‘©â€ðŸ‘¦', 'display_name' => 'Main Parent', 'name_class' => 'main'],
            'parent' => ['badge_class' => 'main-parent', 'badge_text' => 'ðŸ‘¨â€ðŸ‘©â€ðŸ‘¦ Main Parent', 'icon' => 'ðŸ‘¨â€ðŸ‘©â€ðŸ‘¦', 'display_name' => 'Main Parent', 'name_class' => 'main'],
            'parent2' => ['badge_class' => 'second-parent', 'badge_text' => 'ðŸ‘« Second Parent', 'icon' => 'ðŸ‘«', 'display_name' => 'Second Parent', 'name_class' => 'second'],
            'second_parent' => ['badge_class' => 'second-parent', 'badge_text' => 'ðŸ‘« Second Parent', 'icon' => 'ðŸ‘«', 'display_name' => 'Second Parent', 'name_class' => 'second'],
            'guardian' => ['badge_class' => 'guardian', 'badge_text' => 'ðŸ›¡ï¸ Guardian', 'icon' => 'ðŸ›¡ï¸', 'display_name' => 'Guardian', 'name_class' => 'guardian'],
            'admin' => ['badge_class' => 'admin', 'badge_text' => 'ðŸ‘‘ Admin', 'icon' => 'ðŸ‘‘', 'display_name' => 'Admin', 'name_class' => 'admin'],
            'teacher' => ['badge_class' => 'teacher', 'badge_text' => 'ðŸ‘¨â€ðŸ« Teacher', 'icon' => 'ðŸ‘¨â€ðŸ«', 'display_name' => 'Teacher', 'name_class' => 'teacher'],
        ];

        return $roleMap[$role] ?? ['badge_class' => 'parent', 'badge_text' => 'ðŸ‘¤ User', 'icon' => 'ðŸ‘¤', 'display_name' => 'User', 'name_class' => ''];
    }

    // ============================================
    // KIOSK MAIN PAGE
    // ============================================
    public function kiosk()
    {
        $children = Child::with('classroom')->get();
        return view('kiosk.index', compact('children'));
    }

    // ============================================
    // STEP 1: CHECK QR CODE
    // ============================================
    public function checkGPS(Request $request)
    {
        try {
            $qrData = $request->qr_code;

            $child = Child::where('qr_code', $qrData)->first();
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'âŒ QR Code tidak sah!'
                ], 404);
            }

            $slot = $this->getCurrentSlot();
            $timerInfo = $this->getTimerSlotInfo();

            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $existing = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();

            $hasCheckin = $existing && $existing->checkin_time;
            $hasCheckout = $existing && $existing->checkout_time;

            /** @var User|null $user */
            $user = Auth::user();

            if ($user) {
                $hasAccess = false;

                if ($user->role === 'second_parent' || $user->role === 'parent2') {
                    $hasAccess = $user->children->contains('id', $child->id);
                }

                if (!$hasAccess && in_array($user->role, ['parent', 'parent1'])) {
                    $hasAccess = $user->children->contains('id', $child->id);
                }

                if (!$hasAccess && $user->role === 'guardian') {
                    $hasAccess = $user->children->contains('id', $child->id);
                }

                if (!$hasAccess && in_array($user->role, ['admin', 'teacher'])) {
                    $hasAccess = true;
                }

                if (!$hasAccess) {
                    return response()->json([
                        'success' => false,
                        'message' => 'âŒ Anda tidak mempunyai akses ke anak ini!'
                    ], 403);
                }
            }

            $isLate = false;
            if ($slot) {
                if ($slot['slot'] === 'morning') {
                    $isLate = $this->isLateForCheckin();
                } else if ($slot['slot'] === 'evening') {
                    $isLate = $this->isLateForCheckout();
                }
            }

            return response()->json([
                'success' => true,
                'child_id' => $child->id,
                'child_name' => $child->name,
                'slot' => $slot,
                'timer_info' => $timerInfo,
                'has_checkin' => $hasCheckin,
                'has_checkout' => $hasCheckout,
                'is_late' => $isLate,
                'redirect' => route('kiosk.confirm.child', $child->id)
            ]);

        } catch (\Exception $e) {
            Log::error('checkGPS Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // STEP 2: CONFIRM CHILD - REDIRECT KE ADD ANOTHER
    // ============================================
    public function confirmChild($childId)
    {
        try {
            $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
            /** @var User|null $user */
            $user = Auth::user();
            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $now = Carbon::now('Asia/Kuala_Lumpur');

            // Cari parent ID
            $parentId = $user ? $user->id : null;

            if (!$parentId) {
                $firstParent = \App\Models\User::where('role', 'parent1')->first();
                $parentId = $firstParent ? $firstParent->id : 1;
            }

            // CHECK ATTENDANCE - AUTO CHECK-IN
            $existing = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();

            $hasCheckin = $existing && $existing->checkin_time;
            $hasCheckout = $existing && $existing->checkout_time;

            if (!$hasCheckin && !$hasCheckout) {
                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => 'âœ… Check-in via Confirm Child',
                        'drop_off_by' => 'Kiosk',
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $child->id,
                        'parent_id' => $parentId,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => 'âœ… Check-in via Confirm Child',
                        'drop_off_by' => 'Kiosk',
                        'is_verified' => true
                    ]);
                }
            }

            // ðŸ”¥ REDIRECT KE AddAnotherChildController
            return redirect()->route('kiosk.add.another', $childId);

        } catch (\Exception $e) {
            Log::error('confirmChild Error: ' . $e->getMessage());
            return redirect()->route('kiosk.add.another', $childId)
                ->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }

    // ============================================
    // STEP 3: SHOW CHECKIN PAGE
    // ============================================
    public function showCheckinPage($childId)
    {
        $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
        /** @var User|null $user */
        $user = Auth::user();
        $parent = $user;

        if (!$parent) {
            $parent = \App\Models\User::where('role', 'parent1')->first();
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $hour = (int)$now->format('H');
        $currentTime = $now->format('h:i A');

        $timerSetting = TimerSetting::where('day_name', 'like', '%' . $now->format('l') . '%')->first();

        $today = $now->toDateString();
        $todayAttendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        $hasCheckin = $todayAttendance && $todayAttendance->checkin_time;
        $hasCheckout = $todayAttendance && $todayAttendance->checkout_time;

        $slot = $this->getCurrentSlot();
        $isLate = $slot && $slot['slot'] === 'morning' ? $this->isLateForCheckin() : false;

        $canCheckout = false;
        if ($timerSetting) {
            $currentTimeInt = (int) $now->format('Hi');
            $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
            $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);

            if ($currentTimeInt >= $eveningStartInt) {
                $canCheckout = true;
            }
        }

        $userRole = 'unknown';
        $parentName = 'Parent';
        $isMainParent = false;
        $isSecondParent = false;
        $isGuardian = false;

        if ($user) {
            if (in_array($user->role, ['parent', 'parent1'])) {
                $parentName = $user->name ?? 'Parent';
                $userRole = 'main_parent';
                $isMainParent = true;
            }

            if ($user->role === 'second_parent' || $user->role === 'parent2') {
                $userRole = 'second_parent';
                $isSecondParent = true;
                $parentName = $user->name ?? 'Second Parent';
            }

            if ($user->role === 'guardian') {
                $isGuardian = true;
                $userRole = 'guardian';
                $parentName = $user->name;
            }

            if (in_array($user->role, ['admin', 'teacher'])) {
                $userRole = 'admin';
                $parentName = $user->name;
            }
        }

        $allChildren = collect();
        $checkedChildren = collect();
        $checkedInData = [];

        if ($user) {
            $allChildren = $user->children()->where('is_active', true)->get();

            if (!$allChildren->contains('id', $child->id)) {
                $allChildren->push($child);
            }

            $checkedChildren = $allChildren->filter(function($c) use ($today) {
                $attendance = Attendance::where('child_id', $c->id)
                    ->whereDate('date', $today)
                    ->first();
                return $attendance && $attendance->checkin_time && !$attendance->checkout_time;
            });

            $checkedInData = $checkedChildren->map(function($c) {
                $attendance = Attendance::where('child_id', $c->id)
                    ->whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                    ->first();
                return [
                    'name' => $c->name,
                    'classroom' => $c->classroom->name ?? '-',
                    'time' => $attendance ? Carbon::parse($attendance->checkin_time)->format('h:i A') : '',
                    'initial' => strtoupper(substr($c->name, 0, 1))
                ];
            })->values()->toArray();
        }

        return view('kiosk.checkin-page', compact(
            'child', 'parent', 'currentTime', 'isLate',
            'hasCheckin', 'hasCheckout', 'canCheckout',
            'allChildren', 'checkedChildren', 'checkedInData',
            'userRole', 'parentName', 'isMainParent', 'isSecondParent', 'isGuardian',
            'timerSetting',
            'now'
        ));
    }

    // ============================================
    // STEP 4: SUBMIT ATTENDANCE
    // ============================================
    public function submitAttendance(Request $request)
    {
        try {
            $request->validate([
                'child_id' => 'required|exists:children,id',
                'parent_id' => 'required|exists:users,id',
                'action' => 'required|in:checkin,checkout'
            ]);

            $slot = $this->getCurrentSlot();
            $timerInfo = $this->getTimerSlotInfo();

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
                $existing = Attendance::where('child_id', $child->id)
                    ->whereDate('date', $today)
                    ->first();

                if ($existing && $existing->checkin_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anak ini sudah check-in hari ini!'
                    ]);
                }

                $isLate = $this->isLateForCheckin();
                $withinGrace = $this->isWithinGracePeriod('checkin');

                if ($isLate) {
                    $request->validate([
                        'late_reason' => 'required|string|max:255'
                    ]);
                }

                $status = 'present';
                $statusNote = 'âœ… Check-in berjaya';

                if ($isLate && $withinGrace) {
                    $status = 'late';
                    $statusNote = 'â° Late check-in (within grace period)';
                } else if ($isLate && !$withinGrace) {
                    $status = 'late';
                    $statusNote = 'â° Late check-in (past grace period)';
                }

                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $status,
                        'status_note' => $statusNote,
                        'late_reason' => $isLate ? $request->late_reason : null,
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $child->id,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $status,
                        'status_note' => $statusNote,
                        'late_reason' => $isLate ? $request->late_reason : null,
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                }

                $this->sendTelegramNotification($child, $request->parent_id, 'checkin', $isLate, $request->late_reason);

                return response()->json([
                    'success' => true,
                    'message' => $isLate ? 'â° Check-in berjaya! (Late)' : 'âœ… Check-in berjaya! (On Time)',
                    'child_name' => $child->name,
                    'child_classroom' => $child->classroom->name ?? 'Tiada kelas',
                    'checkin_time' => $now->format('h:i A'),
                    'is_late' => $isLate,
                    'slot' => $slot,
                    'timer_info' => $timerInfo,
                    'status_note' => $statusNote
                ]);

            } else {
                // CHECKOUT
                $attendance = Attendance::where('child_id', $child->id)
                    ->whereDate('date', $today)
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

                $timer = $this->getTimerForToday();
                $isLateCheckout = false;
                $lateCheckoutMessage = 'âœ… Check-out berjaya (On Time)';

                if ($timer) {
                    $currentTimeInt = (int) $now->format('Hi');
                    $eveningStartInt = (int) str_replace(':', '', $timer->evening_start);
                    $eveningEndInt = (int) str_replace(':', '', $timer->evening_end);

                    if (!($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt)) {
                        $isLateCheckout = true;
                        $lateCheckoutMessage = 'â° Check-out berjaya (Late Checkout)';
                    }
                }

                $status = 'checkout';
                $statusNote = 'âœ… Check-out berjaya';

                if ($isLateCheckout) {
                    $status = 'late_checkout';
                    $statusNote = 'â° Late Checkout';
                }

                $attendance->update([
                    'checkout_time' => $now->format('H:i:s'),
                    'status' => $status,
                    'status_note' => $statusNote,
                    'pickup_by' => 'Parent ID: ' . $request->parent_id,
                ]);

                $this->sendTelegramNotification($child, $request->parent_id, 'checkout', $isLateCheckout);

                return response()->json([
                    'success' => true,
                    'message' => $isLateCheckout ? 'â° Check-out berjaya! (Late)' : 'âœ… Check-out berjaya! (On Time)',
                    'child_name' => $child->name,
                    'child_classroom' => $child->classroom->name ?? 'Tiada kelas',
                    'checkout_time' => $now->format('h:i A'),
                    'is_late' => $isLateCheckout,
                    'slot' => $slot,
                    'timer_info' => $timerInfo,
                    'status_note' => $statusNote
                ]);
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
    // SEND TELEGRAM NOTIFICATION
    // ============================================
    private function sendTelegramNotification($child, $parentId, $action, $isLate = false, $lateReason = null)
    {
        $parent = \App\Models\User::find($parentId);
        if (!$parent || !$parent->telegram_chat_id) {
            return;
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $slot = $this->getCurrentSlot();
        $slotLabel = $slot ? $slot['label'] : 'Unknown';
        $timerInfo = $this->getTimerSlotInfo();

        $message = "ðŸ§¸ KidsTrack Alert\n\n";
        $message .= "ðŸ‘¶ Child: {$child->name}\n";
        $message .= "ðŸ« Class: " . ($child->classroom->name ?? 'No class') . "\n";

        if ($action == 'checkin') {
            $message .= "âœ… Checked-in at: " . $now->format('h:i A') . "\n";
            $message .= "ðŸ“Š Status: " . ($isLate ? 'â° Late' : 'âœ… On Time') . "\n";
            $message .= "â±ï¸ Slot: " . $slotLabel;
            if ($isLate && $lateReason) {
                $message .= "\nðŸ“ Reason: " . $lateReason;
            }
        } else {
            $message .= "ðŸ‘‹ Checked-out at: " . $now->format('h:i A') . "\n";
            $message .= "ðŸ“Š Status: " . ($isLate ? 'â° Late Checkout' : 'âœ… On Time') . "\n";
            $message .= "â±ï¸ Slot: " . $slotLabel;
        }

        if ($timerInfo) {
            $message .= "\nâ° Operating Hours:";
            $message .= "\n   Morning: " . $timerInfo['morning'];
            $message .= "\n   Evening: " . $timerInfo['evening'];
        }

        $message .= "\nðŸ“… Date: " . $now->format('d M Y');

        $this->telegram->sendMessage($parent->telegram_chat_id, $message);
    }

    // ============================================
    // TIMER SETTINGS
    // ============================================

    public function getTimerSettings()
    {
        try {
            $settings = TimerSetting::all();

            if ($settings->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No timer settings found'
                ]);
            }

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->day_name] = [
                    'morning' => [
                        'start' => date('H:i', strtotime($setting->morning_start)),
                        'end' => date('H:i', strtotime($setting->morning_end))
                    ],
                    'evening' => [
                        'start' => date('H:i', strtotime($setting->evening_start)),
                        'end' => date('H:i', strtotime($setting->evening_end))
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('getTimerSettings Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveTimerSettings(Request $request)
    {
        try {
            $data = $request->all();

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data received'
                ], 400);
            }

            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $saved = 0;

            foreach ($days as $day) {
                if (isset($data[$day])) {
                    $dayData = $data[$day];

                    if (!isset($dayData['morning']['start']) || !isset($dayData['morning']['end']) ||
                        !isset($dayData['evening']['start']) || !isset($dayData['evening']['end'])) {
                        continue;
                    }

                    TimerSetting::updateOrCreate(
                        ['day_name' => $day],
                        [
                            'morning_start' => $dayData['morning']['start'] . ':00',
                            'morning_end' => $dayData['morning']['end'] . ':00',
                            'evening_start' => $dayData['evening']['start'] . ':00',
                            'evening_end' => $dayData['evening']['end'] . ':00',
                            'is_active' => 1
                        ]
                    );
                    $saved++;
                }
            }

            if ($saved > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "âœ… Timer settings saved for {$saved} days!"
                ]);
            }

            if (isset($data['day_name'])) {
                $timer = TimerSetting::where('day_name', $data['day_name'])->first();

                $timerData = [
                    'morning_start' => ($data['morning_start'] ?? '07:00') . ':00',
                    'morning_end' => ($data['morning_end'] ?? '07:30') . ':00',
                    'evening_start' => ($data['evening_start'] ?? '17:00') . ':00',
                    'evening_end' => ($data['evening_end'] ?? '17:30') . ':00',
                    'is_active' => 1
                ];

                if ($timer) {
                    $timer->update($timerData);
                } else {
                    TimerSetting::create(array_merge($timerData, [
                        'day_name' => $data['day_name']
                    ]));
                }

                return response()->json([
                    'success' => true,
                    'message' => "âœ… Timer saved for {$data['day_name']}!"
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No valid data to save'
            ], 400);

        } catch (\Exception $e) {
            Log::error('saveTimerSettings Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetTimerSettings()
    {
        try {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            foreach ($days as $day) {
                TimerSetting::where('day_name', $day)->delete();
            }

            foreach ($days as $day) {
                TimerSetting::create([
                    'day_name' => $day,
                    'morning_start' => '07:00:00',
                    'morning_end' => '07:30:00',
                    'evening_start' => '17:00:00',
                    'evening_end' => '17:30:00',
                    'is_active' => 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'âœ… All timers reset to default!'
            ]);

        } catch (\Exception $e) {
            Log::error('resetTimerSettings Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCalendarData(Request $request)
    {
        try {
            // Support both FullCalendar's start/end params and legacy month/year
            $start = $request->input('start');
            $end = $request->input('end');

            if ($start && $end) {
                // Fix PHP converting + to space in query params
                $start = str_replace(' ', '+', $start);
                $end = str_replace(' ', '+', $end);
                $startDate = Carbon::parse($start)->startOfDay();
                $endDate = Carbon::parse($end)->endOfDay();
            } else {
                $month = $request->input('month', Carbon::now()->month);
                $year = $request->input('year', Carbon::now()->year);
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            }

            $query = Attendance::with(['child', 'child.classroom'])
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

            // Filter by classroom if provided
            if ($classroomId = $request->input('classroom_id')) {
                $query->whereHas('child', fn($q) => $q->where('classroom_id', $classroomId));
            }

            $attendances = $query->get()->map(function ($attendance) {
                $status = $attendance->status;
                if ($attendance->checkout_time && $attendance->checkin_time) {
                    $status = 'checkout';
                } elseif ($attendance->checkin_time && !$attendance->checkout_time) {
                    $status = $attendance->status ?? 'present';
                }

                $color = '#43a047'; // green
                if (in_array($status, ['late', 'late_checkout'])) $color = '#e53935';
                elseif ($status === 'checkout') $color = '#1e88e5';
                elseif ($status === 'absent') $color = '#fb8c00';

                $ci = $attendance->checkin_time ? Carbon::parse($attendance->checkin_time)->format('h:i A') : null;
                $co = $attendance->checkout_time ? Carbon::parse($attendance->checkout_time)->format('h:i A') : null;

                return [
                    'id' => $attendance->id,
                    'title' => $attendance->child->name ?? 'Child',
                    'start' => Carbon::parse($attendance->date)->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'child_name' => $attendance->child->name ?? 'Child',
                        'classroom' => $attendance->child->classroom->name ?? null,
                        'status' => $status,
                        'checkin_time' => $ci,
                        'checkout_time' => $co,
                        'is_late' => in_array($attendance->status, ['late', 'late_checkout']),
                        'color' => $color,
                    ],
                ];
            });

            return response()->json($attendances->values());
        } catch (\Exception $e) {
            Log::error('getCalendarData Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============================================
    // LEGACY FUNCTIONS
    // ============================================

    public function showChildProfile($childId)
    {
        $child = Child::with(['parent', 'classroom', 'attendances'])->findOrFail($childId);
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $todayAttendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        $hasFeeWarning = false;
        $feeMessage = '';

        return view('kiosk.child-profile', compact('child', 'todayAttendance', 'hasFeeWarning', 'feeMessage'));
    }

    public function show($qrCode)
    {
        $child = Child::where('qr_code', $qrCode)->first();
        if (!$child) {
            abort(404, 'QR Code tidak dijumpai');
        }
        return redirect()->route('kiosk.checkin.page', $child->id);
    }

    public function confirmCheckin(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'parent_id' => 'required|exists:users,id'
        ]);

        $child = Child::find($request->child_id);
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();

        $attendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            Attendance::create([
                'child_id' => $child->id,
                'parent_id' => $request->parent_id,
                'date' => $today,
                'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'status' => 'present',
                'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                'is_verified' => true
            ]);
        } else {
            $attendance->update([
                'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'status' => 'present',
                'drop_off_by' => 'Parent ID: ' . $request->parent_id,
            ]);
        }

        return redirect()->route('kiosk.index')
            ->with('success', 'Check-in berjaya!');
    }

    public function confirmCheckout(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'parent_id' => 'required|exists:users,id'
        ]);

        $child = Child::find($request->child_id);
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();

        $attendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            $attendance->update([
                'checkout_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'status' => 'checkout',
                'pickup_by' => 'Parent ID: ' . $request->parent_id,
            ]);
        }

        return redirect()->route('kiosk.index')
            ->with('success', 'Check-out berjaya!');
    }

    public function processQR(Request $request)
    {
        $qrData = $request->qr_code;
        $child = Child::where('qr_code', $qrData)->first();

        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak sah!'
            ]);
        }

        return response()->json([
            'success' => true,
            'child' => $child,
            'redirect' => route('scan.qr.result', $qrData)
        ]);
    }

    public function confirmQR(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'action' => 'required|in:checkin,checkout'
        ]);

        $child = Child::find($request->child_id);
        /** @var User|null $user */
        $user = Auth::user();
        $parent = $user;

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Parent tidak dijumpai!'
            ]);
        }

        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $attendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        if ($request->action == 'checkin') {
            if (!$attendance) {
                Attendance::create([
                    'child_id' => $child->id,
                    'parent_id' => $parent->id,
                    'date' => $today,
                    'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                    'status' => 'present',
                    'drop_off_by' => 'Parent ID: ' . $parent->id,
                    'is_verified' => true
                ]);
            }
        } else {
            if ($attendance) {
                $attendance->update([
                    'checkout_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                    'status' => 'checkout',
                    'pickup_by' => 'Parent ID: ' . $parent->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated!',
            'redirect' => route('parent.dashboard')
        ]);
    }

    public function handleKioskScan(Request $request)
    {
        try {
            $qrData = $request->input('qr_data');
            $parentId = $request->input('parent_id');

            if ($qrData) {
                $child = Child::where('qr_code', $qrData)->first();
                if ($child) {
                    $parentId = $child->parent?->id;
                }
            }

            if (!$parentId) {
                $firstParent = \App\Models\User::where('role', 'parent1')->first();
                if ($firstParent) {
                    $parentId = $firstParent->id;
                }
            }

            if (!$parentId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tiada parent dijumpai.',
                    'type' => 'NO_PARENT'
                ]);
            }

            $children = Child::where('parent_id', $parentId)->get();

            if ($children->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tiada rekod anak berdaftar.',
                    'type' => 'NO_CHILD'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sila pilih anak untuk kehadiran',
                'type' => 'MORNING_IN',
                'children' => $children->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'classroom' => $child->classroom->name ?? 'No class',
                        'checked_in' => $this->isChildCheckedInToday($child->id),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('handleKioskScan Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage(),
                'type' => 'SERVER_ERROR'
            ], 500);
        }
    }

    public function confirmAttendance(Request $request)
    {
        try {
            $request->validate([
                'child_ids' => 'required|array',
                'action' => 'required|in:checkin,checkout',
                'parent_id' => 'required|integer'
            ]);

            $parentId = $request->input('parent_id');
            $action = $request->input('action');
            $childIds = $request->input('child_ids');

            foreach ($childIds as $childId) {
                if ($action === 'checkin') {
                    $this->processCheckin($childId, $parentId);
                } else {
                    $this->processCheckout($childId, $parentId);
                }
            }

            $parent = \App\Models\User::find($parentId);
            if ($parent && $parent->telegram_chat_id) {
                $childNames = Child::whereIn('id', $childIds)->pluck('name')->join(', ');
                $message = $action === 'checkin'
                    ? "ðŸ§¸ KidsTrack Check-in Alert\n\nðŸ‘¶ Children: {$childNames}\nâœ… Checked-in at: " . Carbon::now('Asia/Kuala_Lumpur')->format('h:i A') . "\nðŸ“… Date: " . Carbon::now('Asia/Kuala_Lumpur')->format('d M Y')
                    : "ðŸ§¸ KidsTrack Check-out Alert\n\nðŸ‘¶ Children: {$childNames}\nâœ… Checked-out at: " . Carbon::now('Asia/Kuala_Lumpur')->format('h:i A') . "\nðŸ“… Date: " . Carbon::now('Asia/Kuala_Lumpur')->format('d M Y');

                $this->telegram->sendMessage($parent->telegram_chat_id, $message);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance updated successfully for ' . count($childIds) . ' children!'
            ]);

        } catch (\Exception $e) {
            Log::error('confirmAttendance Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendance($childId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $attendance
        ]);
    }

    public function getTodayAttendance()
    {
        $attendance = Attendance::whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
            ->with('child')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $attendance
        ]);
    }

    private function isChildCheckedInToday($childId)
    {
        return Attendance::where('child_id', $childId)
            ->whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
            ->whereNotNull('checkin_time')
            ->exists();
    }

    private function processCheckin($childId, $parentId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'status' => 'checkin',
                'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'drop_off_by' => 'Kiosk',
            ]);
        } else {
            Attendance::create([
                'child_id' => $childId,
                'parent_id' => $parentId,
                'date' => Carbon::now('Asia/Kuala_Lumpur')->toDateString(),
                'status' => 'checkin',
                'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'drop_off_by' => 'Kiosk',
            ]);
        }
    }

    private function processCheckout($childId, $parentId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'status' => 'checkout',
                'checkout_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'pickup_by' => 'Parent ID: ' . $parentId,
            ]);
        }
    }
}
