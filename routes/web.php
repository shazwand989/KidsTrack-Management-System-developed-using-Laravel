<?php

use Illuminate\Support\Facades\Route;

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

// Models
use App\Models\User;
use App\Models\Subject;
use App\Models\Hall;
use App\Models\Day;
use App\Models\LecturerGroup;
use App\Models\StudentTimetable;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('home', [
        'studentCount' => User::where('role', 'student')->count(),
        'subjectCount' => Subject::count(),
        'hallCount' => Hall::count(),
        'dayCount' => Day::count(),
        'lecturerGroupCount' => LecturerGroup::count(),
        'timetableCount' => StudentTimetable::count(),

        'recentStudents' => User::where('role', 'student')->latest()->take(4)->get(),

        'latestTimetables' => StudentTimetable::with(['user', 'subject', 'day'])
            ->latest()
            ->take(4)
            ->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('home');

/*
|--------------------------------------------------------------------------
| QR CODE SCANNING ROUTES (PUBLIC - NO AUTH REQUIRED)
| Letak ATAS sekali sebelum auth group untuk elak conflict
|--------------------------------------------------------------------------
*/

Route::prefix('attendance')->withoutMiddleware(['auth'])->group(function () {

    Route::get('/scan', fn() => redirect()->route('attendance.search'))
        ->name('attendance.landing');

    Route::get('/search', [AttendanceController::class, 'search'])
        ->name('attendance.search');

    Route::get('/search/results', [AttendanceController::class, 'searchResults'])
        ->name('attendance.search.results');

    Route::post('/child/{child}/verify', [AttendanceController::class, 'verifyPhone'])
        ->name('attendance.verify');

    Route::get('/child/{child}', [AttendanceController::class, 'childProfile'])
        ->name('attendance.child');

    Route::post('/checkin/{child}', [AttendanceController::class, 'processCheckin'])
        ->name('attendance.checkin.process');

    Route::post('/checkout/{child}', [AttendanceController::class, 'processCheckout'])
        ->name('attendance.checkout.process');

    Route::get('/status/{child}', [AttendanceController::class, 'getStatus'])
        ->name('attendance.status');

    Route::get('/status-all', [AttendanceController::class, 'getAllStatus'])
        ->name('attendance.status-all');

});

/*
|--------------------------------------------------------------------------
| AUTH PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Legacy / Existing System (Old)
    Route::resource('students', StudentController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('halls', HallController::class);
    Route::resource('days', DayController::class);
    Route::resource('lecturer-groups', LecturerGroupController::class);
    Route::resource('student-timetables', StudentTimetableController::class);

    // ============================================
    // MAIN SYSTEM (Nursery Management)
    // ============================================

    Route::resource('parents', ParentController::class);
    Route::resource('children', ChildController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('classrooms', ClassroomController::class);

    // ============================================
    // ATTENDANCE SYSTEM (Admin/Teacher - Auth Required)
    // ============================================
    Route::resource('attendance', AttendanceController::class);
    Route::get('/attendance-calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
    Route::get('/attendance/data', [AttendanceController::class, 'getData'])->name('attendance.data');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkin'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // ============================================
    // QR CODE PAGE (Admin - Auth Required)
    // ============================================
    Route::get('/qr-code', function () {
        return view('admin.qr-code');
    })->name('qr.code');

});

/*
|--------------------------------------------------------------------------
| HOLIDAYS API
|--------------------------------------------------------------------------
*/

Route::prefix('holidays')->group(function () {
    Route::get('/{year}', [HolidayController::class, 'getHolidays']);
    Route::get('/check/{date}', [HolidayController::class, 'checkDate']);
    Route::get('/month/current', [HolidayController::class, 'getCurrentMonthHolidays']);
});

/*
|--------------------------------------------------------------------------
| AUTH (BREEZE)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';