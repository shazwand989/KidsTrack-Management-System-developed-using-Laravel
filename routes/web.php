<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
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
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\QRScanController;
use App\Http\Controllers\SimulationClockController;

// Models
use App\Models\User;
use App\Models\Subject;
use App\Models\Hall;
use App\Models\Day;
use App\Models\LecturerGroup;
use App\Models\StudentTimetable;
use App\Models\Child;
use App\Models\ParentModel;

// ============================================
// 🔥 SIMULATION CLOCK ROUTES
// ============================================

Route::get('/simulation/setting', [SimulationClockController::class, 'setting'])->name('simulation.setting');
Route::post('/simulation/save', [SimulationClockController::class, 'save'])->name('simulation.save');
Route::get('/simulation/dashboard', [SimulationClockController::class, 'dashboard'])->name('simulation.dashboard');

Route::get('/api/simulation-time', function() {
    return response()->json([
        'time' => \App\Models\SimulationClock::getFormattedTime(),
        'date' => \App\Models\SimulationClock::getFormattedDate(),
        'timestamp' => \App\Models\SimulationClock::getCurrentTime(),
        'slot' => \App\Models\SimulationClock::getCurrentSlot(),
        'status' => \App\Models\SimulationClock::getStatus(),
        'timer' => \App\Models\SimulationClock::getTimerForToday(),
        'use_simulation' => \App\Models\SimulationClock::first()->use_simulation ?? false
    ]);
})->name('api.simulation.time');

// ============================================
// ROOT
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            return redirect()->route('parent.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ============================================
// DASHBOARD
// ============================================

Route::get('/dashboard', function () {
    if (auth()->check() && in_array(auth()->user()->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
        return redirect()->route('parent.dashboard');
    }

    return view('home', [
        'studentCount' => User::where('role', 'student')->count(),
        'subjectCount' => Subject::count(),
        'hallCount' => Hall::count(),
        'dayCount' => Day::count(),
        'lecturerGroupCount' => LecturerGroup::count(),
        'timetableCount' => StudentTimetable::count(),
        'recentStudents' => User::where('role', 'student')->latest()->take(4)->get(),
        'latestTimetables' => StudentTimetable::with(['user', 'subject', 'day'])->latest()->take(4)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('home');

// ============================================
// QR CODE SCANNING ROUTES (PUBLIC - NO AUTH)
// ============================================

Route::prefix('attendance-scan')->withoutMiddleware(['auth'])->group(function () {
    Route::get('/scan', fn() => redirect()->route('attendance-scan.search'))->name('attendance-scan.landing');
    Route::get('/search', [AttendanceController::class, 'search'])->name('attendance-scan.search');
    Route::get('/search/results', [AttendanceController::class, 'searchResults'])->name('attendance-scan.search.results');
    Route::post('/child/{child}/verify', [AttendanceController::class, 'verifyPhone'])->name('attendance-scan.verify');
    Route::get('/child/{child}', [AttendanceController::class, 'childProfile'])->name('attendance-scan.child');
    Route::post('/checkin/{child}', [AttendanceController::class, 'processCheckin'])->name('attendance-scan.checkin.process');
    Route::post('/checkout/{child}', [AttendanceController::class, 'processCheckout'])->name('attendance-scan.checkout.process');
    Route::get('/status/{child}', [AttendanceController::class, 'getStatus'])->name('attendance-scan.status');
    Route::get('/status-all', [AttendanceController::class, 'getAllStatus'])->name('attendance-scan.status-all');
});

// ============================================
// QR CODE GENERATION
// ============================================

Route::get('/child/qr/generate/{id}', [ChildController::class, 'generateQR'])->name('child.qr.generate');
Route::get('/child/qr/{id}', [ChildController::class, 'showQR'])->name('child.qr.show');
Route::get('/child/qr/download/{id}', [ChildController::class, 'downloadQR'])->name('child.qr.download');
Route::get('/child/qr/image/{id}', [ChildController::class, 'getQR'])->name('child.qr.image');

// ============================================
// 🔥🔥🔥 SCAN QR - PUBLIC PAGES 🔥🔥🔥
// ============================================

Route::get('/scan-qr', function () {
    return view('parent.scan-qr');
})->name('scan.qr.page');

Route::get('/scan-qr/check', function () {
    return response()->json([
        'logged_in' => auth()->check()
    ]);
})->name('scan.qr.check');

Route::get('/scan-qr/result/{qr_data}', function ($qrData) {
    if (!auth()->check()) {
        return redirect()->route('login')->with('redirect_after_login', url('/scan-qr/result/' . $qrData));
    }
    
    $child = Child::where('qr_code', $qrData)->with(['parent', 'classroom', 'attendances'])->first();
    
    if (!$child) {
        return redirect()->route('kiosk.index')->with('error', 'QR Code tidak sah! Sila cuba lagi.');
    }
    
    $user = auth()->user();
    $parent = ParentModel::where('user_id', $user->id)->first();
    
    if ($parent && $child->parent_id != $parent->id && $child->second_parent_id != $parent->id) {
        if (!in_array($user->role, ['admin', 'teacher'])) {
            return redirect()->route('kiosk.index')->with('error', 'Anda tidak mempunyai akses ke anak ini!');
        }
    }
    
    return view('parent.scan-profile', compact('child', 'qrData'));
})->name('scan.qr.result');

Route::get('/scan-qr/{qr_code}', [QRScanController::class, 'show'])->name('scan.qr.show');

// ============================================
// 🔥🔥🔥 KIOSK ROUTES 🔥🔥🔥
// ============================================

Route::get('/kiosk', [QRScanController::class, 'kiosk'])->name('kiosk.index');
Route::post('/kiosk/check-gps', [QRScanController::class, 'checkGPS'])->name('kiosk.check.gps');
Route::get('/kiosk/confirm-child/{child}', [QRScanController::class, 'confirmChild'])->name('kiosk.confirm.child');
Route::get('/kiosk/add-another/{child}', [QRScanController::class, 'showAddAnother'])->name('kiosk.add.another');
Route::get('/kiosk/status/{child}', [QRScanController::class, 'showStatus'])->name('kiosk.status');
Route::get('/kiosk/checkin-page/{child}', [QRScanController::class, 'showCheckinPage'])->name('kiosk.checkin.page');

// 🔥 KIOSK API ROUTES
Route::post('/kiosk/submit-attendance', [QRScanController::class, 'submitAttendance'])->name('kiosk.submit.attendance');
Route::post('/kiosk/checkin-all', [QRScanController::class, 'checkinAll'])->name('kiosk.checkin.all');
Route::post('/kiosk/checkout-all', [QRScanController::class, 'checkoutAll'])->name('kiosk.checkout.all');

// 🔥 KIOSK LEGACY
Route::post('/kiosk/scan', [QRScanController::class, 'handleKioskScan'])->name('kiosk.scan');
Route::post('/kiosk/confirm', [QRScanController::class, 'confirmAttendance'])->name('kiosk.confirm');
Route::get('/kiosk/attendance/{child}', [QRScanController::class, 'getAttendance'])->name('kiosk.attendance');
Route::get('/kiosk/today', [QRScanController::class, 'getTodayAttendance'])->name('kiosk.today');
Route::get('/kiosk/checkin/{child}', function ($childId) {
    $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
    return view('kiosk.checkin', compact('child'));
})->name('kiosk.checkin');
Route::get('/kiosk/checkout/{child}', function ($childId) {
    $child = Child::with(['parent', 'classroom'])->findOrFail($childId);
    return view('kiosk.checkout', compact('child'));
})->name('kiosk.checkout');
Route::post('/kiosk/confirm-checkin', [QRScanController::class, 'confirmCheckin'])->name('kiosk.confirm.checkin');
Route::post('/kiosk/confirm-checkout', [QRScanController::class, 'confirmCheckout'])->name('kiosk.confirm.checkout');
Route::get('/kiosk/child-profile/{child}', [QRScanController::class, 'showChildProfile'])->name('kiosk.child.profile');
Route::redirect('/parent/qr-code', '/kiosk', 301);
Route::redirect('/parent/qr-code/{child}', '/kiosk', 301);

// ============================================
// 🔥🔥🔥 TIMER SETTINGS ROUTES 🔥🔥🔥
// ============================================

Route::get('/get-timer-settings', [QRScanController::class, 'getTimerSettings'])->name('get.timer.settings');
Route::post('/save-timer-settings', [QRScanController::class, 'saveTimerSettings'])->name('save.timer.settings');
Route::post('/reset-timer-settings', [QRScanController::class, 'resetTimerSettings'])->name('reset.timer.settings');
Route::get('/attendance-calendar-data', [QRScanController::class, 'getCalendarData'])->name('attendance.calendar.data');

// ============================================
// ATTENDANCE CALENDAR
// ============================================

Route::get('/attendance-calendar', [AttendanceController::class, 'calendar'])
    ->middleware(['auth'])
    ->name('attendance.calendar');

// ============================================
// HOLIDAYS API
// ============================================

Route::get('/api/holidays/{year}/{month}', function ($year, $month) {
    try {
        $apiUrl = "https://date.nager.at/api/v3/PublicHolidays/{$year}/MY";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $monthHolidays = array_filter($data, function($h) use ($month) {
                $date = new DateTime($h['date']);
                return (int)$date->format('m') == $month;
            });
            
            return response()->json([
                'success' => true,
                'data' => array_values($monthHolidays)
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => getLocalHolidays($year, $month),
                'source' => 'local'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => true,
            'data' => getLocalHolidays($year, $month),
            'source' => 'local'
        ]);
    }
})->name('api.holidays');

function getLocalHolidays($year, $month) {
    $holidays = [];
    $allHolidays = [
        '01-01' => 'Tahun Baru',
        '02-01' => 'Hari Wilayah Persekutuan',
        '05-01' => 'Hari Pekerja',
        '05-22' => 'Hari Wesak',
        '06-01' => 'Hari Keputeraan Agong',
        '06-17' => 'Hari Raya Haji',
        '07-07' => 'Awal Muharram',
        '08-31' => 'Hari Merdeka',
        '09-16' => 'Hari Malaysia',
        '09-30' => 'Maulidur Rasul',
        '11-04' => 'Deepavali',
        '12-25' => 'Krismas',
    ];
    
    if ($year == 2024) {
        $allHolidays['04-10'] = 'Hari Raya Puasa';
        $allHolidays['04-11'] = 'Hari Raya Puasa (Hari 2)';
    }
    if ($year == 2025) {
        $allHolidays['03-31'] = 'Hari Raya Puasa';
        $allHolidays['04-01'] = 'Hari Raya Puasa (Hari 2)';
    }
    
    foreach ($allHolidays as $date => $name) {
        $dateObj = DateTime::createFromFormat('m-d', $date);
        if ($dateObj && (int)$dateObj->format('m') == $month) {
            $holidays[] = [
                'date' => $year . '-' . $date,
                'name' => $name,
                'localName' => $name
            ];
        }
    }
    
    return $holidays;
}

// ============================================
// API ROUTES
// ============================================

Route::post('/api/check-child-access', function(Request $request) {
    $qrData = $request->qr_code;
    $child = Child::where('qr_code', $qrData)->first();
    
    if (!$child) return response()->json(['has_access' => false]);
    
    $user = auth()->user();
    if (!$user) return response()->json(['has_access' => false]);
    
    if (in_array($user->role, ['admin', 'teacher'])) {
        return response()->json(['has_access' => true]);
    }
    
    $parent = ParentModel::where('user_id', $user->id)->first();
    if ($parent && ($child->parent_id == $parent->id || $child->second_parent_id == $parent->id)) {
        return response()->json(['has_access' => true]);
    }
    
    return response()->json(['has_access' => false]);
})->middleware('auth');

// ============================================
// SCAN QR - PROTECTED ROUTES
// ============================================

Route::middleware(['auth'])->group(function () {
    Route::post('/scan-qr/process', [QRScanController::class, 'processQR'])->name('scan.qr.process');
    Route::post('/scan-qr/confirm', [QRScanController::class, 'confirmQR'])->name('scan.qr.confirm');
});

// ============================================
// HOLIDAYS
// ============================================

Route::prefix('holidays')->group(function () {
    Route::get('/{year}', [HolidayController::class, 'getHolidays']);
    Route::get('/check/{date}', [HolidayController::class, 'checkDate']);
    Route::get('/month/current', [HolidayController::class, 'getCurrentMonthHolidays']);
});

// ============================================
// AUTH PROTECTED ROUTES
// ============================================

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('students', StudentController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('halls', HallController::class);
    Route::resource('days', DayController::class);
    Route::resource('lecturer-groups', LecturerGroupController::class);
    Route::resource('student-timetables', StudentTimetableController::class);

    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/children', [ParentDashboardController::class, 'children'])->name('children');
        Route::get('/children/{child}', [ParentDashboardController::class, 'childDetail'])->name('children.detail');
        Route::get('/attendance', [ParentDashboardController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/calendar', [ParentDashboardController::class, 'attendanceCalendar'])->name('attendance.calendar');
        Route::get('/attendance/{child}', [ParentDashboardController::class, 'childAttendance'])->name('attendance.child');
        Route::get('/notifications', [ParentDashboardController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [ParentDashboardController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [ParentDashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        Route::get('/payment', [ParentDashboardController::class, 'payment'])->name('payment');
        Route::get('/payment/{invoice}', [ParentDashboardController::class, 'paymentDetail'])->name('payment.detail');
        Route::post('/payment/{invoice}/pay', [ParentDashboardController::class, 'processPayment'])->name('payment.process');
        Route::get('/fine', [ParentDashboardController::class, 'fine'])->name('fine');
        Route::get('/fine/{fine}', [ParentDashboardController::class, 'fineDetail'])->name('fine.detail');
        Route::post('/fine/{fine}/pay', [ParentDashboardController::class, 'payFine'])->name('fine.pay');
        Route::get('/profile', [ParentDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [ParentDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ParentDashboardController::class, 'updatePassword'])->name('profile.password');
        Route::get('/settings', [ParentDashboardController::class, 'settings'])->name('settings');
        Route::put('/settings', [ParentDashboardController::class, 'updateSettings'])->name('settings.update');
    });

    Route::resource('parents', ParentController::class);
    Route::resource('children', ChildController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('classrooms', ClassroomController::class);

    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance/calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
    Route::get('/attendance/data', [AttendanceController::class, 'getData'])->name('attendance.data');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    Route::get('/qr-code', function () {
        return view('admin.qr-code');
    })->name('qr.code');
});

// ============================================
// REMINDER ROUTES
// ============================================

Route::middleware(['auth'])->group(function () {
    Route::post('/api/send-reminder', function (Request $request) {
        $request->validate([
            'date' => 'required|date',
            'child_ids' => 'nullable|array',
        ]);

        $date = $request->date;
        $childIds = $request->child_ids ?? [];

        if (empty($childIds)) {
            $attendances = App\Models\Attendance::whereDate('date', $date)->get();
            $childIds = $attendances->pluck('child_id')->unique()->toArray();
        }

        $children = Child::whereIn('id', $childIds)->get();
        $count = $children->count();

        return response()->json([
            'success' => true,
            'message' => "Reminder sent to {$count} child(ren) for {$date}",
            'data' => [
                'date' => $date,
                'children' => $children->pluck('name'),
                'count' => $count
            ]
        ]);
    })->name('api.send.reminder');

    Route::post('/api/send-bulk-reminder', function (Request $request) {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $attendances = App\Models\Attendance::whereDate('date', $date)->get();
        $childIds = $attendances->pluck('child_id')->unique()->toArray();
        $children = Child::whereIn('id', $childIds)->get();
        $count = $children->count();

        return response()->json([
            'success' => true,
            'message' => "Bulk reminder sent to {$count} child(ren) for {$date}",
            'data' => [
                'date' => $date,
                'children' => $children->pluck('name'),
                'count' => $count
            ]
        ]);
    })->name('api.send.bulk.reminder');
});

require __DIR__.'/auth.php';