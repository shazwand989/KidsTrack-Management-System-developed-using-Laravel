<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Guardianship;
use App\Models\User;

use App\Models\Classroom;
use App\Models\SimulationClock;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    // ============================================
    // APPLY FILTERS TO ATTENDANCE QUERY
    // ============================================
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('classroom_id')) {
            $childIds = Child::where('classroom_id', $request->classroom_id)->pluck('id');
            $query->whereIn('child_id', $childIds);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('child', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
    }

    // ============================================
    // INDEX - List all attendance
    // ============================================
    public function index(Request $request)
    {
        $user = Auth::user();
        $attendances = collect();
        $children = collect();
        $classrooms = collect();

        if (in_array($user->role, ['admin', 'teacher'])) {
            $query = Attendance::with(['child', 'child.classroom']);
            $this->applyFilters($query, $request);
            $attendances = $query->orderBy('date', 'desc')
                ->orderBy('checkin_time', 'desc')
                ->paginate(10)->appends($request->query());

            // Stats from UNFILTERED data (today's totals, not just current page)
            $statsQuery = Attendance::query();
            $this->applyFilters($statsQuery, $request);
            $allFiltered = $statsQuery->get();
            $stats = [
                'total'     => $allFiltered->count(),
                'checkin'   => $allFiltered->filter(fn($a) => in_array($a->status, ['checkin','present']))->count(),
                'checkout'  => $allFiltered->filter(fn($a) => in_array($a->status, ['checkout','late_checkout']))->count(),
                'late'      => $allFiltered->where('status', 'late')->count(),
                'absent'    => $allFiltered->where('status', 'absent')->count(),
            ];

            $children = Child::with('classroom')->get();
            $classrooms = Classroom::all();
        } elseif (in_array($user->role, ['parent', 'parent1'])) {
            $children = $user->children;
            if ($children->isNotEmpty()) {
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->orderBy('date', 'desc')
                    ->orderBy('checkin_time', 'desc')
                    ->paginate(20);
                $classrooms = Classroom::all();
            }
        } elseif ($user->role === 'parent2') {
            $children = $user->children;
            if ($children->isNotEmpty()) {
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->orderBy('date', 'desc')
                    ->orderBy('checkin_time', 'desc')
                    ->paginate(20);
                $classrooms = Classroom::all();
            }
        } elseif ($user->role === 'guardian') {
            $children = $user->children;
            if ($children->isNotEmpty()) {
                $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                    ->orderBy('date', 'desc')
                    ->orderBy('checkin_time', 'desc')
                    ->paginate(20);
                $classrooms = Classroom::all();
            }
        }

        return view('attendance.index', compact('attendances', 'children', 'classrooms', 'stats'));
    }

    // ============================================
    // 🔥 GET DATA FOR ATTENDANCE (AJAX)
    // ============================================
    public function getData(Request $request)
    {
        try {
            $user = Auth::user();
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
                $childIds = $user->children->pluck('id');
                $query->whereIn('child_id', $childIds);
            } elseif ($user->role === 'parent2') {
                $childIds = $user->children->pluck('id');
                $query->whereIn('child_id', $childIds);
            } elseif ($user->role === 'guardian') {
                $childIds = $user->children->pluck('id');
                $query->whereIn('child_id', $childIds);
            }

            $attendances = $query->orderBy('date', 'desc')
                ->orderBy('checkin_time', 'desc')
                ->get();

            // 🔥 FORMAT DATA UNTUK RESPONSE
            $formatted = $attendances->map(function($attendance) {
                // 🔥 AMBIL NAMA UNTUK DROP_OFF_BY
                $dropOffName = $attendance->drop_off_by;
                if ($dropOffName && is_numeric($dropOffName)) {
                    $dropOffUser = \App\Models\User::find($dropOffName);
                    $dropOffName = $dropOffUser ? $dropOffUser->name : $dropOffName;
                }

                // 🔥 AMBIL NAMA UNTUK PICKUP_BY
                $pickupName = $attendance->pickup_by;
                if ($pickupName && is_numeric($pickupName)) {
                    $pickupUser = \App\Models\User::find($pickupName);
                    $pickupName = $pickupUser ? $pickupUser->name : $pickupName;
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
                'parent_id' => 'required|exists:users,id',
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
            $parent = \App\Models\User::find($request->parent_id);
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
                    'user_id' => $request->parent_id,
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
                'parent_id' => 'required|exists:users,id',
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
            $parent = \App\Models\User::find($request->parent_id);
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
                'parent_id' => 'required|exists:users,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];

            // 🔥 AMBIL NAMA PARENT
            $parent = \App\Models\User::find($request->parent_id);
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
                        'user_id' => $request->parent_id,
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
                'parent_id' => 'required|exists:users,id',
                'child_ids' => 'required|array',
                'child_ids.*' => 'exists:children,id'
            ]);

            $now = Carbon::now('Asia/Kuala_Lumpur');
            $today = $now->toDateString();
            $results = [];

            // 🔥 AMBIL NAMA PARENT
            $parent = \App\Models\User::find($request->parent_id);
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
        $user = Auth::user();
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
            $classrooms = Classroom::all();
            $children = $user->children;
            $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($user->role === 'parent2') {
            $classrooms = Classroom::all();
            $children = $user->children;
            $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($user->role === 'guardian') {
            $classrooms = Classroom::all();
            $children = $user->children;
            $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->orderBy('date', 'asc')
                ->get();
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

        $user = Auth::user();
        $attendances = collect();

        if (in_array($user->role, ['admin', 'teacher'])) {
            $attendances = Attendance::with(['child', 'child.classroom'])
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif (in_array($user->role, ['parent', 'parent1'])) {
            $childrenIds = $user->children->pluck('id');
            $attendances = Attendance::whereIn('child_id', $childrenIds)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($user->role === 'parent2') {
            $childrenIds = $user->children->pluck('id');
            $attendances = Attendance::whereIn('child_id', $childrenIds)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($user->role === 'guardian') {
            $childrenIds = $user->children->pluck('id');
            $attendances = Attendance::whereIn('child_id', $childrenIds)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $attendances,
            'month' => $month,
            'year' => $year
        ]);
    }

    // ============================================
    // CREATE - Show create form
    // ============================================
    public function create()
    {
        $children   = Child::with(['classroom', 'guardianships.user'])->where('is_active', true)->orderBy('name')->get();
        $classrooms = Classroom::all();

        // Today's existing attendance (keyed by child_id)
        $todayAttendance = Attendance::whereDate('date', now()->toDateString())
            ->get()
            ->keyBy('child_id');

        return view('attendance.create', compact('children', 'classrooms', 'todayAttendance'));
    }

    // ============================================
    // STORE - Save new attendance
    // ============================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,checkin,checkout',
            'checkin_time' => 'nullable|date_format:H:i',
            'checkout_time' => 'nullable|date_format:H:i',
            'drop_off_by' => 'nullable|string|max:255',
            'pickup_by' => 'nullable|string|max:255',
            'late_reason' => 'nullable|string|max:500',
            'status_note' => 'nullable|string|max:500',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record created successfully.');
    }

    // ============================================
    // BATCH STORE — Save multiple attendance records
    // ============================================
    public function batchStore(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'attendances' => 'required|array',
        ]);

        $date   = $request->date;
        $count  = 0;
        $errors = [];

        // Preload classroom schedules
        $classrooms = \App\Models\Classroom::all()->keyBy('id');
        $children   = \App\Models\Child::whereIn('id', array_keys($request->attendances))->get()->keyBy('id');

        foreach ($request->attendances as $childId => $data) {
            if (empty($data['status'])) continue;

            $status = $data['status'];

            // Auto-detect late: if status is 'present' but checkin time > classroom start_time
            if ($status === 'present' && !empty($data['checkin_time'])) {
                $child     = $children[$childId] ?? null;
                $classroom = $child ? ($classrooms[$child->classroom_id] ?? null) : null;
                $startTime = $classroom ? substr($classroom->start_time, 0, 5) : '07:00';

                if ($data['checkin_time'] > $startTime) {
                    $status = 'late';
                }
            }

            try {
                Attendance::updateOrCreate(
                    ['child_id' => $childId, 'date' => $date],
                    [
                        'status'       => $status,
                        'checkin_time' => $data['checkin_time'] ?? null,
                        'checkout_time'=> $data['checkout_time'] ?? null,
                        'drop_off_by'  => $data['drop_off_by'] ?? null,
                        'pickup_by'    => $data['pickup_by'] ?? null,
                        'late_reason'  => $data['late_reason'] ?? null,
                        'is_verified'  => $status !== 'absent',
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Child #{$childId}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => true,
            'saved'   => $count,
            'errors'  => $errors,
        ]);
    }

    // ============================================
    // SHOW - Attendance details
    // ============================================
    public function show(int $id)
    {
        $attendance = Attendance::with(['child', 'child.classroom'])->findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }

    // ============================================
    // EDIT - Show edit form
    // ============================================
    public function edit(int $id)
    {
        $attendance = Attendance::with(['child', 'child.classroom'])->findOrFail($id);
        $children = Child::with('classroom')->where('is_active', true)->get();
        $classrooms = Classroom::all();
        return view('attendance.create', compact('attendance', 'children', 'classrooms'));
    }

    // ============================================
    // UPDATE - Update attendance record
    // ============================================
    public function update(Request $request, int $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,checkin,checkout',
            'checkin_time' => 'nullable|date_format:H:i',
            'checkout_time' => 'nullable|date_format:H:i',
            'drop_off_by' => 'nullable|string|max:255',
            'pickup_by' => 'nullable|string|max:255',
            'late_reason' => 'nullable|string|max:500',
            'status_note' => 'nullable|string|max:500',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    // ============================================
    // DESTROY - Delete attendance record
    // ============================================
    public function destroy(int $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'teacher'])) {
            return redirect()->route('attendance.index')
                ->with('error', 'You do not have permission to delete attendance records.');
        }

        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    // ============================================
    // CHILD ATTENDANCE
    // ============================================
    public function childAttendance(int $childId)
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
        // Increase memory for PDF generation
        ini_set('memory_limit', '256M');

        $user = Auth::user();
        $query = Attendance::with(['child', 'child.classroom']);

        // Apply same filters as the list page
        $hasFilters = false;
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
            $hasFilters = true;
        } else {
            // Safety: default to current month to prevent memory exhaustion
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('classroom_id') && $request->classroom_id) {
            $childIds = Child::where('classroom_id', $request->classroom_id)->pluck('id');
            $query->whereIn('child_id', $childIds);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('child', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Filter by user role
        if (in_array($user->role, ['parent', 'parent1'])) {
            $childIds = $user->children->pluck('id');
            $query->whereIn('child_id', $childIds);
        } elseif ($user->role === 'parent2') {
            $childIds = $user->children->pluck('id');
            $query->whereIn('child_id', $childIds);
        } elseif ($user->role === 'guardian') {
            $childIds = $user->children->pluck('id');
            $query->whereIn('child_id', $childIds);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('checkin_time', 'desc')
            ->get();

        // Calculate stats
        $totalCheckin = $attendances->filter(function($item) {
            return in_array($item->status, ['checkin', 'present']);
        })->count();

        $totalCheckout = $attendances->filter(function($item) {
            return in_array($item->status, ['checkout', 'late_checkout']);
        })->count();
        $totalLate = $attendances->where('status', 'late')->count();
        $totalAbsent = $attendances->where('status', 'absent')->count();

        // Generate PDF with minimal memory footprint
        $pdf = Pdf::loadView('attendance.export-pdf', [
            'attendances' => $attendances,
            'total' => $attendances->count(),
            'totalCheckin' => $totalCheckin,
            'totalCheckout' => $totalCheckout,
            'totalLate' => $totalLate,
            'totalAbsent' => $totalAbsent,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => $user->name,
        ])->setPaper('a4', 'landscape')
          ->setOptions([
              'isRemoteEnabled' => false,
              'isHtml5ParserEnabled' => true,
              'defaultFont' => 'DejaVu Sans',
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
public function exportSinglePdf(int $id)
{
    try {
        $attendance = Attendance::with(['child', 'child.classroom'])->findOrFail($id);

        // Format drop_off_by name
        $dropOff = $attendance->drop_off_by;
        if ($dropOff && is_numeric($dropOff)) {
            $dropOffUser = \App\Models\User::find($dropOff);
            if ($dropOffUser) {
                $dropOff = $dropOffUser->name;
            }
        }

        $pickup = $attendance->pickup_by;
        if ($pickup && is_numeric($pickup)) {
            $pickupUser = \App\Models\User::find($pickup);
            if ($pickupUser) {
                $pickup = $pickupUser->name;
            }
        }

        $pdf = Pdf::loadView('attendance.export-single-pdf', [
            'attendance' => $attendance,
            'dropOff' => $dropOff ?? '-',
            'pickup' => $pickup ?? '-',
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'generated_by' => Auth::user()->name,
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
    public function getChildAttendance(int $childId)
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
    // ATTENDANCE SCAN - Public search page
    // ============================================
    public function search()
    {
        return view('attendance.search');
    }

    public function searchResults(Request $request)
    {
        $query = $request->input('q', '');

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
        $parent = $child->parent;
        if ($parent) {
            $parentPhone = preg_replace('/[\s\-]/', '', $parent->phone_number ?? '');
            if ($parentPhone && str_contains($phone, substr($parentPhone, -7))) {
                session(['verified_child_' . $child->id => true]);
                return response()->json(['success' => true]);
            }
        }

        // Check against second parent's phone
        $secondParent = $child->secondParent;
        if ($secondParent) {
            $spPhone = preg_replace('/[\s\-]/', '', $secondParent->phone_number ?? '');
            if ($spPhone && str_contains($phone, substr($spPhone, -7))) {
                session(['verified_child_' . $child->id => true]);
                return response()->json(['success' => true]);
            }
        }

        // Check against guardian's phone
        $guardian = $child->guardian;
        if ($guardian) {
            $gPhone = preg_replace('/[\s\-]/', '', $guardian->phone_number ?? '');
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
        $child->load(['classroom', 'parent', 'secondParent', 'guardian']);

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

        // 🔥 BLOCK if already checked in OR checked out today
        $existing = Attendance::where('child_id', $child->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->checkin_time) {
            $msg = $existing->checkout_time
                ? 'Anak ini sudah check-in dan check-out hari ini!'
                : 'Anak ini sudah check-in hari ini pada ' . date('h:i A', strtotime($existing->checkin_time)) . '!';
            return response()->json(['success' => false, 'message' => $msg]);
        }

        // Use classroom schedule to determine if check-in is late
        $morningEnd = $child->classroom->start_time ?? '07:30:00';
        $isLate = $now > $morningEnd;
        $status = $isLate ? 'late' : 'checkin';

        // Get parent name
        $parent = $child->parent;
        $dropOffName = $parent ? $parent->name : 'Parent';

        if ($existing) {
            $existing->update([
                'status' => $status,
                'checkin_time' => $now,
                'drop_off_by' => $dropOffName,
                'is_verified' => true,
            ]);
            $attendance = $existing;
        } else {
            $attendance = Attendance::create([
                'child_id' => $child->id,
                'date' => $today,
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

        // Use classroom schedule to determine if checkout is late
        $eveningEnd = $child->classroom->end_time ?? '17:30:00';
        $isLateCheckout = $now > $eveningEnd;
        $status = $isLateCheckout ? 'late_checkout' : 'checkout';

        // Get parent name
        $parent = $child->parent;
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
            $parent = $child->parent;
            $user = $parent; // Parent IS the user now

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

    // ============================================
    // 🔥 NEW: VERIFY PARENT BY IC + PHONE
    // ============================================
    public function verifyParent(Request $request)
    {
        $ic = str_replace(['-', ' '], '', $request->input('ic', ''));
        $phone = preg_replace('/[\s\-]/', '', $request->input('phone', ''));

        if (strlen($ic) < 12 || strlen($phone) < 7) {
            return response()->json(['success' => false, 'message' => 'IC atau telefon tidak lengkap.']);
        }

        // Find user by IC (stored as ic_number on children) or by phone
        // First, find children matching this IC
        $child = Child::where('ic_number', 'like', '%' . substr($ic, -6) . '%')->first();

        if (!$child) {
            // Try finding by phone on any parent user
            $user = \App\Models\User::where('phone_number', 'like', '%' . substr($phone, -7) . '%')
                ->whereIn('role', ['parent1', 'parent2', 'guardian'])
                ->first();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'IC atau telefon tidak dijumpai.']);
            }
            // Find children linked to this user
            $childIds = Guardianship::where('user_id', $user->id)->pluck('child_id');
        } else {
            // Find parent user linked to this child
            $gs = Guardianship::where('child_id', $child->id)
                ->where('relationship', 'main_parent')
                ->first();
            if (!$gs) {
                return response()->json(['success' => false, 'message' => 'Parent tidak dijumpai.']);
            }
            $user = User::find($gs->user_id);
            // Verify phone matches
            if ($user && $user->phone_number) {
                $userPhone = preg_replace('/[\s\-]/', '', $user->phone_number);
                if (!str_contains($phone, substr($userPhone, -7))) {
                    return response()->json(['success' => false, 'message' => 'Nombor telefon tidak sepadan.']);
                }
            }
            $childIds = Guardianship::where('user_id', $user->id)->pluck('child_id');
        }

        // Get all children linked to this parent
        $children = Child::whereIn('id', $childIds)
            ->where('is_active', true)
            ->with('classroom')
            ->get();

        if ($children->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tiada anak berdaftar.']);
        }

        $today = date('Y-m-d');
        $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('child_id');

        $childrenData = $children->map(function ($c) use ($attendances) {
            $att = $attendances->get($c->id);
            $ciTime = $att && $att->checkin_time ? date('h:i A', strtotime($att->checkin_time)) : null;
            $coTime = $att && $att->checkout_time ? date('h:i A', strtotime($att->checkout_time)) : null;
            return [
                'id' => $c->id,
                'name' => $c->name,
                'age' => $c->age,
                'classroom' => $c->classroom->name ?? '-',
                'initial' => strtoupper(substr($c->name, 0, 1)),
                'checked_in' => $att && $att->checkin_time ? true : false,
                'checked_out' => $att && $att->checkout_time ? true : false,
                'ci_time' => $ciTime,
                'co_time' => $coTime,
            ];
        });

        return response()->json([
            'success' => true,
            'parent_id' => $user->id,
            'parent_name' => $user->name,
            'children' => $childrenData,
        ]);
    }

    // ============================================
    // 🔥 NEW: BULK CHECKIN FROM SCAN PAGE
    // ============================================
    public function bulkCheckinScan(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'child_ids' => 'required|array',
            'child_ids.*' => 'exists:children,id',
        ]);

        $user = User::find($request->parent_id);
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $count = 0;
        $results = [];

        // Preload children with classrooms
        $children = \App\Models\Child::with('classroom')
            ->whereIn('id', $request->child_ids)
            ->get()
            ->keyBy('id');

        foreach ($request->child_ids as $childId) {
            $existing = Attendance::where('child_id', $childId)
                ->where('date', $today)
                ->first();

            if ($existing && $existing->checkin_time) continue;

            $child = $children[$childId] ?? null;
            $classroom = $child->classroom ?? null;
            $startTime = $classroom ? substr($classroom->start_time, 0, 5) : '07:00';
            $isLate = $now > $startTime;
            $status = $isLate ? 'late' : 'present';

            if ($existing) {
                $existing->update([
                    'checkin_time' => $now, 'status' => $status,
                    'drop_off_by' => $user->name, 'is_verified' => true,
                ]);
            } else {
                Attendance::create([
                    'child_id' => $childId, 'user_id' => $user->id,
                    'date' => $today, 'checkin_time' => $now,
                    'status' => $status, 'drop_off_by' => $user->name,
                    'is_verified' => true,
                ]);
            }

            $results[] = [
                'child_id'      => $childId,
                'child_name'    => $child->name ?? 'Unknown',
                'classroom'     => $classroom->name ?? '-',
                'start_time'    => $startTime,
                'is_late'       => $isLate,
                'checkin_time'  => date('h:i A', strtotime($now)),
            ];
            $count++;
        }

        // Re-fetch children with updated attendance status
        $allChildIds = Guardianship::where('user_id', $user->id)->pluck('child_id');
        $allChildren = Child::whereIn('id', $allChildIds)
            ->where('is_active', true)
            ->with('classroom')
            ->get();
        $allAttendances = Attendance::whereIn('child_id', $allChildren->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('child_id');
        $childrenData = $allChildren->map(function ($c) use ($allAttendances) {
            $att = $allAttendances->get($c->id);
            $ciTime = $att && $att->checkin_time ? date('h:i A', strtotime($att->checkin_time)) : null;
            $coTime = $att && $att->checkout_time ? date('h:i A', strtotime($att->checkout_time)) : null;
            return [
                'id' => $c->id,
                'name' => $c->name,
                'age' => $c->age,
                'classroom' => $c->classroom->name ?? '-',
                'initial' => strtoupper(substr($c->name, 0, 1)),
                'checked_in' => $att && $att->checkin_time ? true : false,
                'checked_out' => $att && $att->checkout_time ? true : false,
                'ci_time' => $ciTime,
                'co_time' => $coTime,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $count,
            'results' => $results,
            'children' => $childrenData,
            'message' => "{$count} anak berjaya check-in!",
        ]);
    }

    /**
     * Bulk checkout from parent scan page.
     */
    public function bulkCheckoutScan(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'child_ids' => 'required|array',
            'child_ids.*' => 'exists:children,id',
        ]);

        $user  = User::find($request->parent_id);
        $today = date('Y-m-d');
        $now   = date('H:i:s');
        $count = 0;
        $results = [];

        $children = \App\Models\Child::with('classroom')
            ->whereIn('id', $request->child_ids)
            ->get()
            ->keyBy('id');

        foreach ($request->child_ids as $childId) {
            $attendance = Attendance::where('child_id', $childId)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->checkin_time) continue;
            if ($attendance->checkout_time) continue;

            $child = $children[$childId] ?? null;
            $classroom = $child->classroom ?? null;
            $endTime = $classroom ? substr($classroom->end_time, 0, 5) : '17:00';
            $isEarly = $now < $endTime;

            $attendance->update([
                'checkout_time' => $now,
                'status' => $isEarly ? 'checkout' : 'late_checkout',
                'pickup_by' => $user->name,
                'is_verified' => true,
            ]);

            $results[] = [
                'child_id'      => $childId,
                'child_name'    => $child->name ?? 'Unknown',
                'classroom'     => $classroom->name ?? '-',
                'checkout_time' => date('h:i A', strtotime($now)),
                'end_time'      => $endTime,
                'is_early'      => $isEarly,
                'pickup_by'     => $user->name,
            ];
            $count++;
        }

        // Re-fetch children with updated attendance status
        $allChildIds = Guardianship::where('user_id', $user->id)->pluck('child_id');
        $allChildren = Child::whereIn('id', $allChildIds)
            ->where('is_active', true)
            ->with('classroom')
            ->get();
        $allAttendances = Attendance::whereIn('child_id', $allChildren->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('child_id');
        $childrenData = $allChildren->map(function ($c) use ($allAttendances) {
            $att = $allAttendances->get($c->id);
            $ciTime = $att && $att->checkin_time ? date('h:i A', strtotime($att->checkin_time)) : null;
            $coTime = $att && $att->checkout_time ? date('h:i A', strtotime($att->checkout_time)) : null;
            return [
                'id' => $c->id,
                'name' => $c->name,
                'age' => $c->age,
                'classroom' => $c->classroom->name ?? '-',
                'initial' => strtoupper(substr($c->name, 0, 1)),
                'checked_in' => $att && $att->checkin_time ? true : false,
                'checked_out' => $att && $att->checkout_time ? true : false,
                'ci_time' => $ciTime,
                'co_time' => $coTime,
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $count,
            'results' => $results,
            'children' => $childrenData,
        ]);
    }
}

