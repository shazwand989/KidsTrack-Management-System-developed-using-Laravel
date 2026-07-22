<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Child;

use App\Models\StudentTimetable;
use Carbon\Carbon;

class AttendanceSummaryService
{
    /**
     * Get a complete attendance summary for a single attendance record.
     *
     * @return array{
     *   checkin: array{time: ?string, status: string, status_label: string, status_class: string, minutes_diff: ?int},
     *   checkout: array{time: ?string, status: string, status_label: string, status_class: string, minutes_diff: ?int},
     *   schedule: array{day: string, morning_start: string, morning_end: string, class_start: ?string, class_end: ?string, evening_end: string},
     *   summary: string
     * }
     */
    public function getAttendanceSummary(Attendance $attendance): array
    {
        $child = $attendance->child;
        $classroom = $child ? $child->classroom : null;
        $date = Carbon::parse($attendance->date);
        $dayName = $date->format('l');

        // Use classroom schedule exclusively
        $classStart = $classroom->start_time ?? '08:00';
        $classEnd   = $classroom->end_time   ?? '17:00';

        // --- CHECK-IN ANALYSIS --- (compare against classroom start time)
        $checkin = $this->analyzeCheckin($attendance, $classStart);

        // --- CHECK-OUT ANALYSIS --- (compare against classroom end time)
        $checkout = $this->analyzeCheckout($attendance, $classEnd, $classEnd);

        // --- OVERALL SUMMARY ---
        $summary = $this->buildSummary($checkin, $checkout);

        return [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'schedule' => [
                'day'          => $dayName,
                'morning_start' => $classStart,
                'morning_end'   => $classStart,
                'class_start'   => $classStart,
                'class_end'     => $classEnd,
                'evening_end'   => $classEnd,
            ],
            'summary' => $summary,
        ];
    }

    /**
     * Analyze check-in time against the morning schedule.
     */
    private function analyzeCheckin(Attendance $attendance, string $morningEnd): array
    {
        $checkinTime = $attendance->checkin_time
            ? $this->normalizeTime($attendance->checkin_time)
            : null;

        $morningEndCarbon = $this->normalizeTime($morningEnd);

        if (!$checkinTime) {
            // No check-in at all → Absent
            return [
                'time'         => null,
                'status'       => 'absent',
                'status_label' => 'Absent',
                'status_class' => 'red',
                'minutes_diff'  => null,
            ];
        }

        // Check-in exists → compare with morning_end
        if ($checkinTime->lte($morningEndCarbon)) {
            return [
                'time'         => $checkinTime->format('h:i A'),
                'status'       => 'on_time',
                'status_label' => 'On Time',
                'status_class' => 'green',
                'minutes_diff'  => 0,
            ];
        }

        // Late
        $minutesLate = (int) round(abs($checkinTime->diffInMinutes($morningEndCarbon)));

        return [
            'time'         => $checkinTime->format('h:i A'),
            'status'       => 'late',
            'status_label' => 'Late',
            'status_class' => 'red',
            'minutes_diff'  => $minutesLate,
        ];
    }

    /**
     * Analyze check-out time against the class/evening schedule.
     */
    private function analyzeCheckout(Attendance $attendance, string $classEnd, string $eveningEnd): array
    {
        $checkoutTime = $attendance->checkout_time
            ? $this->normalizeTime($attendance->checkout_time)
            : null;

        // Use the class end time as the official dismissal time
        $dismissalTime = $classEnd ?: $eveningEnd;
        $dismissalCarbon = $this->normalizeTime($dismissalTime);

        if (!$checkoutTime) {
            return [
                'time'         => null,
                'status'       => 'not_checked_out',
                'status_label' => 'Not Checked Out',
                'status_class' => 'yellow',
                'minutes_diff'  => null,
            ];
        }

        // 5-minute buffer for "on time"
        $buffer = 5;
        // diff from schedule(dismissal) to actual(checkout): positive = late, negative = early
        $diff = (int) round($dismissalCarbon->diffInMinutes($checkoutTime, false));

        if ($diff < -$buffer) {
            // Early: check-out before dismissal time (beyond buffer)
            $minutesEarly = abs($diff);
            return [
                'time'         => $checkoutTime->format('h:i A'),
                'status'       => 'early',
                'status_label' => 'Early',
                'status_class' => 'blue',
                'minutes_diff'  => $minutesEarly,
            ];
        }

        if ($diff <= $buffer) {
            // On Time: within buffer after dismissal
            return [
                'time'         => $checkoutTime->format('h:i A'),
                'status'       => 'on_time',
                'status_label' => 'On Time',
                'status_class' => 'green',
                'minutes_diff'  => 0,
            ];
        }

        // Late: check-out after dismissal time
        return [
            'time'         => $checkoutTime->format('h:i A'),
            'status'       => 'late',
            'status_label' => 'Late',
            'status_class' => 'red',
            'minutes_diff'  => $diff,
        ];
    }

    /**
     * Build a human-readable summary string.
     */
    private function buildSummary(array $checkin, array $checkout): string
    {
        $parts = [];

        // Check-in part
        if ($checkin['status'] === 'absent') {
            $parts[] = '❌ Absent today';
        } elseif ($checkin['status'] === 'on_time') {
            $parts[] = '✅ Arrived on time';
        } elseif ($checkin['status'] === 'late') {
            $parts[] = '⚠️ ' . self::formatDuration($checkin['minutes_diff']) . ' late';
        }

        // Check-out part
        if ($checkout['status'] === 'not_checked_out') {
            $parts[] = '— Not checked out yet';
        } elseif ($checkout['status'] === 'early') {
            $parts[] = '🔵 ' . self::formatDuration($checkout['minutes_diff']) . ' early';
        } elseif ($checkout['status'] === 'on_time') {
            $parts[] = '✅ Picked up on time';
        } elseif ($checkout['status'] === 'late') {
            $parts[] = '⚠️ ' . self::formatDuration($checkout['minutes_diff']) . ' late pickup';
        }

        return implode(' &middot; ', $parts);
    }

    /**
     * Format duration: "456 min" → "7h 36m", "45 min" stays "45m".
     */
    public static function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
    }

    /**
     * Normalize a time value (Carbon or string) to today's date at that time.
     * MySQL TIME fields lack a date, so Carbon may use 1970-01-01.
     * This ensures both times share the same date for accurate comparison.
     */
    private function normalizeTime($time): Carbon
    {
        $carbon = $time instanceof Carbon ? $time->copy() : Carbon::parse($time);
        // Force both times to the same reference date for accurate comparison
        $ref = Carbon::now('Asia/Kuala_Lumpur')->startOfDay();
        return $ref->setTime($carbon->hour, $carbon->minute, $carbon->second);
    }

    /**
     * Parse a time value that could be "HH:MM" or "HH:MM:SS".
     */
    private function parseTime(?string $time): string
    {
        if (!$time) {
            return '00:00';
        }
        return substr($time, 0, 5); // "HH:MM"
    }
}
