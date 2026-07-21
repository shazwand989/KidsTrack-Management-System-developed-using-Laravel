<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('classrooms')->truncate();

        // Format: [name, code, age_group, min_age, max_age, capacity, color, start_time, end_time]
        $classrooms = [
            ['Ceria 1 (2 Tahun)', 'C1', '2 Tahun', 1, 2,  35, '#45B7D1', '08:00:00', '12:00:00'],
            ['Ceria 2 (3 Tahun)', 'C2', '3 Tahun', 2, 3,  35, '#FF6B6B', '08:00:00', '17:00:00'],
            ['Bestari (4 Tahun)', 'B1', '4 Tahun', 3, 4,  35, '#4ECDC4', '08:00:00', '17:00:00'],
        ];

        $this->insertClassrooms($classrooms);
        $this->command->info('  ✓ classrooms: ' . count($classrooms));
    }

    private function insertClassrooms(array $classrooms): void
    {
        foreach ($classrooms as $c) {
            DB::table('classrooms')->insert([
                'name'       => $c[0],
                'code'       => $c[1],
                'age_group'  => $c[2],
                'min_age'    => $c[3],
                'max_age'    => $c[4],
                'capacity'   => $c[5],
                'color'      => $c[6],
                'start_time' => $c[7],
                'end_time'   => $c[8],
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
