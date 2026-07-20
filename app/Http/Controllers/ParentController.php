<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    // LIST - show all parent/guardian users
    public function index()
    {
        $parents = \App\Models\User::whereIn('role', ['parent1', 'parent2', 'guardian'])
            ->with('children')
            ->paginate(10);
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
            // Main Parent
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'age' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',

            // Second Parent
            'second_name' => 'nullable|string|max:255',
            'second_email' => 'nullable|email|unique:users,email',
            'second_password' => 'nullable|string|min:8',
            'second_phone' => 'nullable|string|max:20',
            'second_address' => 'nullable|string',
            'second_age' => 'nullable|string',
            'second_photo' => 'nullable|image|max:2048',

            // Guardian
            'guardian_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|unique:users,email',
            'guardian_password' => 'nullable|string|min:8',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string',
            'guardian_age' => 'nullable|string',
            'guardian_photo' => 'nullable|image|max:2048',

            // Settings
            'verified' => 'nullable|boolean',
            'emergency' => 'nullable|boolean',
        ]);

        // ============================================
        // 1. CREATE MAIN PARENT USER (now directly in users table)
        // ============================================
        $user = \App\Models\User::create([
            'name' => $request->name,
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone,
            'address' => $request->address,
            'role' => 'parent1',
            'verified' => $request->has('verified'),
        ]);

        // Save photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents', 'public');
            $user->update(['photo' => $photoPath]);
        }

        // ============================================
        // 3. CREATE SECOND PARENT (optional) — create User directly
        // ============================================
        if ($request->filled('second_name')) {
            $secondUser = null;
            if ($request->filled('second_email')) {
                $secondUser = \App\Models\User::firstOrCreate(
                    ['email' => $request->second_email],
                    [
                        'name' => $request->second_name,
                        'age' => $request->second_age,
                        'password' => Hash::make($request->second_password ?? 'password123'),
                        'phone_number' => $request->second_phone,
                        'address' => $request->second_address,
                        'role' => 'parent2',
                    ]
                );
                if ($request->hasFile('second_photo')) {
                    $secondUser->update(['photo' => $request->file('second_photo')->store('parents', 'public')]);
                }
            }
        }

        // ============================================
        // 4. CREATE GUARDIAN (optional) — create User directly
        // ============================================
        if ($request->filled('guardian_name')) {
            $guardianUser = null;
            if ($request->filled('guardian_email')) {
                $guardianUser = \App\Models\User::firstOrCreate(
                    ['email' => $request->guardian_email],
                    [
                        'name' => $request->guardian_name,
                        'age' => $request->guardian_age,
                        'password' => Hash::make($request->guardian_password ?? 'password123'),
                        'phone_number' => $request->guardian_phone,
                        'address' => $request->guardian_address,
                        'role' => 'guardian',
                    ]
                );
                if ($request->hasFile('guardian_photo')) {
                    $guardianUser->update(['photo' => $request->file('guardian_photo')->store('parents', 'public')]);
                }
            }
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent registered successfully!');
    }

    // AJAX CHECK EMAIL
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        if (!$email) {
            return response()->json(['available' => false, 'message' => 'Email is required']);
        }

        $exists = \App\Models\User::where('email', $email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email already taken' : 'Email available'
        ]);
    }

    // SHOW
    public function show($id)
    {
        $parent = \App\Models\User::with('children')->findOrFail($id);
        return view('parent.show', compact('parent'));
    }

    public function edit($id)
    {
        $parent = \App\Models\User::with('children')->findOrFail($id);
        return view('parent.edit', compact('parent'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'age' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'verified' => 'nullable|boolean',
        ]);

        $parent = \App\Models\User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'phone_number' => $request->phone,
            'address' => $request->address,
            'verified' => $request->has('verified'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update photo
        if ($request->hasFile('photo')) {
            if ($parent->photo && Storage::disk('public')->exists($parent->photo)) {
                Storage::disk('public')->delete($parent->photo);
            }
            $data['photo'] = $request->file('photo')->store('parents', 'public');
        }

        $parent->update($data);

        return redirect()->route('parent.index')->with('success', 'Parent updated successfully.');
    }

    // DELETE
    public function destroy($id)
    {
        $parent = \App\Models\User::findOrFail($id);

        // Delete photo
        if ($parent->photo && Storage::disk('public')->exists($parent->photo)) {
            Storage::disk('public')->delete($parent->photo);
        }

        $parent->delete();

        return redirect()->route('parent.index')
            ->with('success', 'Parent deleted successfully!');
    }
}
