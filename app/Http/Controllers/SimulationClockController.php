<?php

namespace App\Http\Controllers;

use App\Models\SimulationClock;
use Illuminate\Http\Request;

class SimulationClockController extends Controller
{
    public function setting()
    {
        $clock = SimulationClock::getClock();
        return view('simulation.setting', compact('clock'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'simulation_time' => 'required|date',
            'morning_start' => 'required|date_format:H:i',
            'morning_end' => 'required|date_format:H:i',
            'evening_start' => 'required|date_format:H:i',
            'evening_end' => 'required|date_format:H:i',
        ]);

        $clock = SimulationClock::first();

        $data = [
            'simulation_time' => $request->simulation_time,
            'morning_start' => $request->morning_start . ':00',
            'morning_end' => $request->morning_end . ':00',
            'evening_start' => $request->evening_start . ':00',
            'evening_end' => $request->evening_end . ':00',
            'use_simulation' => $request->has('use_simulation') ? true : false,
        ];

        if ($clock) {
            $clock->update($data);
        } else {
            SimulationClock::create($data);
        }

        return redirect()->back()->with('success', '✅ Simulation settings updated successfully!');
    }

    public function dashboard()
    {
        $clock = SimulationClock::getClock();
        return view('simulation.dashboard', compact('clock'));
    }
}