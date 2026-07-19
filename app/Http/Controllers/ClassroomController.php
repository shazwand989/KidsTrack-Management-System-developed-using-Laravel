<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Child;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassroomController extends Controller
{
    // ============================================
    // INDEX - Senarai semua kelas
    // ============================================
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

    // ============================================
    // CREATE - Papar borang tambah kelas
    // ============================================
    public function create()
    {
        $teachers = Teacher::active()->get();
        return view('classrooms.create', compact('teachers'));
    }

    // ============================================
    // STORE - Simpan kelas baru
    // ============================================
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

    // ============================================
    // SHOW - Papar detail kelas dengan seatmap
    // ============================================
    public function show($id)
    {
        // Cari classroom
        $classroom = Classroom::with('teacher')->findOrFail($id);
        
        // Dapatkan children dalam kelas ini
        $children = Child::where('classroom_id', $id)
            ->where('is_active', true)
            ->get();

        // 📌 AMBIL ATTENDANCE HARI INI
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $attendances = Attendance::whereIn('child_id', $children->pluck('id'))
            ->whereDate('date', $today)
            ->get()
            ->keyBy('child_id');

        // 📌 KIRA STATISTIK
        $totalChildren = $children->count();
        $totalCheckin = $attendances->whereIn('status', ['present', 'checkin', 'late'])->count();
        $totalCheckout = $attendances->where('status', 'checkout')->count();
        $totalAbsent = $totalChildren - $totalCheckin;

        $stats = [
            'total_children' => $totalChildren,
            'total_present' => $totalCheckin,
            'total_checkout' => $totalCheckout,
            'total_absent' => $totalAbsent > 0 ? $totalAbsent : 0,
            'capacity_percentage' => $classroom->capacity > 0
                ? round(($totalChildren / $classroom->capacity) * 100)
                : 0,
        ];

        return view('classrooms.show', compact('classroom', 'children', 'attendances', 'stats'));
    }

    // ============================================
    // EDIT - Papar borang edit kelas
    // ============================================
    public function edit($id)
    {
        $classroom = Classroom::findOrFail($id);
        $teachers = Teacher::active()->get();
        return view('classrooms.edit', compact('classroom', 'teachers'));
    }

    // ============================================
    // UPDATE - Kemaskini kelas
    // ============================================
    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:classrooms,code,' . $id,
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

        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'Classroom updated successfully!');
    }

    // ============================================
    // DESTROY - Padam kelas
    // ============================================
    public function destroy($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();
        
        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom deleted successfully!');
    }
}