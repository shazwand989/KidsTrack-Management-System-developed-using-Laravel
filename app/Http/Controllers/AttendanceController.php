<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\TimerSetting;
use App\Models\Classroom;
use App\Models\SimulationClock;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    // ============================================
    // INDEX - List all attendance
    // ============================================
    public function index()
    {
        $user = auth()->user();
        $attendances = collect();
        $children = collect();

        if (in_array($user->role, ['admin', 'teacher'])) {
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->orderBy('date', 'desc')
                ->orderBy('checkin_time', 'desc')
                ->paginate(20);
            $children = Child::with('classroom')->get();
        } elseif (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', $user->id)->first();
            if ($parent) {
                $children = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->get();
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->orderBy('date', 'desc')
                    ->orderBy('checkin_time', 'desc')
                    ->paginate(20);
            }
        } elseif ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', $user->id)->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $children = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->get();
                    $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                        ->orderBy('date', 'desc')
                        ->orderBy('checkin_time', 'desc')
                        ->paginate(20);
                }
            }
        } elseif ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if ($guardian) {
                $children = Child::where('guardian_id', $guardian->id)->get();
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->orderBy('date', 'desc')
                    ->orderBy('checkin_time', 'desc')
                    ->paginate(20);
            }
        }

        return view('attendance.index', compact('attendances', 'children'));
    }

    // ============================================
    // 🔥 CALENDAR PAGE
    // ============================================
    public function calendar()
    {
        $user = auth()->user();
        $children = collect();
        $attendances = collect();
        $classrooms = collect();
        
        if (in_array($user->role, ['admin', 'teacher'])) {
            $classrooms = Classroom::all();
            $children = Child::with('classroom')->get();
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', $user->id)->first();
            if ($parent) {
                $classrooms = Classroom::all();
                $children = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->get();
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year)
                    ->orderBy('date', 'asc')
                    ->get();
            }
        } elseif ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', $user->id)->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $classrooms = Classroom::all();
                    $children = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->get();
                    $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                        ->whereMonth('date', Carbon::now()->month)
                        ->whereYear('date', Carbon::now()->year)
                        ->orderBy('date', 'asc')
                        ->get();
                }
            }
        } elseif ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if ($guardian) {
                $classrooms = Classroom::all();
                $children = Child::where('guardian_id', $guardian->id)->get();
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year)
                    ->orderBy('date', 'asc')
                    ->get();
            }
        }
        
        return view('attendance.calendar', compact('children', 'attendances', 'classrooms'));
    }

    // ============================================
    // 🔥 GET ATTENDANCE DATA FOR CALENDAR (AJAX)
    // ============================================
    public function getCalendarData(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $user = auth()->user();
        $attendances = collect();
        
        if (in_array($user->role, ['admin', 'teacher'])) {
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', $user->id)->first();
            if ($parent) {
                $childrenIds = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->pluck('id');
                $attendances = Attendance::whereIn('child_id', $childrenIds)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderBy('date', 'asc')
                    ->get();
            }
        } elseif ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', $user->id)->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $childrenIds = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->pluck('id');
                    $attendances = Attendance::whereIn('child_id', $childrenIds)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->orderBy('date', 'asc')
                        ->get();
                }
            }
        } elseif ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if ($guardian) {
                $childrenIds = Child::where('guardian_id', $guardian->id)->pluck('id');
                $attendances = Attendance::whereIn('child_id', $childrenIds)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderBy('date', 'asc')
                    ->get();
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $attendances,
            'month' => $month,
            'year' => $year
        ]);
    }

    // ============================================
    // 🔥🔥🔥 TIMER SETTINGS - BETUL! 🔥🔥🔥
    // ============================================
    
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
            
            // 🔥 LOG UNTUK DEBUG
            Log::info('📥 SAVE TIMER - Data received:', $data);
            
            // 🔥 LOOP SETIAP HARI
            foreach ($days as $day) {
                if (isset($data[$day])) {
                    $dayData = $data[$day];
                    
                    // 🔥 VALIDASI
                    if (!isset($dayData['morning']['start']) || !isset($dayData['morning']['end']) ||
                        !isset($dayData['evening']['start']) || !isset($dayData['evening']['end'])) {
                        Log::warning("⚠️ Invalid data for {$day}: " . json_encode($dayData));
                        continue;
                    }
                    
                    // 🔥 SAVE KE DATABASE
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
                    Log::info("✅ Saved timer for {$day}");
                }
            }
            
            if ($saved > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Timer settings saved for {$saved} days!"
                ]);
            }
            
            // 🔥 TRY ALTERNATIVE FORMAT (day_name)
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
                'message' => 'No valid data to save. Data: ' . json_encode($data)
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('❌ saveTimerSettings Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTimerSettings()
    {
        try {
            $settings = TimerSetting::all();
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
            
            Log::info('📊 GET ALL TIMERS - Count: ' . count($result));
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting timers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTimerForDay($dayName)
    {
        try {
            $timer = TimerSetting::where('day_name', $dayName)->first();
            
            if (!$timer) {
                return response()->json([
                    'success' => false,
                    'message' => 'No timer found for ' . $dayName
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'morning' => [
                        'start' => date('H:i', strtotime($timer->morning_start)),
                        'end' => date('H:i', strtotime($timer->morning_end))
                    ],
                    'evening' => [
                        'start' => date('H:i', strtotime($timer->evening_start)),
                        'end' => date('H:i', strtotime($timer->evening_end))
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting timer for day: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
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
            
            Log::info('🔄 ALL TIMERS RESET');
            
            return response()->json([
                'success' => true,
                'message' => '✅ Semua tetapan masa direset ke default!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error resetting timers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // SHOW - Attendance details
    // ============================================
    public function show($id)
    {
        $attendance = Attendance::with(['child', 'child.classroom'])->findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }

    // ============================================
    // CHILD ATTENDANCE
    // ============================================
    public function childAttendance($childId)
    {
        $child = Child::with('classroom')->findOrFail($childId);
        $attendances = Attendance::where('child_id', $childId)
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        return view('attendance.child', compact('child', 'attendances'));
    }

    // ============================================
    // CHECKIN
    // ============================================
    public function checkin(Request $request)
    {
        try {
            $request->validate([
                'child_id' => 'required|exists:children,id',
                'parent_id' => 'required|exists:parents,id',
            ]);

            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $now = Carbon::now('Asia/Kuala_Lumpur');
            $hour = (int)$now->format('H');
            $minute = (int)$now->format('i');
            
            $existing = Attendance::where('child_id', $request->child_id)
                ->whereDate('date', $today)
                ->first();
                
            if ($existing && $existing->checkin_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini sudah check-in hari ini!'
                ]);
            }
            
            $slot = $this->checkTimerSlot();
            if (!$slot || $slot['type'] !== 'checkin') {
                return response()->json([
                    'success' => false,
                    'message' => '⏰ Check-in hanya dibenarkan dalam waktu Morning slot!'
                ]);
            }
            
            $isLate = false;
            if (($hour == 1 && $minute > 0) || ($hour == 2) || ($hour == 3 && $minute == 0)) {
                $isLate = true;
            }
            
            if ($existing) {
                $existing->update([
                    'checkin_time' => $now->format('H:i:s'),
                    'status' => $isLate ? 'late' : 'present',
                    'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                    'is_verified' => true
                ]);
                $attendance = $existing;
            } else {
                $attendance = Attendance::create([
                    'child_id' => $request->child_id,
                    'parent_id' => $request->parent_id,
                    'date' => $today,
                    'checkin_time' => $now->format('H:i:s'),
                    'status' => $isLate ? 'late' : 'present',
                    'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                    'is_verified' => true
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Check-in berjaya!',
                'data' => $attendance,
                'is_late' => $isLate
            ]);
        } catch (\Exception $e) {
            Log::error('Checkin error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // ✅ CHECK TIMER SLOT - GUNA SIMULATION CLOCK
    // ============================================
    private function checkTimerSlot()
    {
        try {
            $clock = SimulationClock::getClock();
            
            if (!$clock) {
                return null;
            }
            
            $simulationTime = strtotime($clock->simulation_time);
            $hour = date('H', $simulationTime);
            $minute = date('i', $simulationTime);
            $currentTimeInt = (int)($hour . $minute);
            
            $morningStart = (int)str_replace(':', '', $clock->morning_start);
            $morningEnd = (int)str_replace(':', '', $clock->morning_end);
            $eveningStart = (int)str_replace(':', '', $clock->evening_start);
            $eveningEnd = (int)str_replace(':', '', $clock->evening_end);
            
            if ($currentTimeInt >= $morningStart && $currentTimeInt <= $morningEnd) {
                return ['slot' => 'morning', 'type' => 'checkin', 'label' => 'Morning (Check-in)'];
            }
            
            if ($currentTimeInt >= $eveningStart && $currentTimeInt <= $eveningEnd) {
                return ['slot' => 'evening', 'type' => 'checkout', 'label' => 'Evening (Check-out)'];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('checkTimerSlot error: ' . $e->getMessage());
            return null;
        }
    }

    // ============================================
    // CHECKOUT
    // ============================================
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'child_id' => 'required|exists:children,id',
                'parent_id' => 'required|exists:parents,id',
            ]);

            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            $now = Carbon::now('Asia/Kuala_Lumpur');
            
            $slot = $this->checkTimerSlot();
            if (!$slot || $slot['type'] !== 'checkout') {
                return response()->json([
                    'success' => false,
                    'message' => '⏰ Check-out hanya dibenarkan dalam waktu Evening slot!'
                ]);
            }
            
            $attendance = Attendance::where('child_id', $request->child_id)
                ->whereDate('date', $today)
                ->first();
                
            if (!$attendance || !$attendance->checkin_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini belum check-in hari ini!'
                ]);
            }
            
            if ($attendance->checkout_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini sudah check-out hari ini!'
                ]);
            }
            
            $attendance->update([
                'checkout_time' => $now->format('H:i:s'),
                'status' => 'checkout',
                'pickup_by' => 'Parent ID: ' . $request->parent_id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Check-out berjaya!',
                'data' => $attendance,
                'slot' => $slot['label']
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // CHECKIN ALL
    // ============================================
    public function checkinAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|exists:parents,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $slot = $this->checkTimerSlot();
            if (!$slot || $slot['type'] !== 'checkin') {
                return response()->json([
                    'success' => false,
                    'message' => '⏰ Check-in hanya dibenarkan dalam waktu Morning slot!'
                ]);
            }

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $hour = (int)$now->format('H');
            $minute = (int)$now->format('i');
            $results = [];
            
            $isLate = false;
            if (($hour == 1 && $minute > 0) || ($hour == 2) || ($hour == 3 && $minute == 0)) {
                $isLate = true;
            }
            
            foreach ($request->child_ids as $childId) {
                $child = Child::find($childId);
                
                $existing = Attendance::where('child_id', $childId)
                    ->whereDate('date', $today)
                    ->first();
                    
                if ($existing && $existing->checkin_time) {
                    $results[] = [
                        'name' => $child->name,
                        'status' => 'already_checked',
                        'time' => date('h:i A', strtotime($existing->checkin_time))
                    ];
                    continue;
                }
                
                if ($existing) {
                    $existing->update([
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $isLate ? 'late' : 'present',
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $childId,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $isLate ? 'late' : 'present',
                        'drop_off_by' => 'Parent ID: ' . $request->parent_id,
                        'is_verified' => true
                    ]);
                }
                
                $results[] = [
                    'name' => $child->name,
                    'status' => $isLate ? 'late' : 'checked_in',
                    'time' => $now->format('h:i A')
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Semua anak berjaya check-in!',
                'results' => $results,
                'checked_count' => collect($results)->where('status', 'checked_in')->count(),
                'late_count' => collect($results)->where('status', 'late')->count(),
                'already_count' => collect($results)->where('status', 'already_checked')->count()
            ]);
        } catch (\Exception $e) {
            Log::error('checkinAll error: ' . $e->getMessage());
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
                'parent_id' => 'required|exists:parents,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $slot = $this->checkTimerSlot();
            if (!$slot || $slot['type'] !== 'checkout') {
                return response()->json([
                    'success' => false,
                    'message' => '⏰ Check-out hanya dibenarkan dalam waktu Evening slot!'
                ]);
            }

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            
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
                    'status' => 'checkout',
                    'pickup_by' => 'Parent ID: ' . $request->parent_id,
                ]);
                
                $results[] = [
                    'name' => $child->name,
                    'status' => 'checkout',
                    'time' => $now->format('h:i A')
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Semua anak berjaya check-out!',
                'results' => $results,
                'checkout_count' => collect($results)->where('status', 'checkout')->count(),
                'already_count' => collect($results)->where('status', 'already_checked')->count(),
                'slot' => $slot['label']
            ]);
        } catch (\Exception $e) {
            Log::error('checkoutAll error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // GET TODAY ATTENDANCE
    // ============================================
    public function getTodayAttendance(Request $request)
    {
        try {
            $childId = $request->child_id;
            $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
            
            $attendance = Attendance::where('child_id', $childId)
                ->whereDate('date', $today)
                ->first();
                
            return response()->json([
                'success' => true,
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            Log::error('getTodayAttendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // GET CHILD ATTENDANCE
    // ============================================
    public function getChildAttendance($childId)
    {
        try {
            $attendances = Attendance::where('child_id', $childId)
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $attendances
            ]);
        } catch (\Exception $e) {
            Log::error('getChildAttendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ralat: ' . $e->getMessage()
            ], 500);
        }
    }
}