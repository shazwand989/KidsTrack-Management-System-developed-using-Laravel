<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Classroom; // <-- TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('classroom')->latest()->get(); // <-- TAMBAH with('classroom')
        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $classrooms = Classroom::all(); // <-- TAMBAH INI
        return view('teachers.create', compact('classrooms')); // <-- TAMBAH compact('classrooms')
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:70',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:teachers',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'classroom_id' => 'nullable|exists:classrooms,id', // <-- TAMBAH INI (ganti nursery_class)
            'status' => 'required|in:active,inactive,on_leave',
            'qualifications' => 'nullable|string',
            'join_date' => 'nullable|date',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }
        
        Teacher::create($data);
        
        return redirect()->route('teachers.index')
            ->with('success', 'Teacher registered successfully!');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('classroom'); // <-- TAMBAH INI
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $classrooms = Classroom::all(); // <-- TAMBAH INI
        return view('teachers.edit', compact('teacher', 'classrooms')); // <-- TAMBAH compact('classrooms')
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:70',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'classroom_id' => 'nullable|exists:classrooms,id', // <-- TAMBAH INI (ganti nursery_class)
            'status' => 'required|in:active,inactive,on_leave',
            'qualifications' => 'nullable|string',
            'join_date' => 'nullable|date',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }
        
        $teacher->update($data);
        
        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }
        $teacher->delete();
        
        return redirect()->route('teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}