<?php

use App\Http\Controllers\Api\QRController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

// Telegram Webhook
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);

Route::prefix('qr')->group(function () {
    Route::post('/generate', [QRController::class, 'generate'])->name('api.qr.generate');
    Route::post('/validate', [QRController::class, 'validateQR'])->name('api.qr.validate');
    Route::post('/attendance', [QRController::class, 'processAttendance'])->name('api.qr.attendance');
    Route::get('/child/{qr_data}', [QRController::class, 'getChildByQR'])->name('api.qr.child');
});