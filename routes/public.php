<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controllers for public routes
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\QRScanController;
use App\Http\Controllers\HolidayController;

// ============================================
// GLOBAL BINDING: {child} accepts hashed OR plain ID
// ============================================
Route::bind('child', function ($value) {
    $id = \App\Helper\KioskHelper::decodeId($value);
    if ($id) {
        return \App\Models\Child::findOrFail($id);
    }
    if (is_numeric($value)) {
        return \App\Models\Child::findOrFail($value);
    }
    abort(404);
});

// ============================================
// SIMULATION CLOCK API
// ============================================
Route::get('/api/simulation-time', function () {
    return response()->json([
        'time'        => \App\Models\SimulationClock::getFormattedTime(),
        'date'        => \App\Models\SimulationClock::getFormattedDate(),
        'timestamp'   => \App\Models\SimulationClock::getCurrentTime(),
        'slot'        => \App\Models\SimulationClock::getCurrentSlot(),
        'status'      => \App\Models\SimulationClock::getStatus(),
        'timer'       => \App\Models\SimulationClock::getTimerForToday(),
        'use_simulation' => \App\Models\SimulationClock::first()->use_simulation ?? false,
    ]);
})->name('api.simulation.time');

// ============================================
// ATTENDANCE SCAN (PUBLIC - NO AUTH)
// ============================================
Route::prefix('attendance-scan')->name('attendance-scan.')->group(function () {
    Route::get('/scan', fn () => redirect()->route('attendance-scan.search'))->name('landing');
    Route::get('/search', [AttendanceController::class, 'search'])->name('search');
    Route::get('/search/results', [AttendanceController::class, 'searchResults'])->name('search.results');
    Route::post('/verify-parent', [AttendanceController::class, 'verifyParent'])->name('verify-parent');
    Route::post('/bulk-checkin', [AttendanceController::class, 'bulkCheckinScan'])->name('bulk-checkin');
    Route::post('/bulk-checkout', [AttendanceController::class, 'bulkCheckoutScan'])->name('bulk-checkout');
    Route::post('/child/{child}/verify', [AttendanceController::class, 'verifyPhone'])->name('verify');
    Route::get('/child/{child}', [AttendanceController::class, 'childProfile'])->name('child');
    Route::post('/checkin/{child}', [AttendanceController::class, 'processCheckin'])->name('checkin.process');
    Route::post('/checkout/{child}', [AttendanceController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/status/{child}', [AttendanceController::class, 'getStatus'])->name('status');
    Route::get('/status-all', [AttendanceController::class, 'getAllStatus'])->name('status-all');
});

// ============================================
// SCAN QR - PUBLIC PAGES
// ============================================
Route::get('/scan-qr', function () {
    return view('parent.scan-qr');
})->name('scan.qr.page');

Route::get('/scan-qr/check', function () {
    return response()->json([
        'logged_in' => Auth::check(),
    ]);
})->name('scan.qr.check');

Route::get('/scan-qr/result/{qr_data}', function ($qrData) {
    if (!Auth::check()) {
        return redirect()->route('login')->with('redirect_after_login', url('/scan-qr/result/' . $qrData));
    }

    $child = \App\Models\Child::where('qr_code', $qrData)
        ->with(['parent', 'classroom', 'attendances'])
        ->first();

    if (!$child) {
        return redirect()->route('kiosk.index')->with('error', 'QR Code tidak sah! Sila cuba lagi.');
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!in_array($user->role, ['admin', 'teacher'])) {
        $hasAccess = $user->children()->where('child_id', $child->id)->exists();
        if (!$hasAccess) {
            return redirect()->route('kiosk.index')->with('error', 'Anda tidak mempunyai akses ke anak ini!');
        }
    }

    return view('parent.scan-profile', compact('child', 'qrData'));
})->name('scan.qr.result');

Route::get('/scan-qr/{qr_code}', [QRScanController::class, 'show'])->name('scan.qr.show');

// ============================================
// TIMER SETTINGS (PUBLIC)
// ============================================
Route::get('/get-timer-settings', [QRScanController::class, 'getTimerSettings'])
    ->name('get.timer.settings.public');

// ============================================
// KIOSK ROUTES (PUBLIC - USED BY PARENTS/GUARDIANS)
// ============================================
Route::prefix('kiosk')->name('kiosk.')->group(function () {

    Route::get('/', [QRScanController::class, 'kiosk'])->name('index');
    Route::post('/check-access', [QRScanController::class, 'checkAccess'])->name('check.access');
    Route::get('/confirm-child/{child}', [QRScanController::class, 'confirmChild'])->name('confirm.child');

    // Add another child
    Route::get('/add-another/{child}', [\App\Http\Controllers\AddAnotherChildController::class, 'showAddAnother'])
        ->name('add.another');
    Route::post('/bulk-checkin', [\App\Http\Controllers\AddAnotherChildController::class, 'bulkCheckin'])
        ->name('bulk.checkin');

    // Checkin page
    Route::get('/checkin-page/{child}', [\App\Http\Controllers\CheckinController::class, 'showCheckinPage'])
        ->name('checkin.page');
    Route::post('/submit-attendance', [\App\Http\Controllers\CheckinController::class, 'submitAttendance'])
        ->name('submit.attendance');
    Route::post('/checkin-all', [\App\Http\Controllers\CheckinController::class, 'checkinAll'])
        ->name('checkin.all');
    Route::post('/checkout-all', [\App\Http\Controllers\CheckinController::class, 'checkoutAll'])
        ->name('checkout.all');
    Route::get('/checkout-landing', [\App\Http\Controllers\CheckinController::class, 'checkout'])
        ->name('checkout.landing');

    // Kiosk QRScanController routes
    Route::get('/status/{child}', [QRScanController::class, 'showStatus'])->name('status');
    Route::post('/scan', [QRScanController::class, 'handleKioskScan'])->name('scan');
    Route::post('/confirm', [QRScanController::class, 'confirmAttendance'])->name('confirm');
    Route::get('/attendance/{child}', [QRScanController::class, 'getAttendance'])->name('attendance');
    Route::get('/today', [QRScanController::class, 'getTodayAttendance'])->name('today');
    Route::get('/checkin/{child}', fn ($childId) => redirect()->route('kiosk.checkin.page', $childId))->name('checkin');
    Route::get('/checkout/{child}', fn ($childId) => redirect()->route('kiosk.checkin.page', $childId))->name('checkout');
    Route::post('/confirm-checkin', [QRScanController::class, 'confirmCheckin'])->name('confirm.checkin');
    Route::post('/confirm-checkout', [QRScanController::class, 'confirmCheckout'])->name('confirm.checkout');
    Route::post('/direct-checkout', [QRScanController::class, 'directCheckout'])->name('direct.checkout');
    Route::get('/child-profile/{child}', [QRScanController::class, 'showChildProfile'])->name('child.profile');

    // Timer settings
    Route::get('/get-timer-settings', [QRScanController::class, 'getTimerSettings'])->name('get.timer.settings');
    Route::post('/save-timer-settings', [QRScanController::class, 'saveTimerSettings'])->name('save.timer.settings');
    Route::post('/reset-timer-settings', [QRScanController::class, 'resetTimerSettings'])->name('reset.timer.settings');
});

// ============================================
// REDIRECT OLD ROUTES
// ============================================
Route::redirect('/parent/qr-code', '/kiosk', 301);
Route::redirect('/parent/qr-code/{child}', '/kiosk', 301);

// ============================================
// ATTENDANCE CALENDAR DATA (PUBLIC API)
// ============================================
Route::get('/attendance-calendar-data', [QRScanController::class, 'getCalendarData'])
    ->name('attendance.calendar.data');

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
            $monthHolidays = array_filter($data, function ($h) use ($month) {
                $date = new DateTime($h['date']);
                return (int) $date->format('m') == $month;
            });

            return response()->json([
                'success' => true,
                'data'    => array_values($monthHolidays),
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data'    => getLocalHolidays($year, $month),
                'source'  => 'local',
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => true,
            'data'    => getLocalHolidays($year, $month),
            'source'  => 'local',
        ]);
    }
})->name('api.holidays');

// ============================================
// HOLIDAYS (no auth middleware)
// ============================================
Route::prefix('holidays')->group(function () {
    Route::get('/{year}', [HolidayController::class, 'getHolidays']);
    Route::get('/check/{date}', [HolidayController::class, 'checkDate']);
    Route::get('/month/current', [HolidayController::class, 'getCurrentMonthHolidays']);
});

// ============================================
// LOCAL HOLIDAYS HELPER
// ============================================
function getLocalHolidays($year, $month)
{
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
        if ($dateObj && (int) $dateObj->format('m') == $month) {
            $holidays[] = [
                'date'      => $year . '-' . $date,
                'name'      => $name,
                'localName' => $name,
            ];
        }
    }

    return $holidays;
}
