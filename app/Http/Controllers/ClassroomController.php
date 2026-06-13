<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Child;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with('teacher')->latest()->get();
        
        $stats = [
            'total_classrooms' => $classrooms->count(),
            'total_children' => Child::count(),
            'total_present_today' => 0,
            'total_drop_off_today' => 0,
            'total_pickup_today' => 0,
            'total_not_pickup_today' => 0,
        ];
        
        return view('classrooms.index', compact('classrooms', 'stats'));
    }

    public function create()
    {
        $teachers = Teacher::active()->get();
        return view('classrooms.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:classrooms',
            'age_group' => 'required|string|max:100',
            'min_age' => 'required|integer|min:0',
            'max_age' => 'required|integer|min:0|gt:min_age',
            'capacity' => 'required|integer|min:1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        Classroom::create($request->all());

        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom created successfully!');
    }

    public function show(Classroom $classroom)
    {
        // Load teacher relationship
        $classroom->load('teacher');
        
        // Get children that belong to this classroom
        $children = Child::where('classroom_id', $classroom->id)
            ->where('is_active', true)
            ->get();

        // Ambil attendance hari ni
        $today = today()->toDateString();
        $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
            ->whereDate('date', $today)
            ->get()
            ->keyBy('child_id');

        // Calculate statistics
        $checkinCount  = $attendances->where('status', 'checkin')->count();
        $checkoutCount = $attendances->where('status', 'checkout')->count();
        $absentCount   = $children->count() - $checkinCount - $checkoutCount;

        $stats = [
            'total_children'      => $children->count(),
            'total_present'       => $checkinCount,
            'total_checkout'      => $checkoutCount,
            'total_absent'        => $absentCount,
            'capacity_percentage' => $classroom->capacity > 0
                ? round(($children->count() / $classroom->capacity) * 100)
                : 0,
        ];

        return view('classrooms.show', compact('classroom', 'children', 'attendances', 'stats'));
    }

    public function edit(Classroom $classroom)
    {
        $teachers = Teacher::active()->get();
        return view('classrooms.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:classrooms,code,' . $classroom->id,
            'age_group' => 'required|string|max:100',
            'min_age' => 'required|integer|min:0',
            'max_age' => 'required|integer|min:0|gt:min_age',
            'capacity' => 'required|integer|min:1',
            'teacher_id' => 'nullable|exists:teachers,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $classroom->update($request->all());

        return redirect()->route('classrooms.show', $classroom)
            ->with('success', 'Classroom updated successfully!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom deleted successfully!');
    }
}