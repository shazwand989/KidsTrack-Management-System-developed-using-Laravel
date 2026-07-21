<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('classrooms')->truncate();

        DB::table('classrooms')->insert([
            [
                'id'         => 1,
                'name'       => 'Ceria 1 (2 Tahun)',
                'code'       => 'C1',
                'age_group'  => '2 Tahun',
                'min_age'    => 1,
                'max_age'    => 2,
                'capacity'   => 15,
                'color'      => '#45B7D1',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'Ceria 2 (3 Tahun)',
                'code'       => 'C2',
                'age_group'  => '3 Tahun',
                'min_age'    => 2,
                'max_age'    => 3,
                'capacity'   => 15,
                'color'      => '#FF6B6B',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'name'       => 'Bestari (4 Tahun)',
                'code'       => 'B1',
                'age_group'  => '4 Tahun',
                'min_age'    => 3,
                'max_age'    => 4,
                'capacity'   => 15,
                'color'      => '#4ECDC4',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('  ✓ classrooms: 3');
    }
}
