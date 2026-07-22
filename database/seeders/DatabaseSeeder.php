<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeders are called in dependency order:
     *   users & classrooms first  →  teachers, children  →  guardianships  →  attendance
     *   timer_settings is independent.
     */
    public function run(): void
    {
        $this->command->info('Seeding normalized KidsTrack...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->call([
            UserSeeder::class,
            ClassroomSeeder::class,
            TeacherSeeder::class,
            ChildSeeder::class,
            GuardianshipSeeder::class,
            AttendanceSeeder::class,
            PenaltySeeder::class,
            TimerSettingSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Done! Database normalized.');
    }
}
