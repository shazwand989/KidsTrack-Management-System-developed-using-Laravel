<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Example accepted: CSC301, ITT626, CTU554
            // Must start with letters and end with numbers
            'subject_code' => [
                'required',
                'string',
                'max:10',
                'unique:subjects,subject_code',
                'regex:/^[A-Za-z]{2,5}[0-9]{2,4}$/'
            ],

            // Subject name cannot be numbers only
            'subject_name' => [
                'required',
                'string',
                'max:100',
                'not_regex:/^[0-9]+$/'
            ],

            // Lecturer name must contain at least one letter
            // Allowed: letters, spaces, dot, dash, apostrophe
            'lecturer_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z\s\.\'\-]+$/'
            ],
        ], [
            'subject_code.required' => 'The subject code is required.',
            'subject_code.unique' => 'This subject code already exists.',
            'subject_code.regex' => 'Subject code must contain letters followed by numbers. Example: CSC301 or ITT626.',

            'subject_name.required' => 'The subject name is required.',
            'subject_name.not_regex' => 'Subject name cannot be numbers only.',

            'lecturer_name.required' => 'The lecturer name is required.',
            'lecturer_name.regex' => 'Lecturer name must contain letters and can only include spaces, dot, dash, or apostrophe.',
        ]);

        Subject::create($validated);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'subject_code' => [
                'required',
                'string',
                'max:10',
                'unique:subjects,subject_code,' . $subject->id,
                'regex:/^[A-Za-z]{2,5}[0-9]{2,4}$/'
            ],

            'subject_name' => [
                'required',
                'string',
                'max:100',
                'not_regex:/^[0-9]+$/'
            ],

            'lecturer_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z\s\.\'\-]+$/'
            ],
        ], [
            'subject_code.required' => 'The subject code is required.',
            'subject_code.unique' => 'This subject code already exists.',
            'subject_code.regex' => 'Subject code must contain letters followed by numbers. Example: CSC301 or ITT626.',

            'subject_name.required' => 'The subject name is required.',
            'subject_name.not_regex' => 'Subject name cannot be numbers only.',

            'lecturer_name.required' => 'The lecturer name is required.',
            'lecturer_name.regex' => 'Lecturer name must contain letters and can only include spaces, dot, dash, or apostrophe.',
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }
}