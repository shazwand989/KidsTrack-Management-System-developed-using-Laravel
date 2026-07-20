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
use App\Services\TelegramService;
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
        $classrooms = collect(); // 🔥 TAMBAH INI

        if (in_array($user->role, ['admin', 'teacher'])) {
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->orderBy('date', 'desc')
                ->orderBy('checkin_time', 'desc')
                ->paginate(20);
            $children = Child::with('classroom')->get();
            $classrooms = Classroom::all(); // 🔥 TAMBAH INI
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
                $classrooms = Classroom::all(); // 🔥 TAMBAH INI
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
                    $classrooms = Classroom::all(); // 🔥 TAMBAH INI
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
                $classrooms = Classroom::all(); // 🔥 TAMBAH INI
            }
        }

        return view('attendance.index', compact('attendances', 'children', 'classrooms')); // 🔥 TAMBAH classrooms
    }

    // ============================================
    // 🔥 GET DATA FOR ATTENDANCE (AJAX)
    // ============================================
    public function getData(Request $request)
    {
        try {
            $user = auth()->user();
            $query = Attendance::with(['child', 'child.classroom']);
            
            // Filter by date
            if ($request->has('date') && $request->date) {
                $query->whereDate('date', $request->date);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Filter by child
            if ($request->has('child_id') && $request->child_id) {
                $query->where('child_id', $request->child_id);
            }
            
            // 🔥 FILTER BY CLASSROOM
            if ($request->has('classroom_id') && $request->classroom_id) {
                $childIds = Child::where('classroom_id', $request->classroom_id)->pluck('id');
                $query->whereIn('child_id', $childIds);
            }
            
            // Filter by user role
            if (in_array($user->role, ['parent', 'parent1'])) {
                $parent = ParentModel::where('user_id', $user->id)->first();
                if ($parent) {
                    $childIds = Child::where('parent_id', $parent->id)
                        ->orWhere('second_parent_id', $parent->id)
                        ->pluck('id');
                    $query->whereIn('child_id', $childIds);
                }
            } elseif ($user->role === 'parent2') {
                $secondParent = SecondParent::where('user_id', $user->id)->first();
                if ($secondParent) {
                    $mainParent = ParentModel::find($secondParent->parent_id);
                    if ($mainParent) {
                        $childIds = Child::where('parent_id', $mainParent->id)
                            ->orWhere('second_parent_id', $mainParent->id)
                            ->pluck('id');
                        $query->whereIn('child_id', $childIds);
                    }
                }
            } elseif ($user->role === 'guardian') {
                $guardian = Guardian::where('user_id', $user->id)->first();
                if ($guardian) {
                    $childIds = Child::where('guardian_id', $guardian->id)->pluck('id');
                    $query->whereIn('child_id', $childIds);
                }
            }
            
            $attendances = $query->orderBy('date', 'desc')
                ->orderBy('checkin_time', 'desc')
                ->get();
            
            // 🔥 FORMAT DATA UNTUK RESPONSE
            $formatted = $attendances->map(function($attendance) {
                // 🔥 AMBIL NAMA UNTUK DROP_OFF_BY
                $dropOffName = $attendance->drop_off_by;
                if ($dropOffName && is_numeric($dropOffName)) {
                    $parent = ParentModel::find($dropOffName);
                    if ($parent) {
                        $dropOffName = $parent->name;
                    } else {
                        $user = \App\Models\User::find($dropOffName);
                        if ($user) {
                            $dropOffName = $user->name;
                        }
                    }
                }
                
                // 🔥 AMBIL NAMA UNTUK PICKUP_BY
                $pickupName = $attendance->pickup_by;
                if ($pickupName && is_numeric($pickupName)) {
                    $parent = ParentModel::find($pickupName);
                    if ($parent) {
                        $pickupName = $parent->name;
                    } else {
                        $user = \App\Models\User::find($pickupName);
                        if ($user) {
                            $pickupName = $user->name;
                        }
                    }
                }
                
                // 🔥 AMBIL CLASSROOM INFO
                $classroom = $attendance->child ? $attendance->child->classroom : null;
                
                return [
                    'id' => $attendance->id,
                    'child_id' => $attendance->child_id,
                    'child_name' => $attendance->child->name ?? 'Unknown',
                    'classroom_id' => $classroom ? $classroom->id : 0,
                    'classroom' => $classroom ? $classroom->name : 'No class',
                    'classroom_color' => $classroom ? $classroom->color : '#94a3b8',
                    'date' => $attendance->date,
                    'date_formatted' => Carbon::parse($attendance->date)->format('d M Y'),
                    'status' => $attendance->status,
                    'checkin_time' => $attendance->checkin_time ? Carbon::parse($attendance->checkin_time)->format('h:i A') : '-',
                    'checkout_time' => $attendance->checkout_time ? Carbon::parse($attendance->checkout_time)->format('h:i A') : '-',
                    'drop_off_by' => $dropOffName ?? '-',
                    'pickup_by' => $pickupName ?? '-',
                    'is_late' => $attendance->is_late ?? false,
                    'late_reason' => $attendance->late_reason ?? '-'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formatted,
                'total' => $formatted->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('getData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // 🔥 CHECKIN - BETULKAN UNTUK QR SCAN
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
            
            $existing = Attendance::where('child_id', $request->child_id)
                ->whereDate('date', $today)
                ->first();
                
            if ($existing && $existing->checkin_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anak ini sudah check-in hari ini!'
                ]);
            }
            
            // 🔥 AMBIL NAMA PARENT
            $parent = ParentModel::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';
            
            // 🔥 CEK SLOT
            $slot = $this->checkTimerSlot();
            $isLate = false;
            
            if ($slot && $slot['type'] === 'checkin') {
                $isLate = false;
            } else {
                // 🔥 KALAU LUAR SLOT, TAPI BOLEH CHECKIN DENGAN STATUS LATE
                $isLate = true;
            }
            
            if ($existing) {
                $existing->update([
                    'checkin_time' => $now->format('H:i:s'),
                    'status' => $isLate ? 'late' : 'checkin',
                    'drop_off_by' => $parentName,
                    'is_verified' => true,
                    'is_late' => $isLate
                ]);
                $attendance = $existing;
            } else {
                $attendance = Attendance::create([
                    'child_id' => $request->child_id,
                    'parent_id' => $request->parent_id,
                    'date' => $today,
                    'checkin_time' => $now->format('H:i:s'),
                    'status' => $isLate ? 'late' : 'checkin',
                    'drop_off_by' => $parentName,
                    'is_verified' => true,
                    'is_late' => $isLate
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Check-in berjaya!',
                'data' => $attendance,
                'is_late' => $isLate,
                'drop_off_by' => $parentName
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
    // 🔥 CHECKOUT - BETULKAN UNTUK QR SCAN
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
            
            // 🔥 AMBIL NAMA PARENT
            $parent = ParentModel::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';
            
            $attendance->update([
                'checkout_time' => $now->format('H:i:s'),
                'status' => 'checkout',
                'pickup_by' => $parentName,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Check-out berjaya!',
                'data' => $attendance,
                'pickup_by' => $parentName
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
    // 🔥 CHECKIN ALL - BETULKAN
    // ============================================
    public function checkinAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|exists:parents,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            
            // 🔥 AMBIL NAMA PARENT
            $parent = ParentModel::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';
            
            // 🔥 CEK SLOT
            $slot = $this->checkTimerSlot();
            $isLate = false;
            
            if (!$slot || $slot['type'] !== 'checkin') {
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
                        'status' => $isLate ? 'late' : 'checkin',
                        'drop_off_by' => $parentName,
                        'is_verified' => true,
                        'is_late' => $isLate
                    ]);
                } else {
                    Attendance::create([
                        'child_id' => $childId,
                        'parent_id' => $request->parent_id,
                        'date' => $today,
                        'checkin_time' => $now->format('H:i:s'),
                        'status' => $isLate ? 'late' : 'checkin',
                        'drop_off_by' => $parentName,
                        'is_verified' => true,
                        'is_late' => $isLate
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
    // 🔥 CHECKOUT ALL - BETULKAN
    // ============================================
    public function checkoutAll(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|exists:parents,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];
            
            // 🔥 AMBIL NAMA PARENT
            $parent = ParentModel::find($request->parent_id);
            $parentName = $parent ? $parent->name : 'Unknown';
            
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
                    'pickup_by' => $parentName,
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
                'already_count' => collect($results)->where('status', 'already_checked')->count()
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
    // CALENDAR PAGE
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
    // GET CALENDAR DATA (AJAX)
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
// 🔥 EXPORT PDF - REPORT SUMMARY / AUDIT TRAIL
// ============================================
public function exportPdf(Request $request)
{
    try {
        $user = auth()->user();
        $query = Attendance::with(['child', 'child.classroom']);
        
        // Apply filters from request
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('classroom_id') && $request->classroom_id) {
            $childIds = Child::where('classroom_id', $request->classroom_id)->pluck('id');
            $query->whereIn('child_id', $childIds);
        }
        
        // Filter by user role
        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', $user->id)->first();
            if ($parent) {
                $childIds = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->pluck('id');
                $query->whereIn('child_id', $childIds);
            }
        } elseif ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', $user->id)->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $childIds = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->pluck('id');
                    $query->whereIn('child_id', $childIds);
                }
            }
        } elseif ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if ($guardian) {
                $childIds = Child::where('guardian_id', $guardian->id)->pluck('id');
                $query->whereIn('child_id', $childIds);
            }
        }
        
        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('checkin_time', 'desc')
            ->get();
        
        // Calculate stats
        $totalCheckin = $attendances->filter(function($item) {
            return in_array($item->status, ['checkin', 'present']);
        })->count();
        
        $totalCheckout = $attendances->where('status', 'checkout')->count();
        $totalLate = $attendances->where('status', 'late')->count();
        $totalAbsent = $attendances->where('status', 'absent')->count();
        
        // 🔥 Generate PDF using DomPDF
        $pdf = \PDF::loadView('attendance.export-pdf', [
            'attendances' => $attendances,
            'total' => $attendances->count(),
            'totalCheckin' => $totalCheckin,
            'totalCheckout' => $totalCheckout,
            'totalLate' => $totalLate,
            'totalAbsent' => $totalAbsent,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => $user->name,
        ]);
        
        return $pdf->download('attendance_report_' . Carbon::now()->format('Y-m-d') . '.pdf');
        
    } catch (\Exception $e) {
        Log::error('Export PDF error: ' . $e->getMessage());
        return back()->with('error', 'Ralat menjana PDF: ' . $e->getMessage());
    }
}

// ============================================
// 🔥 EXPORT SINGLE PDF
// ============================================
public function exportSinglePdf($id)
{
    try {
        $attendance = Attendance::with(['child', 'child.classroom'])->findOrFail($id);
        
        // Format drop_off_by name
        $dropOff = $attendance->drop_off_by;
        if ($dropOff && is_numeric($dropOff)) {
            $parent = ParentModel::find($dropOff);
            if ($parent) {
                $dropOff = $parent->name;
            } else {
                $user = \App\Models\User::find($dropOff);
                if ($user) {
                    $dropOff = $user->name;
                }
            }
        }
        
        $pickup = $attendance->pickup_by;
        if ($pickup && is_numeric($pickup)) {
            $parent = ParentModel::find($pickup);
            if ($parent) {
                $pickup = $parent->name;
            } else {
                $user = \App\Models\User::find($pickup);
                if ($user) {
                    $pickup = $user->name;
                }
            }
        }
        
        $pdf = \PDF::loadView('attendance.export-single-pdf', [
            'attendance' => $attendance,
            'dropOff' => $dropOff ?? '-',
            'pickup' => $pickup ?? '-',
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name,
        ]);
        
        return $pdf->download('attendance_record_' . $attendance->id . '.pdf');
        
    } catch (\Exception $e) {
        Log::error('Export Single PDF error: ' . $e->getMessage());
        return back()->with('error', 'Ralat menjana PDF: ' . $e->getMessage());
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

    // ============================================
    // TIMER SETTINGS
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
            
            Log::info('📥 SAVE TIMER - Data received:', $data);
            
            foreach ($days as $day) {
                if (isset($data[$day])) {
                    $dayData = $data[$day];
                    
                    if (!isset($dayData['morning']['start']) || !isset($dayData['morning']['end']) ||
                        !isset($dayData['evening']['start']) || !isset($dayData['evening']['end'])) {
                        Log::warning("⚠️ Invalid data for {$day}: " . json_encode($dayData));
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
                    Log::info("✅ Saved timer for {$day}");
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
                'message' => 'No valid data to save.'
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
    // ATTENDANCE SCAN - Public search page
    // ============================================
    public function search()
    {
        return view('attendance.search');
    }

    public function searchResults(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $children = Child::where('is_active', true)
            ->where('name', 'like', '%' . $query . '%')
            ->select('id', 'name', 'age', 'photo', 'classroom_id')
            ->with('classroom:id,name')
            ->limit(10)
            ->get()
            ->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'age' => $child->age,
                    'photo' => $child->photo ? asset('storage/' . $child->photo) : null,
                    'classroom' => $child->classroom->name ?? 'N/A',
                ];
            });
        
        return response()->json($children);
    }

    public function verifyPhone(Request $request, Child $child)
    {
        $phone = $request->input('phone', '');
        
        // Normalize phone number - remove spaces, dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Check against parent's phone
        $parent = ParentModel::find($child->parent_id);
        if ($parent) {
            $parentPhone = preg_replace('/[\s\-]/', '', $parent->phone ?? '');
            if ($parentPhone && str_contains($phone, substr($parentPhone, -7))) {
                session(['verified_child_' . $child->id => true]);
                return response()->json(['success' => true]);
            }
        }
        
        // Check against second parent's phone
        $secondParent = SecondParent::find($child->second_parent_id);
        if ($secondParent) {
            $spPhone = preg_replace('/[\s\-]/', '', $secondParent->phone ?? '');
            if ($spPhone && str_contains($phone, substr($spPhone, -7))) {
                session(['verified_child_' . $child->id => true]);
                return response()->json(['success' => true]);
            }
        }
        
        // Check against guardian's phone
        $guardian = Guardian::find($child->guardian_id);
        if ($guardian) {
            $gPhone = preg_replace('/[\s\-]/', '', $guardian->phone ?? '');
            if ($gPhone && str_contains($phone, substr($gPhone, -7))) {
                session(['verified_child_' . $child->id => true]);
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => '❌ No telefon tidak sepadan dengan rekod. Cuba semula.'
        ]);
    }

    public function childProfile(Child $child)
    {
        $child->load(['parent', 'secondParent', 'guardian', 'classroom']);
        
        // Check if verified via session
        $verified = session('verified_child_' . $child->id, false);
        
        // Get today's attendance
        $today = date('Y-m-d', SimulationClock::getCurrentTime());
        $attendance = Attendance::where('child_id', $child->id)
            ->where('date', $today)
            ->first();
        
        return view('attendance.child-profile', compact('child', 'attendance', 'verified'));
    }

    public function processCheckin(Request $request, Child $child)
    {
        $today = date('Y-m-d', SimulationClock::getCurrentTime());
        $now = date('H:i:s', SimulationClock::getCurrentTime());
        
        // Get timer settings for today
        $dayName = date('l', SimulationClock::getCurrentTime());
        $timer = TimerSetting::where('day_name', $dayName)->first();
        $morningEnd = $timer->morning_end ?? '07:30:00';
        
        // Determine if late (after morning end time)
        $isLate = $now > $morningEnd;
        $status = $isLate ? 'late' : 'checkin';
        
        // Get parent name from verified session
        $parent = ParentModel::find($child->parent_id);
        $dropOffName = $parent ? $parent->name : 'Parent';
        
        $attendance = Attendance::firstOrCreate(
            ['child_id' => $child->id, 'date' => $today],
            [
                'status' => $status,
                'checkin_time' => $now,
                'drop_off_by' => $dropOffName,
                'is_verified' => true,
            ]
        );
        
        if (!$attendance->wasRecentlyCreated) {
            $attendance->update([
                'status' => $status,
                'checkin_time' => $now,
                'drop_off_by' => $dropOffName,
                'is_verified' => true,
            ]);
        }
        
        $message = $isLate 
            ? '⚠️ Check-in lewat! (' . date('h:i A', strtotime($now)) . ')'
            : '✅ Check-in berjaya! (' . date('h:i A', strtotime($now)) . ')';
        
        // Send Telegram notification if late
        if ($isLate) {
            $this->sendLateNotification($child, 'check-in', $now, $morningEnd);
        }
        
        return response()->json(['success' => true, 'message' => $message, 'late' => $isLate]);
    }

    public function processCheckout(Request $request, Child $child)
    {
        $today = date('Y-m-d', SimulationClock::getCurrentTime());
        $now = date('H:i:s', SimulationClock::getCurrentTime());
        
        $attendance = Attendance::where('child_id', $child->id)
            ->where('date', $today)
            ->first();
        
        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Sila check-in dahulu.']);
        }
        
        // Get timer settings for today
        $dayName = date('l', SimulationClock::getCurrentTime());
        $timer = TimerSetting::where('day_name', $dayName)->first();
        $eveningEnd = $timer->evening_end ?? '17:30:00';
        
        // Determine if late checkout (after evening end time)
        $isLateCheckout = $now > $eveningEnd;
        $status = $isLateCheckout ? 'late_checkout' : 'checkout';
        
        // Get parent name
        $parent = ParentModel::find($child->parent_id);
        $pickupName = $parent ? $parent->name : 'Parent';
        
        $attendance->update([
            'status' => $status,
            'checkout_time' => $now,
            'pickup_by' => $pickupName,
        ]);
        
        $message = $isLateCheckout 
            ? '⚠️ Check-out lewat! (' . date('h:i A', strtotime($now)) . ')'
            : '✅ Check-out berjaya! (' . date('h:i A', strtotime($now)) . ')';
        
        // Send Telegram notification if late checkout
        if ($isLateCheckout) {
            $this->sendLateNotification($child, 'check-out', $now, $eveningEnd);
        }
        
        return response()->json(['success' => true, 'message' => $message, 'late' => $isLateCheckout]);
    }

    /**
     * Send Telegram notification for late check-in/checkout
     */
    private function sendLateNotification(Child $child, string $type, string $actualTime, string $deadline)
    {
        try {
            $telegram = new TelegramService();
            $parent = ParentModel::find($child->parent_id);
            $user = $parent ? \App\Models\User::find($parent->user_id) : null;
            
            $icon = $type === 'check-in' ? '⏰' : '📤';
            $message = "{$icon} <b>Late {$type} Notification</b>\n\n"
                . "👶 <b>Child:</b> {$child->name}\n"
                . "🏫 <b>Class:</b> " . ($child->classroom->name ?? 'N/A') . "\n"
                . "🕐 <b>Time:</b> " . date('h:i A', strtotime($actualTime)) . "\n"
                . "⏳ <b>Deadline:</b> " . date('h:i A', strtotime($deadline)) . "\n"
                . "👤 <b>Parent:</b> " . ($parent->name ?? 'N/A') . "\n\n"
                . "<i>Please take note. - KIDSTRACK SAFECARE</i>";
            
            // 1. Send to admin
            $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID');
            if ($adminChatId) {
                $telegram->sendMessage($adminChatId, $message);
            }
            
            // 2. Send to parent (if they linked their Telegram)
            if ($user && $user->telegram_chat_id) {
                $telegram->sendMessage($user->telegram_chat_id, $message);
            }
        } catch (\Exception $e) {
            Log::error('Telegram notification failed: ' . $e->getMessage());
        }
    }

    public function getStatus(Child $child)
    {
        $today = date('Y-m-d', SimulationClock::getCurrentTime());
        $attendance = Attendance::where('child_id', $child->id)
            ->where('date', $today)
            ->first();
        
        return response()->json([
            'child' => $child->name,
            'status' => $attendance->status ?? 'absent',
            'checkin_time' => $attendance->checkin_time ?? null,
            'checkout_time' => $attendance->checkout_time ?? null,
        ]);
    }

    public function getAllStatus()
    {
        $today = date('Y-m-d', SimulationClock::getCurrentTime());
        $attendances = Attendance::with('child:id,name,classroom_id')
            ->where('date', $today)
            ->get()
            ->map(function ($att) {
                return [
                    'child' => $att->child->name ?? 'Unknown',
                    'status' => $att->status,
                    'checkin_time' => $att->checkin_time,
                    'checkout_time' => $att->checkout_time,
                ];
            });
        
        return response()->json($attendances);
    }
}