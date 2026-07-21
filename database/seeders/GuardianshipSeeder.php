<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuardianshipSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('guardianships')->truncate();

        // Detect actual IDs at runtime
        $mainParents   = DB::table('users')->where('role', 'parent1')->orderBy('id')->pluck('id')->toArray();
        $secondParents = DB::table('users')->where('role', 'parent2')->orderBy('id')->pluck('id')->toArray();
        $guardians     = DB::table('users')->where('role', 'guardian')->orderBy('id')->pluck('id')->toArray();
        $childIds      = DB::table('children')->orderBy('id')->pluck('id')->toArray();

        // Every child gets all 3 guardians — pick from pool evenly
        $count = 0;
        foreach ($childIds as $i => $childId) {
            $main   = $mainParents[$i % count($mainParents)];
            $second = $secondParents[$i % count($secondParents)];
            $guard  = $guardians[$i % count($guardians)];

            DB::table('guardianships')->insert([
                ['child_id' => $childId, 'user_id' => $main,   'relationship' => 'main_parent',   'is_emergency_contact' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['child_id' => $childId, 'user_id' => $second, 'relationship' => 'second_parent', 'is_emergency_contact' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['child_id' => $childId, 'user_id' => $guard,  'relationship' => 'guardian',      'is_emergency_contact' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $count += 3;
        }
        $this->command->info('  ✓ guardianships: ' . $count);
    }
}
