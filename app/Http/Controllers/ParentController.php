<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    // LIST
    public function index()
    {
        $parents = ParentModel::with(['secondParent', 'guardian', 'user'])->get();
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
        // 1. CREATE MAIN PARENT USER
        // ============================================
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent1',
        ]);

        // Save main parent photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents', 'public');
        }

        // 2. Save main parent
        $parent = ParentModel::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'age' => $request->age,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'verified' => $request->has('verified'),
            'emergency' => $request->has('emergency'),
        ]);

        // ============================================
        // 3. CREATE SECOND PARENT (optional)
        // ============================================
        if ($request->filled('second_name')) {
            $secondUser = null;
            
            // If email provided, create user
            if ($request->filled('second_email')) {
                $secondUser = User::create([
                    'name' => $request->second_name,
                    'email' => $request->second_email,
                    'password' => Hash::make($request->second_password ?? 'password123'),
                    'role' => 'parent2',
                ]);
            }

            $secondPhotoPath = null;
            if ($request->hasFile('second_photo')) {
                $secondPhotoPath = $request->file('second_photo')->store('parents', 'public');
            }

            SecondParent::create([
                'parent_id' => $parent->id,
                'user_id' => $secondUser?->id,
                'name' => $request->second_name,
                'age' => $request->second_age,
                'phone' => $request->second_phone,
                'address' => $request->second_address,
                'photo' => $secondPhotoPath,
            ]);
        }

        // ============================================
        // 4. CREATE GUARDIAN (optional)
        // ============================================
        if ($request->filled('guardian_name')) {
            $guardianUser = null;
            
            // If email provided, create user
            if ($request->filled('guardian_email')) {
                $guardianUser = User::create([
                    'name' => $request->guardian_name,
                    'email' => $request->guardian_email,
                    'password' => Hash::make($request->guardian_password ?? 'password123'),
                    'role' => 'guardian',
                ]);
            }

            $guardianPhotoPath = null;
            if ($request->hasFile('guardian_photo')) {
                $guardianPhotoPath = $request->file('guardian_photo')->store('parents', 'public');
            }

            Guardian::create([
                'parent_id' => $parent->id,
                'user_id' => $guardianUser?->id,
                'name' => $request->guardian_name,
                'age' => $request->guardian_age,
                'phone' => $request->guardian_phone,
                'address' => $request->guardian_address,
                'photo' => $guardianPhotoPath,
            ]);
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent registered successfully!');
    }

    // SHOW
    public function show($id)
    {
        $parent = ParentModel::with(['secondParent', 'guardian', 'children', 'user'])->findOrFail($id);
        return view('parent.show', compact('parent'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $parent = ParentModel::with(['secondParent', 'guardian', 'user'])->findOrFail($id);
        return view('parent.edit', compact('parent'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            // Main Parent
            'name' => 'required|string|max:255',
            'email' => 'required|email',  // <-- DAH BUANG UNIQUE
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'age' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',

            // Second Parent
            'second_name' => 'nullable|string|max:255',
            'second_email' => 'nullable|email',  // <-- DAH BUANG UNIQUE
            'second_password' => 'nullable|string|min:8',
            'second_phone' => 'nullable|string|max:20',
            'second_address' => 'nullable|string',
            'second_age' => 'nullable|string',
            'second_photo' => 'nullable|image|max:2048',

            // Guardian
            'guardian_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email',  // <-- DAH BUANG UNIQUE
            'guardian_password' => 'nullable|string|min:8',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string',
            'guardian_age' => 'nullable|string',
            'guardian_photo' => 'nullable|image|max:2048',

            // Settings
            'verified' => 'nullable|boolean',
            'emergency' => 'nullable|boolean',
        ]);

        $parent = ParentModel::findOrFail($id);

        // ============================================
        // UPDATE MAIN PARENT USER
        // ============================================
        if ($parent->user_id) {
            $user = User::find($parent->user_id);
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                // Update password if provided
                if ($request->filled('password')) {
                    $user->update([
                        'password' => Hash::make($request->password),
                    ]);
                }
            }
        }

        // Update main parent photo
        $photoPath = $parent->photo;
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('parents', 'public');
        }

        $parent->update([
            'name' => $request->name,
            'age' => $request->age,
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photoPath,
            'verified' => $request->has('verified'),
            'emergency' => $request->has('emergency'),
        ]);

        // ============================================
        // UPDATE SECOND PARENT (optional)
        // ============================================
        if ($request->filled('second_name')) {
            $secondParent = SecondParent::where('parent_id', $parent->id)->first();
            $secondUser = null;
            
            // Update or create user for second parent
            if ($request->filled('second_email')) {
                if ($secondParent && $secondParent->user_id) {
                    // Update existing user
                    $secondUser = User::find($secondParent->user_id);
                    if ($secondUser) {
                        $secondUser->update([
                            'name' => $request->second_name,
                            'email' => $request->second_email,
                        ]);
                        if ($request->filled('second_password')) {
                            $secondUser->update([
                                'password' => Hash::make($request->second_password),
                            ]);
                        }
                    }
                } else {
                    // Create new user
                    $secondUser = User::create([
                        'name' => $request->second_name,
                        'email' => $request->second_email,
                        'password' => Hash::make($request->second_password ?? 'password123'),
                        'role' => 'parent2',
                    ]);
                }
            }

            $secondPhotoPath = null;
            if ($request->hasFile('second_photo')) {
                $secondPhotoPath = $request->file('second_photo')->store('parents', 'public');
            }

            $secondData = [
                'parent_id' => $parent->id,
                'user_id' => $secondUser?->id ?? ($secondParent?->user_id ?? null),
                'name' => $request->second_name,
                'age' => $request->second_age,
                'phone' => $request->second_phone,
                'address' => $request->second_address,
            ];

            if ($secondPhotoPath) {
                // Delete old photo
                if ($secondParent && $secondParent->photo && Storage::disk('public')->exists($secondParent->photo)) {
                    Storage::disk('public')->delete($secondParent->photo);
                }
                $secondData['photo'] = $secondPhotoPath;
            }

            SecondParent::updateOrCreate(
                ['parent_id' => $parent->id],
                $secondData
            );
        } else {
            // If empty, delete second parent and its user
            $secondParent = SecondParent::where('parent_id', $parent->id)->first();
            if ($secondParent) {
                if ($secondParent->user_id) {
                    User::where('id', $secondParent->user_id)->delete();
                }
                $secondParent->delete();
            }
        }

        // ============================================
        // UPDATE GUARDIAN (optional)
        // ============================================
        if ($request->filled('guardian_name')) {
            $guardian = Guardian::where('parent_id', $parent->id)->first();
            $guardianUser = null;
            
            // Update or create user for guardian
            if ($request->filled('guardian_email')) {
                if ($guardian && $guardian->user_id) {
                    // Update existing user
                    $guardianUser = User::find($guardian->user_id);
                    if ($guardianUser) {
                        $guardianUser->update([
                            'name' => $request->guardian_name,
                            'email' => $request->guardian_email,
                        ]);
                        if ($request->filled('guardian_password')) {
                            $guardianUser->update([
                                'password' => Hash::make($request->guardian_password),
                            ]);
                        }
                    }
                } else {
                    // Create new user
                    $guardianUser = User::create([
                        'name' => $request->guardian_name,
                        'email' => $request->guardian_email,
                        'password' => Hash::make($request->guardian_password ?? 'password123'),
                        'role' => 'guardian',
                    ]);
                }
            }

            $guardianPhotoPath = null;
            if ($request->hasFile('guardian_photo')) {
                $guardianPhotoPath = $request->file('guardian_photo')->store('parents', 'public');
            }

            $guardianData = [
                'parent_id' => $parent->id,
                'user_id' => $guardianUser?->id ?? ($guardian?->user_id ?? null),
                'name' => $request->guardian_name,
                'age' => $request->guardian_age,
                'phone' => $request->guardian_phone,
                'address' => $request->guardian_address,
            ];

            if ($guardianPhotoPath) {
                // Delete old photo
                if ($guardian && $guardian->photo && Storage::disk('public')->exists($guardian->photo)) {
                    Storage::disk('public')->delete($guardian->photo);
                }
                $guardianData['photo'] = $guardianPhotoPath;
            }

            Guardian::updateOrCreate(
                ['parent_id' => $parent->id],
                $guardianData
            );
        } else {
            // If empty, delete guardian and its user
            $guardian = Guardian::where('parent_id', $parent->id)->first();
            if ($guardian) {
                if ($guardian->user_id) {
                    User::where('id', $guardian->user_id)->delete();
                }
                $guardian->delete();
            }
        }

        return redirect()->route('parents.index')
            ->with('success', 'Parent updated successfully!');
    }

    // DELETE
    public function destroy($id)
    {
        $parent = ParentModel::findOrFail($id);

        // Delete related records
        $secondParent = SecondParent::where('parent_id', $id)->first();
        if ($secondParent) {
            if ($secondParent->user_id) {
                User::where('id', $secondParent->user_id)->delete();
            }
            $secondParent->delete();
        }

        $guardian = Guardian::where('parent_id', $id)->first();
        if ($guardian) {
            if ($guardian->user_id) {
                User::where('id', $guardian->user_id)->delete();
            }
            $guardian->delete();
        }

        // Delete main parent user
        if ($parent->user_id) {
            User::where('id', $parent->user_id)->delete();
        }

        // Delete photo
        if ($parent->photo && Storage::disk('public')->exists($parent->photo)) {
            Storage::disk('public')->delete($parent->photo);
        }

        $parent->delete();

        return redirect()->route('parents.index')
            ->with('success', 'Parent deleted successfully!');
    }
}