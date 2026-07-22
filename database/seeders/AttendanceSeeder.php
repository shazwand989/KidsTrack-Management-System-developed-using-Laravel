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

        // Build classroom schedule lookup: child_id => end_time
        $classSchedules = DB::table('children')
            ->join('classrooms', 'children.classroom_id', '=', 'classrooms.id')
            ->whereIn('children.id', $childIds)
            ->select('children.id as child_id', 'classrooms.start_time', 'classrooms.end_time')
            ->get()
            ->keyBy('child_id');

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
                    // Check-in: pick a random minute within first 30 min of the hour
                    $checkinHour = $status === 'late' ? '08' : '07';
                    $checkinMin  = str_pad(mt_rand(0, 30), 2, '0', STR_PAD_LEFT);
                    $record['checkin_time'] = "$checkinHour:$checkinMin:00";

                    // Checkout based on classroom end_time
                    $schedule = $classSchedules->get($childId);
                    if ($schedule && $schedule->end_time) {
                        // Parse classroom end_time (e.g. "17:00:00" or "17:00")
                        $endParts = explode(':', $schedule->end_time);
                        $endHour  = (int) $endParts[0];
                        $endMin   = (int) ($endParts[1] ?? 0);

                        // Checkout within ±10 minutes of scheduled end time
                        $offset = mt_rand(-10, 10);
                        $totalMin = ($endHour * 60 + $endMin + $offset);
                        $coHour = str_pad(intdiv($totalMin, 60), 2, '0', STR_PAD_LEFT);
                        $coMin  = str_pad($totalMin % 60, 2, '0', STR_PAD_LEFT);
                        $record['checkout_time'] = "$coHour:$coMin:00";
                    } else {
                        // Fallback: default 17:00 checkout
                        $record['checkout_time'] = '17:00:00';
                    }
                    $record['status'] = $status === 'late' ? 'late_checkout' : 'checkout';
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
