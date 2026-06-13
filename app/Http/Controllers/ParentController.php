<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\Child;

class ParentController extends Controller
{
    // LIST
    public function index()
    {
        $parents = ParentModel::with(['secondParent', 'guardian'])->get();
        return view('parent.index', compact('parents'));
    }

    // CREATE FORM
    public function create()
    {
        return view('parent.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        // 1. Save main parent photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents', 'public');
        }

        // 2. Save main parent
        $parent = ParentModel::create([
            'name'    => $request->name,
            'age'     => $request->age,
            'phone'   => $request->phone,
            'address' => $request->address,
            'photo'   => $photoPath,
        ]);

        // 3. Save second parent (optional)
        if ($request->filled('second_name')) {
            $secondPhotoPath = null;
            if ($request->hasFile('second_photo')) {
                $secondPhotoPath = $request->file('second_photo')->store('parents', 'public');
            }

            SecondParent::create([
                'parent_id' => $parent->id,
                'name'      => $request->second_name,
                'age'       => $request->second_age,
                'phone'     => $request->second_phone,
                'address'   => $request->second_address,
                'photo'     => $secondPhotoPath,
            ]);
        }

        // 4. Save guardian (optional)
        if ($request->filled('guardian_name')) {
            $guardianPhotoPath = null;
            if ($request->hasFile('guardian_photo')) {
                $guardianPhotoPath = $request->file('guardian_photo')->store('parents', 'public');
            }

            Guardian::create([
                'parent_id' => $parent->id,
                'name'      => $request->guardian_name,
                'age'       => $request->guardian_age,
                'phone'     => $request->guardian_phone,
                'address'   => $request->guardian_address,
            ]);
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent registered successfully!');
    }

    // SHOW
    public function show($id)
    {
        $parent = ParentModel::with(['secondParent', 'guardian', 'children'])->findOrFail($id);
        return view('parent.show', compact('parent'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $parent = ParentModel::with(['secondParent', 'guardian'])->findOrFail($id);
        return view('parent.edit', compact('parent'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $parent = ParentModel::findOrFail($id);

        // Update main parent photo
        $photoPath = $parent->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents', 'public');
        }

        $parent->update([
            'name'    => $request->name,
            'age'     => $request->age,
            'phone'   => $request->phone,
            'address' => $request->address,
            'photo'   => $photoPath,
        ]);

        // Update second parent
        if ($request->filled('second_name')) {
            $secondData = [
                'parent_id' => $parent->id,
                'name'      => $request->second_name,
                'age'       => $request->second_age,
                'phone'     => $request->second_phone,
                'address'   => $request->second_address,
            ];

            if ($request->hasFile('second_photo')) {
                $secondData['photo'] = $request->file('second_photo')->store('parents', 'public');
            }

            SecondParent::updateOrCreate(
                ['parent_id' => $parent->id],
                $secondData
            );
        } else {
            // Kalau kosong, delete second parent
            SecondParent::where('parent_id', $parent->id)->delete();
        }

        // Update guardian
        if ($request->filled('guardian_name')) {
            $guardianData = [
                'parent_id' => $parent->id,
                'name'      => $request->guardian_name,
                'age'       => $request->guardian_age,
                'phone'     => $request->guardian_phone,
                'address'   => $request->guardian_address,
            ];

            if ($request->hasFile('guardian_photo')) {
                $guardianData['photo'] = $request->file('guardian_photo')->store('parents', 'public');
            }

            Guardian::updateOrCreate(
                ['parent_id' => $parent->id],
                $guardianData
            );
        } else {
            // Kalau kosong, delete guardian
            Guardian::where('parent_id', $parent->id)->delete();
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent updated successfully!');
    }

    // DELETE
    public function destroy($id)
    {
        $parent = ParentModel::findOrFail($id);

        // Delete related records dulu sebelum delete parent
        SecondParent::where('parent_id', $id)->delete();
        Guardian::where('parent_id', $id)->delete();
        // Note: children tidak delete sebab children ada module sendiri

        $parent->delete();

        return redirect()->route('parents.index')
            ->with('success', 'Parent deleted successfully!');
    }
}