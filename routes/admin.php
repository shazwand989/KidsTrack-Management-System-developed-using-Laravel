<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Admin/Teacher controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\LecturerGroupController;
use App\Http\Controllers\StudentTimetableController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SimulationClockController;
use App\Http\Controllers\QRScanController;

// Models used in closures
use App\Models\User;
use App\Models\Subject;
use App\Models\Hall;
use App\Models\Day;
use App\Models\LecturerGroup;
use App\Models\StudentTimetable;

// ============================================
// ADMIN / TEACHER ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {

    // ============================================
    // ROOT & DASHBOARD (ADMIN/TEACHER ONLY)
    // ============================================
    Route::get('/', function () {
        $user = Auth::user();
        if (in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            return redirect()->route('parent.dashboard');
        }
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        if (Auth::check() && in_array(Auth::user()->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            return redirect()->route('parent.dashboard');
        }

        return view('home', [
            'studentCount'      => User::where('role', 'student')->count(),
            'subjectCount'      => Subject::count(),
            'hallCount'         => Hall::count(),
            'dayCount'          => Day::count(),
            'lecturerGroupCount' => LecturerGroup::count(),
            'timetableCount'    => StudentTimetable::count(),
            'recentStudents'    => User::where('role', 'student')->latest()->take(4)->get(),
            'latestTimetables'  => StudentTimetable::with(['user', 'subject', 'day'])->latest()->take(4)->get(),
        ]);
    })->middleware(['verified'])->name('dashboard');

    Route::get('/home', fn () => redirect()->route('dashboard'))->name('home');

    // ============================================
    // PROFILE
    // ============================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ============================================
    // RESOURCE ROUTES (ADMIN ONLY)
    // ============================================
    Route::resource('students', StudentController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('halls', HallController::class);
    Route::resource('days', DayController::class);
    Route::resource('lecturer-groups', LecturerGroupController::class);
    Route::resource('student-timetables', StudentTimetableController::class);

    Route::resource('parents', ParentController::class);
    Route::post('/parents/check-email', [ParentController::class, 'checkEmail'])->name('parents.check-email');
    Route::resource('children', ChildController::class);
    Route::post('/children/check-ic', [ChildController::class, 'checkIc'])->name('children.check-ic');
    Route::resource('teachers', TeacherController::class);
    Route::resource('classrooms', ClassroomController::class);

    // ============================================
    // ATTENDANCE MANAGEMENT
    // ============================================
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
    Route::get('/attendance/data', [AttendanceController::class, 'getData'])->name('attendance.data');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // ============================================
    // QR CODE
    // ============================================
    Route::get('/qr-code', fn () => view('admin.qr-code'))->name('qr.code');

    // ============================================
    // SCAN QR - PROTECTED
    // ============================================
    Route::post('/scan-qr/process', [QRScanController::class, 'processQR'])->name('scan.qr.process');
    Route::post('/scan-qr/confirm', [QRScanController::class, 'confirmQR'])->name('scan.qr.confirm');

    // ============================================
    // API: CHECK CHILD ACCESS
    // ============================================
    Route::post('/api/check-child-access', function (Request $request) {
        $qrData = $request->qr_code;
        $child = \App\Models\Child::where('qr_code', $qrData)->first();

        if (!$child) {
            return response()->json(['has_access' => false]);
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['has_access' => false]);
        }

        if (in_array($user->role, ['admin', 'teacher'])) {
            return response()->json(['has_access' => true]);
        }

        if ($user->children()->where('child_id', $child->id)->exists()) {
            return response()->json(['has_access' => true]);
        }

        return response()->json(['has_access' => false]);
    });

    // ============================================
    // REMINDER APIs
    // ============================================
    Route::post('/api/send-reminder', function (Request $request) {
        $request->validate([
            'date'      => 'required|date',
            'child_ids' => 'nullable|array',
        ]);

        $date = $request->date;
        $childIds = $request->child_ids ?? [];

        if (empty($childIds)) {
            $attendances = \App\Models\Attendance::whereDate('date', $date)->get();
            $childIds = $attendances->pluck('child_id')->unique()->toArray();
        }

        $children = \App\Models\Child::whereIn('id', $childIds)->get();
        $count = $children->count();

        return response()->json([
            'success' => true,
            'message' => "Reminder sent to {$count} child(ren) for {$date}",
            'data'    => [
                'date'     => $date,
                'children' => $children->pluck('name'),
                'count'    => $count,
            ],
        ]);
    })->name('api.send.reminder');

    Route::post('/api/send-bulk-reminder', function (Request $request) {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $attendances = \App\Models\Attendance::whereDate('date', $date)->get();
        $childIds = $attendances->pluck('child_id')->unique()->toArray();
        $children = \App\Models\Child::whereIn('id', $childIds)->get();
        $count = $children->count();

        return response()->json([
            'success' => true,
            'message' => "Bulk reminder sent to {$count} child(ren) for {$date}",
            'data'    => [
                'date'     => $date,
                'children' => $children->pluck('name'),
                'count'    => $count,
            ],
        ]);
    })->name('api.send.bulk.reminder');

});

// ============================================
// ATTENDANCE EXPORT PDF (ADMIN - NO AUTH GROUP BUT PROTECTED BY CONTROLLER)
// ============================================
Route::get('/attendance/export-single/{id}', [AttendanceController::class, 'exportSinglePdf'])
    ->name('attendance.export.single');
Route::get('/attendance/export-pdf', [AttendanceController::class, 'exportPdf'])
    ->name('attendance.export.pdf');

// ============================================
// SIMULATION CLOCK SETTINGS (ADMIN)
// ============================================
Route::get('/simulation/setting', [SimulationClockController::class, 'setting'])->name('simulation.setting');
Route::post('/simulation/save', [SimulationClockController::class, 'save'])->name('simulation.save');
Route::get('/simulation/dashboard', [SimulationClockController::class, 'dashboard'])->name('simulation.dashboard');

// ============================================
// CHILD QR CODE GENERATION (ADMIN)
// ============================================
Route::get('/child/qr/generate/{id}', [ChildController::class, 'generateQR'])->name('child.qr.generate');
Route::get('/child/qr/{id}', [ChildController::class, 'showQR'])->name('child.qr.show');
Route::get('/child/qr/download/{id}', [ChildController::class, 'downloadQR'])->name('child.qr.download');
Route::get('/child/qr/image/{id}', [ChildController::class, 'getQR'])->name('child.qr.image');

// ============================================
// ATTENDANCE CALENDAR (ADMIN)
// ============================================
Route::get('/attendance-calendar', [AttendanceController::class, 'calendar'])
    ->middleware(['auth'])
    ->name('attendance.calendar');
