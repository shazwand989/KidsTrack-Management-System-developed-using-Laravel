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
use Illuminate\Support\Facades\Log;

class QRScanController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    // ============================================
    // 🔥 TIMER FUNCTIONS - GUNA DATABASE timer_settings
    // ============================================

    private function getTimerForToday()
    {
        $today = Carbon::now('Asia/Kuala_Lumpur')->format('l');
        return TimerSetting::where('day_name', $today)->first();
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
            'parent1' => ['badge_class' => 'main-parent', 'badge_text' => '👨‍👩‍👦 Main Parent', 'icon' => '👨‍👩‍👦', 'display_name' => 'Main Parent', 'name_class' => 'main'],
            'parent' => ['badge_class' => 'main-parent', 'badge_text' => '👨‍👩‍👦 Main Parent', 'icon' => '👨‍👩‍👦', 'display_name' => 'Main Parent', 'name_class' => 'main'],
            'parent2' => ['badge_class' => 'second-parent', 'badge_text' => '👫 Second Parent', 'icon' => '👫', 'display_name' => 'Second Parent', 'name_class' => 'second'],
            'second_parent' => ['badge_class' => 'second-parent', 'badge_text' => '👫 Second Parent', 'icon' => '👫', 'display_name' => 'Second Parent', 'name_class' => 'second'],
            'guardian' => ['badge_class' => 'guardian', 'badge_text' => '🛡️ Guardian', 'icon' => '🛡️', 'display_name' => 'Guardian', 'name_class' => 'guardian'],
            'admin' => ['badge_class' => 'admin', 'badge_text' => '👑 Admin', 'icon' => '👑', 'display_name' => 'Admin', 'name_class' => 'admin'],
            'teacher' => ['badge_class' => 'teacher', 'badge_text' => '👨‍🏫 Teacher', 'icon' => '👨‍🏫', 'display_name' => 'Teacher', 'name_class' => 'teacher'],
        ];
        
        return $roleMap[$role] ?? ['badge_class' => 'parent', 'badge_text' => '👤 User', 'icon' => '👤', 'display_name' => 'User', 'name_class' => ''];
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
                    'message' => '❌ QR Code tidak sah!'
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
            
            $user = auth()->user();
            
            if ($user) {
                $hasAccess = false;
                
                if ($user->role === 'second_parent' || $user->role === 'parent2') {
                    $hasAccess = true;
                }
                
                if (!$hasAccess) {
                    $parent = ParentModel::where('user_id', $user->id)->first();
                    if ($parent && $child->parent_id == $parent->id) {
                        $hasAccess = true;
                    }
                }
                
                if (!$hasAccess) {
                    $secondParent = SecondParent::where('user_id', $user->id)->first();
                    if ($secondParent) {
                        $childSecondParent = Child::where('id', $child->id)
                            ->where('second_parent_id', $secondParent->parent_id)
                            ->first();
                        if ($childSecondParent) {
                            $hasAccess = true;
                        }
                    }
                }
                
                if (!$hasAccess && $user->role === 'guardian') {
                    $guardian = Guardian::where('user_id', $user->id)->first();
                    if ($guardian && $child->guardian_id == $guardian->id) {
                        $hasAccess = true;
                    }
                }
                
                if (!$hasAccess && in_array($user->role, ['admin', 'teacher'])) {
                    $hasAccess = true;
                }
                
                if (!$hasAccess) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Anda tidak mempunyai akses ke anak ini!'
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
    // STEP 2: CONFIRM CHILD - DENGAN CHECK-IN!
    // ============================================
    public function confirmChild($childId)
    {
        try {
            $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
            $user = auth()->user();
            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $now = Carbon::now('Asia/Kuala_Lumpur');
            
            // 🔥 Cari parent ID
            $parentId = null;
            if ($user) {
                $parent = ParentModel::where('user_id', $user->id)->first();
                if ($parent) {
                    $parentId = $parent->id;
                } else {
                    $secondParent = SecondParent::where('user_id', $user->id)->first();
                    if ($secondParent) {
                        $parentId = $secondParent->id;
                    }
                }
            }
            
            if (!$parentId) {
                $firstParent = ParentModel::first();
                $parentId = $firstParent ? $firstParent->id : 1;
            }
            
            // 🔥🔥🔥 CHECK ATTENDANCE - JIKA BELUM CHECK-IN, BUAT CHECK-IN 🔥🔥🔥
            $existing = Attendance::where('child_id', $child->id)
                ->whereDate('date', $today)
                ->first();
            
            $hasCheckin = $existing && $existing->checkin_time;
            $hasCheckout = $existing && $existing->checkout_time;
            
            // 🔥 AUTO CHECK-IN jika belum check-in
            if (!$hasCheckin && !$hasCheckout) {
                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => '✅ Check-in via Confirm Child',
                        'drop_off_by' => 'Parent ID: ' . $parentId,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $child->id,
                        'parent_id' => $parentId,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => 'present',
                        'status_note' => '✅ Check-in via Confirm Child',
                        'drop_off_by' => 'Parent ID: ' . $parentId,
                        'is_verified' => true
                    ]);
                }
            }
            
            // 🔥 Redirect ke add-another
            return redirect()->route('kiosk.add.another', $childId);
            
        } catch (\Exception $e) {
            Log::error('confirmChild Error: ' . $e->getMessage());
            return redirect()->route('kiosk.add.another', $childId)
                ->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }

    // ============================================
    // STEP 3: ADD ANOTHER CHILD - FULLY FIXED!
    // 🔥🔥🔥 GUNA TIMER SETTING UNTUK CAN CHECKOUT! 🔥🔥🔥
    // ============================================
    public function showAddAnother($childId)
    {
        $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
        $user = auth()->user();
        $parent = null;
        $allChildren = collect();
        $otherChildren = collect();
        $parentIdForView = 0;
        $allCheckedInData = [];
        $childCheckedIn = false;
        $childCheckedOut = false;
        
        $now = Carbon::now('Asia/Kuala_Lumpur');
        $userRole = $user ? $user->role : 'unknown';
        $today = $now->toDateString();
        
        // 🔥🔥🔥 AMBIL TIMER SETTING UNTUK CAN CHECKOUT 🔥🔥🔥
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
            
            // 🔥🔥🔥 BOLEH CHECKOUT JIKA DAH MELEPASI EVENING START! 🔥🔥🔥
            // (Dalam slot = On Time, Lepas slot = Late Checkout — tetap boleh!)
            if ($currentTimeInt >= $eveningStartInt) {
                $canCheckout = true;
                $isCheckoutMode = true;
            }
        }
        
        // 🔥 CHECK ATTENDANCE FOR CURRENT CHILD
        $childAttendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();
        
        $childCheckedIn = $childAttendance && $childAttendance->checkin_time ? true : false;
        $childCheckedOut = $childAttendance && $childAttendance->checkout_time ? true : false;
        
        // 🔥 GET CHILDREN BASED ON ROLE
        if ($user) {
            // MAIN PARENT
            if (in_array($user->role, ['parent', 'parent1', 'main_parent'])) {
                $parent = ParentModel::where('user_id', $user->id)->first();
                if ($parent) {
                    $parentIdForView = $parent->id;
                    $allChildren = Child::where(function($query) use ($parent) {
                        $query->where('parent_id', $parent->id)
                              ->orWhere('second_parent_id', $parent->id);
                    })->where('is_active', true)->get();
                }
            }
            
            // SECOND PARENT
            if (in_array($user->role, ['second_parent', 'parent2'])) {
                $secondParent = SecondParent::where('user_id', $user->id)->first();
                if ($secondParent) {
                    $parentIdForView = $secondParent->id;
                    $mainParent = ParentModel::find($secondParent->parent_id);
                    if ($mainParent) {
                        $allChildren = Child::where(function($query) use ($mainParent) {
                            $query->where('parent_id', $mainParent->id)
                                  ->orWhere('second_parent_id', $mainParent->id);
                        })->where('is_active', true)->get();
                    }
                }
            }
            
            // GUARDIAN
            if ($user->role === 'guardian') {
                $guardian = Guardian::where('user_id', $user->id)->first();
                if ($guardian) {
                    $parentIdForView = $guardian->id;
                    $allChildren = Child::where('guardian_id', $guardian->id)
                        ->where('is_active', true)->get();
                }
            }
            
            // ADMIN / TEACHER
            if (in_array($user->role, ['admin', 'teacher'])) {
                $parentIdForView = $user->id;
                $allChildren = Child::where('is_active', true)->get();
            }
        }
        
        // 🔥 INCLUDE CURRENT CHILD IN ALL CHILDREN
        if (!$allChildren->contains('id', $child->id)) {
            $allChildren->push($child);
        }
        
        // 🔥 FILTER OTHER CHILDREN (excluding current)
        $otherChildren = $allChildren->filter(function($c) use ($childId) {
            return $c->id != $childId;
        })->map(function($c) use ($today) {
            $attendance = Attendance::where('child_id', $c->id)
                ->whereDate('date', $today)
                ->first();
            
            return (object) [
                'id' => $c->id,
                'name' => $c->name,
                'classroom' => $c->classroom,
                'checked_in_today' => $attendance && $attendance->checkin_time ? true : false,
                'checked_out_today' => $attendance && $attendance->checkout_time ? true : false
            ];
        });
        
        // 🔥🔥🔥 BUILD CHECKED-IN DATA (INCLUDING CURRENT CHILD) 🔥🔥🔥
        // Current child
        if ($childCheckedIn && !$childCheckedOut) {
            $allCheckedInData[] = [
                'name' => $child->name,
                'classroom' => $child->classroom->name ?? '-',
                'time' => $childAttendance ? Carbon::parse($childAttendance->checkin_time)->format('h:i A') : '',
                'initial' => strtoupper(substr($child->name, 0, 1)),
                'is_current' => true
            ];
        }
        
        // Other children
        foreach ($otherChildren as $otherChild) {
            if ($otherChild->checked_in_today && !$otherChild->checked_out_today) {
                $att = Attendance::where('child_id', $otherChild->id)
                    ->whereDate('date', $today)
                    ->first();
                $allCheckedInData[] = [
                    'name' => $otherChild->name,
                    'classroom' => $otherChild->classroom->name ?? '-',
                    'time' => $att ? Carbon::parse($att->checkin_time)->format('h:i A') : '',
                    'initial' => strtoupper(substr($otherChild->name, 0, 1)),
                    'is_current' => false
                ];
            }
        }
        
        // 🔥 SORT: Current child first
        usort($allCheckedInData, function($a, $b) {
            if ($a['is_current'] && !$b['is_current']) return -1;
            if (!$a['is_current'] && $b['is_current']) return 1;
            return strcmp($a['name'], $b['name']);
        });
        
        $roleData = $this->getRoleData($userRole);
        
        // 🔥🔥🔥 PASS ALL DATA TO VIEW 🔥🔥🔥
        return view('kiosk.add-another', compact(
            'child', 'parent', 'allChildren', 'otherChildren', 
            'canCheckout', 'userRole', 'roleData', 'parentIdForView',
            'allCheckedInData', 'childCheckedIn', 'childCheckedOut',
            'isCheckoutMode', 'checkoutStartTime', 'checkoutEndTime'
        ));
    }

    // ============================================
    // HELPER: GET ROLE DATA
    // ============================================
    private function getRoleData($role)
    {
        $roleMap = [
            'parent1' => [
                'class' => 'main-parent', 
                'badge_class' => 'main-parent', 
                'badge_text' => '👨‍👩‍👦 Main Parent', 
                'icon' => '👨‍👩‍👦', 
                'display_name' => 'Main Parent',
                'name_class' => 'main',
                'border_class' => 'main-parent-border',
                'avatar_class' => 'main-parent-avatar',
                'tag_class' => 'main-parent-tag'
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
                'tag_class' => 'main-parent-tag'
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
                'tag_class' => 'second-parent-tag'
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
                'tag_class' => 'second-parent-tag'
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
                'tag_class' => 'guardian-tag'
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
                'tag_class' => 'admin-tag'
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
                'tag_class' => 'admin-tag'
            ],
        ];
        
        return $roleMap[$role] ?? $roleMap['parent1'];
    }

    // ============================================
    // STEP 4: SHOW CHECKIN PAGE - DENGAN TIMER SETTING
    // ============================================
    public function showCheckinPage($childId)
    {
        $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
        $user = auth()->user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
        if (!$parent) {
            $parent = ParentModel::first();
        }
        
        $now = Carbon::now('Asia/Kuala_Lumpur');
        $hour = (int)$now->format('H');
        $currentTime = $now->format('h:i A');
        
        // 🔥🔥🔥 AMBIL TIMER SETTING DARI DATABASE 🔥🔥🔥
        $timerSetting = TimerSetting::where('day_name', $now->format('l'))->first();
        
        $today = $now->toDateString();
        $todayAttendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();
        
        $hasCheckin = $todayAttendance && $todayAttendance->checkin_time;
        $hasCheckout = $todayAttendance && $todayAttendance->checkout_time;
        
        $slot = $this->getCurrentSlot();
        $isLate = $slot && $slot['slot'] === 'morning' ? $this->isLateForCheckin() : false;
        
        // 🔥🔥🔥 TENTUKAN CHECKOUT - GUNA TIMER DARI DATABASE 🔥🔥🔥
        // 🔥 BOLEH CHECKOUT JIKA DAH MELEPASI EVENING START!
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
            $parentCheck = ParentModel::where('user_id', $user->id)->first();
            if ($parentCheck) {
                $parentName = $parentCheck->name ?? 'Parent';
                if ($child->parent_id == $parentCheck->id) {
                    $userRole = 'main_parent';
                    $isMainParent = true;
                }
            }
            
            if ($user->role === 'second_parent' || $user->role === 'parent2') {
                $userRole = 'second_parent';
                $isSecondParent = true;
                $parentName = $user->name ?? 'Second Parent';
                $secondParent = SecondParent::where('user_id', $user->id)->first();
                if ($secondParent) {
                    $parentName = $secondParent->name ?? 'Second Parent';
                }
            }
            
            if ($user->role === 'guardian') {
                $guardian = Guardian::where('user_id', $user->id)->first();
                if ($guardian && $child->guardian_id == $guardian->id) {
                    $isGuardian = true;
                    $userRole = 'guardian';
                    $parentName = $guardian->name;
                }
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
            $parent = ParentModel::where('user_id', $user->id)->first();
            if ($parent) {
                $allChildren = Child::where(function($query) use ($parent) {
                    $query->where('parent_id', $parent->id)
                          ->orWhere('second_parent_id', $parent->id);
                })->where('is_active', true)->get();
            } else {
                $secondParent = SecondParent::where('user_id', $user->id)->first();
                if ($secondParent) {
                    $mainParent = ParentModel::find($secondParent->parent_id);
                    if ($mainParent) {
                        $allChildren = Child::where(function($query) use ($mainParent) {
                            $query->where('parent_id', $mainParent->id)
                                  ->orWhere('second_parent_id', $mainParent->id);
                        })->where('is_active', true)->get();
                    }
                }
                
                if ($user->role === 'guardian') {
                    $guardian = Guardian::where('user_id', $user->id)->first();
                    if ($guardian) {
                        $allChildren = Child::where('guardian_id', $guardian->id)
                            ->where('is_active', true)->get();
                    }
                }
            }
            
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
    // STEP 5: SUBMIT ATTENDANCE - CHECK-IN BOLEH BILA-BILA!
    // 🔥🔥🔥 CHECKOUT JUGA BOLEH BILA-BILA (LATE CHECKOUT JIKA LUAR SLOT) 🔥🔥🔥
    // ============================================
    public function submitAttendance(Request $request)
    {
        try {
            $request->validate([
                'child_id' => 'required|exists:children,id',
                'parent_id' => 'required|exists:parents,id',
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
                // 🔥 CHECK: Already checked in today
                $existing = Attendance::where('child_id', $child->id)
                    ->whereDate('date', $today)
                    ->first();
                    
                if ($existing && $existing->checkin_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anak ini sudah check-in hari ini!'
                    ]);
                }
                
                // 🔥🔥🔥 CHECK-IN BOLEH BILA-BILA MASA! TAK BLOCK! 🔥🔥🔥
                $isLate = $this->isLateForCheckin();
                $withinGrace = $this->isWithinGracePeriod('checkin');
                
                if ($isLate) {
                    $request->validate([
                        'late_reason' => 'required|string|max:255'
                    ]);
                }
                
                $status = 'present';
                $statusNote = '✅ Check-in berjaya';
                
                if ($isLate && $withinGrace) {
                    $status = 'late';
                    $statusNote = '⏰ Late check-in (within grace period)';
                } else if ($isLate && !$withinGrace) {
                    $status = 'late';
                    $statusNote = '⏰ Late check-in (past grace period)';
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
                    'message' => $isLate ? '⏰ Check-in berjaya! (Late)' : '✅ Check-in berjaya! (On Time)',
                    'child_name' => $child->name,
                    'child_classroom' => $child->classroom->name ?? 'Tiada kelas',
                    'checkin_time' => $now->format('h:i A'),
                    'is_late' => $isLate,
                    'slot' => $slot,
                    'timer_info' => $timerInfo,
                    'status_note' => $statusNote
                ]);
                
            } else {
                // 🔥🔥🔥 CHECKOUT - BOLEH BILA-BILA! 🔥🔥🔥
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
                
                // 🔥🔥🔥 TAKDE RESTRICTION! CHECKOUT BOLEH BILA-BILA! 🔥🔥🔥
                // TAPI kita detect sama ada late checkout atau tidak
                $timer = $this->getTimerForToday();
                $isLateCheckout = false;
                $lateCheckoutMessage = '✅ Check-out berjaya (On Time)';
                
                if ($timer) {
                    $currentTimeInt = (int) $now->format('Hi');
                    $eveningStartInt = (int) str_replace(':', '', $timer->evening_start);
                    $eveningEndInt = (int) str_replace(':', '', $timer->evening_end);
                    
                    // 🔥 Kalau luar evening slot → Late Checkout (still boleh!)
                    if (!($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt)) {
                        $isLateCheckout = true;
                        $lateCheckoutMessage = '⏰ Check-out berjaya (Late Checkout)';
                    }
                }
                
                $status = 'checkout';
                $statusNote = '✅ Check-out berjaya';
                
                if ($isLateCheckout) {
                    $status = 'late_checkout';
                    $statusNote = '⏰ Late Checkout';
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
                    'message' => $isLateCheckout ? '⏰ Check-out berjaya! (Late)' : '✅ Check-out berjaya! (On Time)',
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
    // STEP 6: CHECK-IN ALL CHILDREN
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
            
            $isLate = $this->isLateForCheckin();
            $withinGrace = $this->isWithinGracePeriod('checkin');
            
            $status = 'present';
            $statusNote = '✅ Check-in berjaya';
            
            if ($isLate && $withinGrace) {
                $status = 'late';
                $statusNote = '⏰ Late check-in (within grace period)';
            } else if ($isLate && !$withinGrace) {
                $status = 'late';
                $statusNote = '⏰ Late check-in (past grace period)';
            }
            
            $checkedCount = 0;
            $lateCount = 0;
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
                        'time' => date('h:i A', strtotime($existing->checkin_time))
                    ];
                    $alreadyCount++;
                    continue;
                }
                
                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $status,
                        'status_note' => $statusNote,
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $childId,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $status,
                        'status_note' => $statusNote,
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                }
                
                $this->sendTelegramNotification($child, $request->parent_id, 'checkin', $isLate, null);
                
                if ($isLate) {
                    $lateCount++;
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'late',
                        'time' => $now->format('h:i A')
                    ];
                } else {
                    $checkedCount++;
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'checked_in',
                        'time' => $now->format('h:i A')
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $isLate ? '⏰ Semua anak berjaya check-in! (Late)' : '✅ Semua anak berjaya check-in! (On Time)',
                'results' => $results,
                'checked_count' => $checkedCount,
                'late_count' => $lateCount,
                'already_count' => $alreadyCount,
                'status_note' => $statusNote
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
    // STEP 7: CHECKOUT ALL CHILDREN
    // 🔥🔥🔥 BOLEH BILA-BILA! (LATE CHECKOUT JIKA LUAR SLOT) 🔥🔥🔥
    // ============================================
    public function checkoutAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|exists:parents,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);
            
            $parent = ParentModel::find($request->parent_id);
            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            
            // 🔥🔥🔥 TAKDE RESTRICTION! CHECKOUT BOLEH BILA-BILA! 🔥🔥🔥
            $timer = $this->getTimerForToday();
            $isLateCheckout = false;
            
            if ($timer) {
                $currentTimeInt = (int) $now->format('Hi');
                $eveningStartInt = (int) str_replace(':', '', $timer->evening_start);
                $eveningEndInt = (int) str_replace(':', '', $timer->evening_end);
                
                // 🔥 Kalau luar evening slot → Late Checkout (still boleh!)
                if (!($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt)) {
                    $isLateCheckout = true;
                }
            }
            
            $status = 'checkout';
            $statusNote = '✅ Check-out berjaya';
            
            if ($isLateCheckout) {
                $status = 'late_checkout';
                $statusNote = '⏰ Late Checkout';
            }
            
            foreach ($request->child_ids as $childId) {
                $child = Child::find($childId);
                
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
                        'time' => date('h:i A', strtotime($attendance->checkout_time))
                    ];
                    continue;
                }
                
                $attendance->update([
                    'checkout_time' => $now->format('H:i:s'),
                    'status' => $status,
                    'status_note' => $statusNote,
                    'pickup_by' => 'Parent ID: ' . $request->parent_id,
                ]);
                
                $this->sendTelegramNotification($child, $request->parent_id, 'checkout', $isLateCheckout);
                
                $results[] = [
                    'name' => $child->name,
                    'status' => 'checkout',
                    'time' => $now->format('h:i A')
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => $isLateCheckout ? '⏰ Semua anak berjaya check-out! (Late)' : '✅ Semua anak berjaya check-out! (On Time)',
                'results' => $results,
                'checkout_count' => collect($results)->where('status', 'checkout')->count(),
                'already_count' => collect($results)->where('status', 'already_checked')->count(),
                'status_note' => $statusNote
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
    // SEND TELEGRAM NOTIFICATION
    // ============================================
    private function sendTelegramNotification($child, $parentId, $action, $isLate = false, $lateReason = null)
    {
        $parent = ParentModel::find($parentId);
        if (!$parent || !$parent->telegram_notification || !$parent->telegram_id) {
            return;
        }
        
        $now = Carbon::now('Asia/Kuala_Lumpur');
        $slot = $this->getCurrentSlot();
        $slotLabel = $slot ? $slot['label'] : 'Unknown';
        $timerInfo = $this->getTimerSlotInfo();
        
        $message = "🧸 KidsTrack Alert\n\n";
        $message .= "👶 Child: {$child->name}\n";
        $message .= "🏫 Class: " . ($child->classroom->name ?? 'No class') . "\n";
        
        if ($action == 'checkin') {
            $message .= "✅ Checked-in at: " . $now->format('h:i A') . "\n";
            $message .= "📊 Status: " . ($isLate ? '⏰ Late' : '✅ On Time') . "\n";
            $message .= "⏱️ Slot: " . $slotLabel;
            if ($isLate && $lateReason) {
                $message .= "\n📝 Reason: " . $lateReason;
            }
        } else {
            $message .= "👋 Checked-out at: " . $now->format('h:i A') . "\n";
            $message .= "📊 Status: " . ($isLate ? '⏰ Late Checkout' : '✅ On Time') . "\n";
            $message .= "⏱️ Slot: " . $slotLabel;
        }
        
        if ($timerInfo) {
            $message .= "\n⏰ Operating Hours:";
            $message .= "\n   Morning: " . $timerInfo['morning'];
            $message .= "\n   Evening: " . $timerInfo['evening'];
        }
        
        $message .= "\n📅 Date: " . $now->format('d M Y');
        
        $this->telegram->sendMessage($parent->telegram_id, $message);
    }

    // ============================================
    // 🔥🔥🔥 CALENDAR FUNCTIONS - SAVE FORMAT BETUL! 🔥🔥🔥
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
                    'message' => "✅ Timer settings saved for {$saved} days!"
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
                    'message' => "✅ Timer saved for {$data['day_name']}!"
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
                'message' => '✅ All timers reset to default!'
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
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);
            
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->map(function($attendance) {
                    $status = $attendance->status;
                    if ($attendance->checkout_time && $attendance->checkin_time) {
                        $status = 'checkout';
                    } elseif ($attendance->checkin_time && !$attendance->checkout_time) {
                        $status = $attendance->status ?? 'present';
                    }
                    
                    return [
                        'id' => $attendance->id,
                        'child_id' => $attendance->child_id,
                        'child' => $attendance->child ? [
                            'id' => $attendance->child->id,
                            'name' => $attendance->child->name,
                            'classroom_id' => $attendance->child->classroom_id,
                            'classroom' => $attendance->child->classroom ? [
                                'id' => $attendance->child->classroom->id,
                                'name' => $attendance->child->classroom->name
                            ] : null
                        ] : null,
                        'date' => $attendance->date,
                        'checkin_time' => $attendance->checkin_time,
                        'checkout_time' => $attendance->checkout_time,
                        'status' => $status,
                        'status_note' => $attendance->status_note,
                        'is_late' => in_array($attendance->status, ['late', 'late_checkout']),
                        'late_reason' => $attendance->late_reason,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $attendances,
                'month' => $month,
                'year' => $year
            ]);
            
        } catch (\Exception $e) {
            Log::error('getCalendarData Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
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
        return view('kiosk.qr-show', compact('child', 'qrCode'));
    }

    public function confirmCheckin(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'parent_id' => 'required|exists:parents,id'
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
            'parent_id' => 'required|exists:parents,id'
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
        $user = auth()->user();
        $parent = ParentModel::where('user_id', $user->id)->first();
        
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
                    $parentId = $child->parent_id;
                }
            }
            
            if (!$parentId) {
                $firstParent = ParentModel::first();
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

            $parent = ParentModel::find($parentId);
            if ($parent && $parent->telegram_notification && $parent->telegram_id) {
                $childNames = Child::whereIn('id', $childIds)->pluck('name')->join(', ');
                $message = $action === 'checkin' 
                    ? "🧸 KidsTrack Check-in Alert\n\n👶 Children: {$childNames}\n✅ Checked-in at: " . Carbon::now('Asia/Kuala_Lumpur')->format('h:i A') . "\n📅 Date: " . Carbon::now('Asia/Kuala_Lumpur')->format('d M Y')
                    : "🧸 KidsTrack Check-out Alert\n\n👶 Children: {$childNames}\n✅ Checked-out at: " . Carbon::now('Asia/Kuala_Lumpur')->format('h:i A') . "\n📅 Date: " . Carbon::now('Asia/Kuala_Lumpur')->format('d M Y');
                
                $this->telegram->sendMessage($parent->telegram_id, $message);
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
                'drop_off_by' => 'Parent ID: ' . $parentId,
            ]);
        } else {
            Attendance::create([
                'child_id' => $childId,
                'parent_id' => $parentId,
                'date' => Carbon::now('Asia/Kuala_Lumpur')->toDateString(),
                'status' => 'checkin',
                'checkin_time' => Carbon::now('Asia/Kuala_Lumpur')->format('H:i:s'),
                'drop_off_by' => 'Parent ID: ' . $parentId,
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