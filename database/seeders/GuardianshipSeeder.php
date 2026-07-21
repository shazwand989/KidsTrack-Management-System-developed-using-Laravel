<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuardianshipSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('guardianships')->truncate();

        // [child_id, user_id, relationship, is_emergency]
        $links = [
            [1, 10, 'main_parent', 1], [1, 11, 'second_parent', 0], [1, 13, 'guardian', 1],
            [2, 10, 'main_parent', 1], [2, 11, 'second_parent', 0], [2, 13, 'guardian', 1],
            [3, 16, 'main_parent', 1], [3, 17, 'second_parent', 0], [3, 18, 'guardian', 1],
            [4, 19, 'main_parent', 1], [4, 20, 'second_parent', 0], [4, 21, 'guardian', 1],
            [5, 19, 'main_parent', 1], [5, 20, 'second_parent', 0], [5, 21, 'guardian', 1],
            [6, 22, 'main_parent', 1], [6, 23, 'second_parent', 0], [6, 24, 'guardian', 1],
            [7, 22, 'main_parent', 1], [7, 23, 'second_parent', 0], [7, 24, 'guardian', 1],
            [8, 25, 'main_parent', 1], [8, 26, 'second_parent', 0], [8, 27, 'guardian', 1],
            [9, 25, 'main_parent', 1], [9, 26, 'second_parent', 0], [9, 27, 'guardian', 1],
            [10, 25, 'main_parent', 1], [10, 26, 'second_parent', 0], [10, 27, 'guardian', 1],
            [13, 34, 'main_parent', 1],
            [14, 34, 'main_parent', 1],
        ];

        foreach ($links as $l) {
            DB::table('guardianships')->insert([
                'child_id'            => $l[0],
                'user_id'             => $l[1],
                'relationship'        => $l[2],
                'is_emergency_contact'=> $l[3],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        $this->command->info('  ✓ guardianships: ' . count($links));
    }
}
