<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teachers')->truncate();

        DB::table('teachers')->insert([
            [
                'id'           => 1,
                'name'         => 'Hasanatun Nasuha binti Md Azlee',
                'position'     => 'Guru Besar',
                'age'          => 35,
                'phone'        => '011-3345 8141',
                'email'        => 'hasanatun@taskakids.com',
                'classroom_id' => 1,
                'status'       => 'active',
                'join_date'    => '2025-01-02',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id'           => 2,
                'name'         => 'Nur Fatimah binti Azmi',
                'position'     => 'Guru Kanan',
                'age'          => 29,
                'phone'        => '013-778 9901',
                'email'        => 'fatimah@taskakids.com',
                'classroom_id' => 2,
                'status'       => 'active',
                'join_date'    => '2025-03-15',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        $this->command->info('  ✓ teachers: 2');
    }
}
