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
        $children      = DB::table('children')->orderBy('id')->get();

        // Group children by insertion order — ChildSeeder creates 1-3 per family in sequence
        $families = [];
        $currentFamily = 0;
        $prevParent = null;

        // Since ChildSeeder creates kids per main parent, we infer families by cycling parents
        // Each family = same main parent, same second parent, same guardian for all its kids
        $childCount = $children->count();
        $familyIndex = 0;
        $childIndex = 0;

        // Distribute children into family groups (~2 per family average)
        $count = 0;
        foreach ($children as $child) {
            $idx = $familyIndex % count($mainParents);
            $main   = $mainParents[$idx];
            $second = $secondParents[$idx % count($secondParents)];
            $guard  = $guardians[$idx % count($guardians)];

            DB::table('guardianships')->insert([
                ['child_id' => $child->id, 'user_id' => $main,   'relationship' => 'main_parent',   'is_emergency_contact' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['child_id' => $child->id, 'user_id' => $second, 'relationship' => 'second_parent', 'is_emergency_contact' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['child_id' => $child->id, 'user_id' => $guard,  'relationship' => 'guardian',      'is_emergency_contact' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
            $count += 3;

            $childIndex++;
            // Move to next family every ~2 children
            if ($childIndex % 2 === 0) $familyIndex++;
        }

        $this->command->info('  ✓ guardianships: ' . $count . ' (families: ' . ($familyIndex + 1) . ')');
    }
}
