<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DayController extends Controller
{
    public function index()
    {
        $days = Day::all();
        return view('days.index', compact('days'));
    }

    public function create()
    {
        return view('days.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Only valid English day names are accepted
            // Prevent duplicate day names
            'day_name' => [
                'required',
                'string',
                Rule::in([
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday',
                ]),
                'unique:days,day_name',
            ],
        ], [
            'day_name.required' => 'The day name is required.',
            'day_name.in' => 'Please select a valid day name: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, or Sunday.',
            'day_name.unique' => 'This day already exists.',
        ]);

        Day::create($validated);

        return redirect()->route('days.index')
            ->with('success', 'Day created successfully!');
    }

    public function show(Day $day)
    {
        return view('days.show', compact('day'));
    }

    public function edit(Day $day)
    {
        return view('days.edit', compact('day'));
    }

    public function update(Request $request, Day $day)
    {
        $validated = $request->validate([
            'day_name' => [
                'required',
                'string',
                Rule::in([
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday',
                ]),
                'unique:days,day_name,' . $day->id,
            ],
        ], [
            'day_name.required' => 'The day name is required.',
            'day_name.in' => 'Please select a valid day name: Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, or Sunday.',
            'day_name.unique' => 'This day already exists.',
        ]);

        $day->update($validated);

        return redirect()->route('days.index')
            ->with('success', 'Day updated successfully!');
    }

    public function destroy(Day $day)
    {
        $day->delete();

        return redirect()->route('days.index')
            ->with('success', 'Day deleted successfully!');
    }
}