<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SimulationClock;

class SimulationClockSeeder extends Seeder
{
    public function run(): void
    {
        SimulationClock::create([

            'simulation_time'=>'2026-07-19 07:30:00'

        ]);
    }
}