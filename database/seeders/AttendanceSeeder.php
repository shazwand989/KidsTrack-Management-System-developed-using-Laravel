<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendance')->truncate();

        $mainParents = DB::table('users')->where('role', 'parent1')->orderBy('id')->pluck('id')->toArray();
        $childIds    = DB::table('children')->orderBy('id')->pluck('id')->toArray();
        $userNames   = DB::table('users')->where('role', 'parent1')->orderBy('id')->pluck('name')->toArray();

        $statuses = ['present', 'present', 'present', 'present', 'late', 'absent']; // weighted
        $reasons  = ['Kesesakan lalu lintas', 'Kereta rosak', 'Hujan lebat', 'Anak tidak sihat'];

        $count = 0;
        // 3 days of attendance: today, yesterday, day before
        $dates = [now()->toDateString(), now()->subDay()->toDateString(), now()->subDays(2)->toDateString()];

        foreach ($childIds as $i => $childId) {
            $parentIdx = $i % count($mainParents);

            foreach ($dates as $date) {
                $status = $statuses[array_rand($statuses)];
                $record = [
                    'child_id'     => $childId,
                    'user_id'      => $mainParents[$parentIdx],
                    'date'         => $date,
                    'status'       => $status,
                    'drop_off_by'  => $userNames[$parentIdx] ?? 'Ibu',
                    'is_verified'  => $status !== 'absent' ? 1 : 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                if ($status === 'present' || $status === 'late') {
                    $hour   = $status === 'late' ? '08' : '07';
                    $minute = str_pad(mt_rand(0, 30), 2, '0', STR_PAD_LEFT);
                    $record['checkin_time'] = "$hour:$minute:00";
                }

                if ($status === 'late') {
                    $record['late_reason'] = $reasons[array_rand($reasons)];
                }

                DB::table('attendance')->insert($record);
                $count++;
            }
        }

        $this->command->info('  ✓ attendance: ' . $count . ' records (3 days × ' . count($childIds) . ' children)');
    }
}
