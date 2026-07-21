<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teachers')->truncate();

        // Format: [name, position, age, phone, email, classroom_id, join_date]
        $teachers = [
            ['Hasanatun Nasuha binti Md Azlee', 'Guru Besar', '35', '01133458141', 'hasanatun@taskakids.com', 1, '2025-01-02'],
            ['Nur Fatimah binti Azmi',          'Guru Kanan','29', '0137789901',  'fatimah@taskakids.com',  2, '2025-03-15'],
        ];

        $this->insertTeachers($teachers);
        $this->command->info('  ✓ teachers: ' . count($teachers));
    }

    private function insertTeachers(array $teachers): void
    {
        foreach ($teachers as $t) {
            DB::table('teachers')->insert([
                'name'         => $t[0],
                'position'     => $t[1],
                'age'          => $t[2],
                'phone'        => $t[3],
                'email'        => $t[4],
                'classroom_id' => $t[5],
                'status'       => 'active',
                'join_date'    => $t[6],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
