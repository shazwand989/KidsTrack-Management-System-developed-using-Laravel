<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('classrooms')->truncate();

        // Format: [name, code, age_group, min_age, max_age, capacity, color]
        $classrooms = [
            ['Ceria 1 (2 Tahun)', 'C1', '2 Tahun', 1, 2,  15, '#45B7D1'],
            ['Ceria 2 (3 Tahun)', 'C2', '3 Tahun', 2, 3,  15, '#FF6B6B'],
            ['Bestari (4 Tahun)', 'B1', '4 Tahun', 3, 4,  15, '#4ECDC4'],
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
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
