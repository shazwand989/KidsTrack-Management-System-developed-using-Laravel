<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendance')->truncate();

        $records = [
            [
                'child_id'     => 1,
                'user_id'      => 10,
                'date'         => '2026-07-21',
                'status'       => 'present',
                'checkin_time' => '07:15:00',
                'drop_off_by'  => 'Norazila binti Mahmud',
                'is_verified'  => 1,
            ],
            [
                'child_id'     => 2,
                'user_id'      => 10,
                'date'         => '2026-07-21',
                'status'       => 'present',
                'checkin_time' => '07:15:00',
                'drop_off_by'  => 'Norazila binti Mahmud',
                'is_verified'  => 1,
            ],
            [
                'child_id'     => 3,
                'user_id'      => 16,
                'date'         => '2026-07-21',
                'status'       => 'late',
                'checkin_time' => '08:05:00',
                'drop_off_by'  => 'Rosnani binti Shuib',
                'is_verified'  => 1,
                'late_reason'  => 'Kesesakan lalu lintas',
            ],
        ];

        foreach ($records as $rec) {
            DB::table('attendance')->insert(array_merge($rec, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('  ✓ attendance: ' . count($records));
    }
}
