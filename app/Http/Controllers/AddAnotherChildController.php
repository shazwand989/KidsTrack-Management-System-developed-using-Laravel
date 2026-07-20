<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Attendance;
use App\Models\ParentModel;
use App\Models\Guardian;
use App\Models\SecondParent;
use App\Models\TimerSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AddAnotherChildController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function showAddAnother($childId)
    {
        try {
            $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
            $user = Auth::user();
            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();

            // Get role data
            $roleData = $this->getRoleData($user);

            // Get children based on role
            $childrenData = $this->getUserChildren($user, $child);
            $allChildren = $childrenData['all'];
            $parentId = $childrenData['parent_id'];
            $otherChildren = $childrenData['other'];

            // Get attendance for all children
            $childIds = $allChildren->pluck('id')->toArray();
            $attendances = Attendance::whereIn('child_id', $childIds)
                ->whereDate('date', $today)
                ->get()
                ->keyBy('child_id');

            // Check current child
            $childAttendance = $attendances->get($child->id);
            $childCheckedIn = $childAttendance && $childAttendance->checkin_time;
            $childCheckedOut = $childAttendance && $childAttendance->checkout_time;

            // Build checked-in data
            $allCheckedInData = [];
            $checkedInIds = [];

            if ($childCheckedIn && !$childCheckedOut) {
                $allCheckedInData[] = [
                    'name' => $child->name,
                    'classroom' => $child->classroom->name ?? '-',
                    'check_in_time' => $childAttendance ? Carbon::parse($childAttendance->checkin_time)->format('h:i A') : '',
                    'initial' => strtoupper(substr($child->name, 0, 1)),
                    'is_current' => true
                ];
                $checkedInIds[] = $child->id;
            }

            foreach ($otherChildren as $otherChild) {
                $att = $attendances->get($otherChild->id);
                if ($att && $att->checkin_time && !$att->checkout_time) {
                    $allCheckedInData[] = [
                        'name' => $otherChild->name,
                        'classroom' => $otherChild->classroom->name ?? '-',
                        'check_in_time' => Carbon::parse($att->checkin_time)->format('h:i A'),
                        'initial' => strtoupper(substr($otherChild->name, 0, 1)),
                        'is_current' => false
                    ];
                    $checkedInIds[] = $otherChild->id;
                }
            }

            // Build available children
            $availableChildren = [];
            foreach ($otherChildren as $otherChild) {
                $att = $attendances->get($otherChild->id);
                $isCheckedIn = $att && $att->checkin_time;
                $isCheckedOut = $att && $att->checkout_time;

                if (!$isCheckedIn && !$isCheckedOut) {
                    $availableChildren[] = [
                        'id' => $otherChild->id,
                        'name' => $otherChild->name,
                        'classroom' => $otherChild->classroom->name ?? '-',
                        'initial' => strtoupper(substr($otherChild->name, 0, 1)),
                        'is_available' => true
                    ];
                }
            }

            // Checkout logic
            $timerSetting = TimerSetting::where('day_name', $now->format('l'))->first();
            $canCheckout = false;
            $isCheckoutMode = false;
            $checkoutStartTime = '--:--';
            $checkoutEndTime = '--:--';

            if ($timerSetting) {
                $currentTimeInt = (int) $now->format('Hi');
                $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
                $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);
                $checkoutStartTime = date('H:i', strtotime($timerSetting->evening_start));
                $checkoutEndTime = date('H:i', strtotime($timerSetting->evening_end));

                if ($currentTimeInt >= $eveningStartInt) {
                    $canCheckout = true;
                    $isCheckoutMode = true;
                }
            }

            // 🔥🔥🔥 SEMAK SEMUA ANAK SUDAH CHECK-IN? 🔥🔥🔥
            $totalChildren = $allChildren->count();
            $checkedInCount = count($allCheckedInData);
            $allCheckedIn = ($checkedInCount == $totalChildren && $totalChildren > 0);

            // 🔥🔥🔥 CURRENT CHILD DATA 🔥🔥🔥
            $currentChild = $child;
            $currentChildId = $child->id;

            return view('kiosk.add-another', compact(
                'child',
                'currentChild',
                'currentChildId',
                'parentId',
                'allChildren',
                'otherChildren',
                'availableChildren',
                'allCheckedInData',
                'checkedInIds',
                'childCheckedIn',
                'childCheckedOut',
                'canCheckout',
                'isCheckoutMode',
                'checkoutStartTime',
                'checkoutEndTime',
                'allCheckedIn',      // ⭐ TAMBAH
                'totalChildren',     // ⭐ TAMBAH
                'checkedInCount',    // ⭐ TAMBAH
                'roleData'
            ));

        } catch (\Exception $e) {
            Log::error('showAddAnother Error: ' . $e->getMessage());
            return redirect()->route('kiosk.index')
                ->with('error', 'Ralat: ' . $e->getMessage());
        }
    }

    // ============================================
    // HELPER FUNCTIONS
    // ============================================

    private function getRoleData($user)
    {
        $role = $user ? $user->role : 'guest';

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
                'welcome' => 'Welcome, Main Parent 👋'
            ],
            'parent' => [
                'class' => 'main-parent',
                'badge_class' => 'main-parent',
                'badge_text' => '👨‍👩‍👦 Main Parent',
                'icon' => '👨‍👩‍👦',
                'display_name' => 'Main Parent',
                'name_class' => 'main',
                'border_class' => 'main-parent-border',
                'avatar_class' => 'main-parent-avatar',
                'tag_class' => 'main-parent-tag',
                'welcome' => 'Welcome, Main Parent 👋'
            ],
            'parent1' => [
                'class' => 'main-parent',
                'badge_class' => 'main-parent',
                'badge_text' => '👨‍👩‍👦 Main Parent',
                'icon' => '👨‍👩‍👦',
                'display_name' => 'Main Parent',
                'name_class' => 'main',
                'border_class' => 'main-parent-border',
                'avatar_class' => 'main-parent-avatar',
                'tag_class' => 'main-parent-tag',
                'welcome' => 'Welcome, Main Parent 👋'
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
                'welcome' => 'Welcome, Second Parent 👋'
            ],
            'parent2' => [
                'class' => 'second-parent',
                'badge_class' => 'second-parent',
                'badge_text' => '👫 Second Parent',
                'icon' => '👫',
                'display_name' => 'Second Parent',
                'name_class' => 'second',
                'border_class' => 'second-parent-border',
                'avatar_class' => 'second-parent-avatar',
                'tag_class' => 'second-parent-tag',
                'welcome' => 'Welcome, Second Parent 👋'
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
                'welcome' => 'Welcome, Guardian 👋'
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
                'welcome' => 'Welcome, Admin 👋'
            ],
            'teacher' => [
                'class' => 'admin',
                'badge_class' => 'admin',
                'badge_text' => '👨‍🏫 Teacher',
                'icon' => '👨‍🏫',
                'display_name' => 'Teacher',
                'name_class' => 'teacher',
                'border_class' => 'admin-border',
                'avatar_class' => 'admin-avatar',
                'tag_class' => 'admin-tag',
                'welcome' => 'Welcome, Teacher 👋'
            ],
        ];

        $normalized = $this->normalizeRole($role);
        return $roleMap[$normalized] ?? $roleMap['main_parent'];
    }

    private function normalizeRole($role)
    {
        $map = [
            'parent' => 'main_parent',
            'parent1' => 'main_parent',
            'main_parent' => 'main_parent',
            'main-parent' => 'main_parent',
            'parent2' => 'second_parent',
            'second_parent' => 'second_parent',
            'second-parent' => 'second_parent',
            'guardian' => 'guardian',
            'admin' => 'admin',
            'teacher' => 'admin',
        ];

        return $map[$role] ?? 'main_parent';
    }

    private function getUserChildren($user, $currentChild)
    {
        $allChildren = collect();
        $parentId = 0;

        if (!$user) {
            $allChildren->push($currentChild);
            return [
                'all' => $allChildren,
                'other' => collect(),
                'parent_id' => 0
            ];
        }

        $role = $this->normalizeRole($user->role);

        switch ($role) {
            case 'main_parent':
                $parent = ParentModel::where('user_id', $user->id)->first();
                if ($parent) {
                    $parentId = $parent->id;
                    $allChildren = Child::where(function($query) use ($parent) {
                        $query->where('parent_id', $parent->id)
                              ->orWhere('second_parent_id', $parent->id);
                    })->where('is_active', true)->get();
                }
                break;

            case 'second_parent':
                $secondParent = SecondParent::where('user_id', $user->id)->first();
                if ($secondParent) {
                    $parentId = $secondParent->id;
                    $mainParent = ParentModel::find($secondParent->parent_id);
                    if ($mainParent) {
                        $allChildren = Child::where(function($query) use ($mainParent) {
                            $query->where('parent_id', $mainParent->id)
                                  ->orWhere('second_parent_id', $mainParent->id);
                        })->where('is_active', true)->get();
                    }
                }
                break;

            case 'guardian':
                $guardian = Guardian::where('user_id', $user->id)->first();
                if ($guardian) {
                    $parentId = $guardian->id;
                    $allChildren = Child::where('guardian_id', $guardian->id)
                        ->where('is_active', true)->get();
                }
                break;

            case 'admin':
            default:
                $parentId = $user->id;
                $allChildren = Child::where('is_active', true)->get();
                break;
        }

        // Include current child
        if ($currentChild && !$allChildren->contains('id', $currentChild->id)) {
            $allChildren->push($currentChild);
        }

        // Other children (exclude current)
        $otherChildren = $allChildren->filter(function($c) use ($currentChild) {
            return $currentChild ? $c->id != $currentChild->id : true;
        });

        return [
            'all' => $allChildren,
            'other' => $otherChildren,
            'parent_id' => $parentId
        ];
    }

    // ============================================
    // CHECKIN ALL
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

                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => '✅ Check-in via Add Another',
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $childId,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => '✅ Check-in via Add Another',
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                }

                $checkedCount++;
                $results[] = [
                    'name' => $child->name,
                    'status' => 'checked_in',
                    'time' => $now->format('h:i A')
                ];

                $this->sendTelegramNotification($child, $request->parent_id, 'checkin');
            }

            return response()->json([
                'success' => true,
                'message' => '✅ ' . $checkedCount . ' anak berjaya check-in!',
                'results' => $results,
                'checked_count' => $checkedCount,
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
    // CHECKOUT ALL
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

                $attendance->update([
                    'checkout_time' => $now->format('H:i:s'),
                    'status' => 'checkout',
                    'status_note' => '✅ Check-out via Add Another',
                    'pickup_by' => 'Parent ID: ' . $request->parent_id,
                ]);

                $checkoutCount++;
                $results[] = [
                    'name' => $child->name,
                    'status' => 'checkout',
                    'time' => $now->format('h:i A')
                ];

                $this->sendTelegramNotification($child, $request->parent_id, 'checkout');
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

    private function sendTelegramNotification($child, $parentId, $action)
    {
        $parent = ParentModel::find($parentId);
        if (!$parent || !$parent->telegram_notification || !$parent->telegram_id) {
            return;
        }

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $message = "🧸 KidsTrack Alert\n\n";
        $message .= "👶 Child: {$child->name}\n";
        $message .= "🏫 Class: " . ($child->classroom->name ?? 'No class') . "\n";

        if ($action == 'checkin') {
            $message .= "✅ Checked-in at: " . $now->format('h:i A') . "\n";
        } else {
            $message .= "👋 Checked-out at: " . $now->format('h:i A') . "\n";
        }

        $message .= "📅 Date: " . $now->format('d M Y');

        $this->telegram->sendMessage($parent->telegram_id, $message);
    }
}
