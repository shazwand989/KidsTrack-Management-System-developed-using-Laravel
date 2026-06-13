<?php

namespace App\Http\Controllers;

use App\Models\LecturerGroup;
use Illuminate\Http\Request;

class LecturerGroupController extends Controller
{
    public function index()
    {
        $lecturerGroups = LecturerGroup::all();

        return view('lecturer_groups.index', compact('lecturerGroups'));
    }

    public function create()
    {
        return view('lecturer_groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Example accepted: CS251, RCDCS251A, Group A
            // Must contain at least one letter
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:lecturer_groups,name',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],

            // Example accepted: 1, 2, 3, Part 4
            // Allows number only because part usually uses number
            'part' => [
                'required',
                'string',
                'max:50',
                'regex:/^(?=.*[0-9A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],
        ], [
            'name.required' => 'The group name is required.',
            'name.unique' => 'This group name already exists.',
            'name.regex' => 'Group name must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',

            'part.required' => 'The part is required.',
            'part.regex' => 'Part must contain letters or numbers and can include spaces, dot, dash, slash, or brackets.',
        ]);

        LecturerGroup::create($validated);

        return redirect()->route('lecturer-groups.index')
            ->with('success', 'Lecturer group created successfully!');
    }

    public function show(LecturerGroup $lecturer_group)
    {
        return view('lecturer_groups.show', compact('lecturer_group'));
    }

    public function edit(LecturerGroup $lecturer_group)
    {
        return view('lecturer_groups.edit', compact('lecturer_group'));
    }

    public function update(Request $request, LecturerGroup $lecturer_group)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:lecturer_groups,name,' . $lecturer_group->id,
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],

            'part' => [
                'required',
                'string',
                'max:50',
                'regex:/^(?=.*[0-9A-Za-z])[A-Za-z0-9\s\.\-\/\(\)]+$/'
            ],
        ], [
            'name.required' => 'The group name is required.',
            'name.unique' => 'This group name already exists.',
            'name.regex' => 'Group name must contain at least one letter and can include numbers, spaces, dot, dash, slash, or brackets.',

            'part.required' => 'The part is required.',
            'part.regex' => 'Part must contain letters or numbers and can include spaces, dot, dash, slash, or brackets.',
        ]);

        $lecturer_group->update($validated);

        return redirect()->route('lecturer-groups.index')
            ->with('success', 'Lecturer group updated successfully!');
    }

    public function destroy(LecturerGroup $lecturer_group)
    {
        $lecturer_group->delete();

        return redirect()->route('lecturer-groups.index')
            ->with('success', 'Lecturer group deleted successfully!');
    }
}