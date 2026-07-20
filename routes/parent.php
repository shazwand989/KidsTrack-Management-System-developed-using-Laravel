<?php

use Illuminate\Support\Facades\Route;

// ============================================
// PARENT / GUARDIAN ROUTES
// ============================================
Route::middleware(['auth'])->prefix('parent')->name('parent.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])
        ->name('dashboard');

    // Children
    Route::get('/children', [\App\Http\Controllers\Parent\ChildrenController::class, 'index'])
        ->name('children.index');
    Route::get('/children/{child}', [\App\Http\Controllers\Parent\ChildrenController::class, 'show'])
        ->name('children.show');

    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Parent\AttendanceController::class, 'index'])
        ->name('attendance.index');
    Route::get('/attendance/calendar', [\App\Http\Controllers\Parent\AttendanceController::class, 'calendar'])
        ->name('attendance.calendar');
    Route::get('/attendance/calendar-data', [\App\Http\Controllers\Parent\AttendanceController::class, 'calendarData'])
        ->name('attendance.calendar-data');
    Route::get('/attendance/{child}', [\App\Http\Controllers\Parent\AttendanceController::class, 'childAttendance'])
        ->name('attendance.child');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Parent\ProfileController::class, 'index'])
        ->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Parent\ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Parent\ProfileController::class, 'updatePassword'])
        ->name('profile.password');
    Route::get('/settings', [\App\Http\Controllers\Parent\ProfileController::class, 'settings'])
        ->name('settings');

    // Notifications, Payment, Fine
    Route::get('/notifications', [\App\Http\Controllers\Parent\DashboardController::class, 'notifications'])
        ->name('notifications');
    Route::get('/payment', [\App\Http\Controllers\Parent\DashboardController::class, 'payment'])
        ->name('payment');
    Route::get('/fine', [\App\Http\Controllers\Parent\DashboardController::class, 'fine'])
        ->name('fine');
});
