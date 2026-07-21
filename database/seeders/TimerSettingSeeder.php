<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimerSettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('timer_settings')->truncate();

        $days = [
            'Isnin (Monday)',
            'Selasa (Tuesday)',
            'Rabu (Wednesday)',
            'Khamis (Thursday)',
            'Jumaat (Friday)',
        ];

        foreach ($days as $day) {
            DB::table('timer_settings')->insert([
                'day_name'        => $day,
                'morning_start'   => '07:00:00',
                'morning_end'     => '07:30:00',
                'afternoon_start' => '12:00:00',
                'afternoon_end'   => '12:30:00',
                'evening_start'   => '17:00:00',
                'evening_end'     => '17:30:00',
                'is_active'       => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        $this->command->info('  ✓ timer_settings: ' . count($days));
    }
}
