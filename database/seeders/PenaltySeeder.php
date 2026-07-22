<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenaltySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('late_checkout_penalties')->truncate();
        DB::table('penalty_settings')->truncate();

        // ---- Penalty Settings ----
        DB::table('penalty_settings')->insert([
            ['key' => 'enabled',             'value' => 'true'],
            ['key' => 'grace_period',        'value' => '10'],
            ['key' => 'penalty_amount',      'value' => '20.00'],
            ['key' => 'toyyibpay_mode',      'value' => 'sandbox'],
            ['key' => 'toyyibpay_category',  'value' => '1h3x8o4a'],
            ['key' => 'toyyibpay_secret',    'value' => 'wibp6oak-4iwy-ocio-l65c-oletr03exv6e'],
            ['key' => 'callback_url',        'value' => rtrim(config('app.url'), '/') . '/api/penalty/callback'],
            ['key' => 'return_url',          'value' => rtrim(config('app.url'), '/') . '/parent/penalties'],
        ]);

        // ---- Generate penalties from seeded attendance ----
        // Get all attendance with a checkout_time + classroom schedule
        $attendances = DB::table('attendance')
            ->whereNotNull('checkout_time')
            ->join('children', 'attendance.child_id', '=', 'children.id')
            ->join('classrooms', 'children.classroom_id', '=', 'classrooms.id')
            ->select(
                'attendance.id as attendance_id',
                'attendance.child_id',
                'attendance.date',
                'attendance.checkout_time',
                'attendance.user_id',
                'classrooms.end_time'
            )
            ->get();

        $count = 0;
        $grace = 10; // minutes (must match the setting above)
        $penaltyAmount = 20.00;

        foreach ($attendances as $att) {
            if (!$att->end_time || !$att->checkout_time) continue;

            // Parse times
            $endParts = explode(':', $att->end_time);
            $endMinutes = (int)$endParts[0] * 60 + (int)($endParts[1] ?? 0);

            $checkoutParts = explode(':', $att->checkout_time);
            $checkoutMinutes = (int)$checkoutParts[0] * 60 + (int)($checkoutParts[1] ?? 0);

            $lateMinutes = $checkoutMinutes - $endMinutes;

            // Only create penalty if late beyond grace period
            if ($lateMinutes <= $grace) continue;

            DB::table('late_checkout_penalties')->insert([
                'attendance_id'     => $att->attendance_id,
                'child_id'          => $att->child_id,
                'parent_id'         => $att->user_id,
                'date'              => $att->date,
                'scheduled_checkout' => $att->end_time,
                'actual_checkout'   => $att->checkout_time,
                'late_minutes'      => $lateMinutes,
                'grace_period'      => $grace,
                'penalty_amount'    => $penaltyAmount,
                'payment_status'    => 'pending',
                'created_by'        => $att->user_id,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $count++;
        }

        $this->command->info('  ✓ penalty_settings: 8 records');
        $this->command->info('  ✓ late_checkout_penalties: ' . $count . ' records');
    }
}
