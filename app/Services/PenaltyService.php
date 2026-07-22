<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\LateCheckoutPenalty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PenaltyService
{
    /**
     * Get penalty settings with defaults.
     */
    public function getSettings(): array
    {
        $settings = \App\Models\PenaltySetting::pluck('value', 'key')->toArray();

        return [
            'enabled'            => $settings['enabled'] ?? 'false',
            'grace_period'       => (int) ($settings['grace_period'] ?? 10),
            'penalty_amount'     => (float) ($settings['penalty_amount'] ?? 1.00),
            'toyyibpay_mode'     => $settings['toyyibpay_mode'] ?? 'sandbox',
            'toyyibpay_category' => $settings['toyyibpay_category'] ?? '',
            'toyyibpay_secret'   => $settings['toyyibpay_secret'] ?? '',
            'callback_url'       => $settings['callback_url'] ?? url('/api/penalty/callback'),
            'return_url'         => $settings['return_url'] ?? url('/parent/penalties'),
        ];
    }

    /**
     * Calculate late minutes and determine if penalty applies.
     * Returns null if no penalty needed, or penalty data if applicable.
     */
    public function calculate(Attendance $attendance): ?array
    {
        $settings = $this->getSettings();

        if ($settings['enabled'] !== 'true') return null;

        $child = $attendance->child;
        if (!$child || !$child->classroom) return null;

        $classEnd = $child->classroom->end_time ?? '17:00:00';
        $classEndCarbon = Carbon::parse($classEnd);
        $checkoutTime = $attendance->checkout_time;

        if (!$checkoutTime) return null;

        $checkoutCarbon = $this->normalizeTime($checkoutTime);
        $lateMinutes = (int) round($classEndCarbon->diffInMinutes($checkoutCarbon, false));

        // Not late or within grace period
        if ($lateMinutes <= $settings['grace_period']) {
            return null; // No penalty
        }

        $penaltyAmount = $settings['penalty_amount'];

        return [
            'late_minutes'      => $lateMinutes,
            'grace_period'      => $settings['grace_period'],
            'penalty_amount'    => $penaltyAmount,
            'scheduled_checkout' => $classEndCarbon->format('H:i'),
            'actual_checkout'   => $checkoutCarbon->format('H:i'),
            'needs_payment'     => $penaltyAmount > 0,
        ];
    }

    /**
     * Create a penalty record.
     */
    public function createPenalty(Attendance $attendance, array $calc, int $parentId): LateCheckoutPenalty
    {
        return LateCheckoutPenalty::create([
            'attendance_id'     => $attendance->id,
            'child_id'          => $attendance->child_id,
            'parent_id'         => $parentId,
            'date'              => $attendance->date,
            'scheduled_checkout' => $calc['scheduled_checkout'],
            'actual_checkout'   => $calc['actual_checkout'],
            'late_minutes'      => $calc['late_minutes'],
            'grace_period'      => $calc['grace_period'],
            'penalty_amount'    => $calc['penalty_amount'],
            'payment_status'    => 'pending',
            'created_by'        => Auth::id() ?? $parentId,
        ]);
    }

    private function normalizeTime(Carbon|string $time): Carbon
    {
        $carbon = $time instanceof Carbon ? $time->copy() : Carbon::parse($time);
        return Carbon::now('Asia/Kuala_Lumpur')->startOfDay()
            ->setTime($carbon->hour, $carbon->minute, $carbon->second);
    }
}
