<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SimulationClock;

class SimulationClockSeeder extends Seeder
{
    public function run(): void
    {
        SimulationClock::create([
            'simulation_time' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
