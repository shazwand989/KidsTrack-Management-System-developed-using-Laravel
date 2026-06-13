<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function index()
    {
        $halls = Hall::all();
        return view('halls.index', compact('halls'));
    }

    public function create()
    {
        return view('halls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Example accepted: DK1, Hall A, Lab 2, Bilik Kuliah 3
            // Must contain at least one letter
            // Allows letters, numbers, spaces, dot, dash, slash, and brackets
            'lecture_hall_name' => [
                'required',
                'string',
                'max:100',
                'unique:halls,lecture_hall_name',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],

            // Example accepted: Block A, Level 2, Main Campus
            // Must contain at least one letter
            // Allows letters, numbers, spaces, dot, dash, slash, and brackets
            'lecture_hall_place' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],
        ], [
            'lecture_hall_name.required' => 'The lecture hall name is required.',
            'lecture_hall_name.unique' => 'This lecture hall name already exists.',
            'lecture_hall_name.regex' => 'Lecture hall name must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',

            'lecture_hall_place.required' => 'The lecture hall place is required.',
            'lecture_hall_place.regex' => 'Lecture hall place must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',
        ]);

        Hall::create($validated);

        return redirect()->route('halls.index')
            ->with('success', 'Hall created successfully!');
    }

    public function show(Hall $hall)
    {
        return view('halls.show', compact('hall'));
    }

    public function edit(Hall $hall)
    {
        return view('halls.edit', compact('hall'));
    }

    public function update(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'lecture_hall_name' => [
                'required',
                'string',
                'max:100',
                'unique:halls,lecture_hall_name,' . $hall->id,
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],

            'lecture_hall_place' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],
        ], [
            'lecture_hall_name.required' => 'The lecture hall name is required.',
            'lecture_hall_name.unique' => 'This lecture hall name already exists.',
            'lecture_hall_name.regex' => 'Lecture hall name must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',

            'lecture_hall_place.required' => 'The lecture hall place is required.',
            'lecture_hall_place.regex' => 'Lecture hall place must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',
        ]);

        $hall->update($validated);

        return redirect()->route('halls.index')
            ->with('success', 'Hall updated successfully!');
    }

    public function destroy(Hall $hall)
    {
        $hall->delete();

        return redirect()->route('halls.index')
            ->with('success', 'Hall deleted successfully!');
    }
}