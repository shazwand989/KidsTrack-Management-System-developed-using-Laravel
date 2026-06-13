<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentModel;
use App\Models\Guardian;
use App\Models\Classroom;  // <-- TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::with(['parent', 'secondParent', 'guardian', 'classroom'])->latest()->get();
        return view('children.index', compact('children'));
    }

    public function create()
    {
        $classrooms = Classroom::all();  // <-- TAMBAH INI
        $parents = ParentModel::all();
        $guardians = Guardian::all();
        return view('children.create', compact('classrooms', 'parents', 'guardians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:17',
            'ic_number' => 'required|string|unique:children',
            'dob' => 'nullable|date',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'classroom_id' => 'nullable|exists:classrooms,id',  // <-- TUKAR dari nursery_type
            'parent_id' => 'required|exists:parents,id',
            'second_parent_id' => 'nullable|exists:parents,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'medical_notes' => 'nullable|string',
            'dietary' => 'nullable|string',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }
        
        $data['enrollment_date'] = now();
        
        Child::create($data);
        
        return redirect()->route('children.index')
            ->with('success', 'Child registered successfully!');
    }

    public function show(Child $child)
    {
        $child->load(['parent', 'secondParent', 'guardian', 'classroom']);
        return view('children.show', compact('child'));
    }

    public function edit(Child $child)
    {
        $classrooms = Classroom::all();  // <-- TAMBAH INI
        $parents = ParentModel::all();
        $guardians = Guardian::all();
        return view('children.edit', compact('child', 'classrooms', 'parents', 'guardians'));
    }

    public function update(Request $request, Child $child)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:17',
            'ic_number' => 'required|string|unique:children,ic_number,' . $child->id,
            'dob' => 'nullable|date',
            'address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'classroom_id' => 'nullable|exists:classrooms,id',  // <-- TUKAR dari nursery_type
            'parent_id' => 'required|exists:parents,id',
            'second_parent_id' => 'nullable|exists:parents,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'medical_notes' => 'nullable|string',
            'dietary' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            if ($child->photo) {
                Storage::disk('public')->delete($child->photo);
            }
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }
        
        $data['is_active'] = $request->has('is_active');
        
        $child->update($data);
        
        return redirect()->route('children.show', $child)
            ->with('success', 'Child updated successfully!');
    }

    public function destroy(Child $child)
    {
        if ($child->photo) {
            Storage::disk('public')->delete($child->photo);
        }
        $child->delete();
        
        return redirect()->route('children.index')
            ->with('success', 'Child deleted successfully!');
    }
}