<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    // LIST - show families grouped by shared children
    public function index()
    {
        // Get all main parents with their children
        $mainParents = \App\Models\User::where('role', 'parent1')
            ->with('children')
            ->get();

        // Build family groups: each main parent + their linked second parent & guardian
        $families = collect();
        $seenUserIds = [];

        foreach ($mainParents as $main) {
            $childIds = $main->children->pluck('id');

            if ($childIds->isEmpty()) {
                // Main parent with no children — show alone
                $families->push([
                    'main' => $main,
                    'second' => null,
                    'guardian' => null,
                    'children' => collect(),
                    'childCount' => 0,
                ]);
                $seenUserIds[] = $main->id;
                continue;
            }

            // Find related users sharing same children
            $related = \App\Models\User::where('id', '!=', $main->id)
                ->whereHas('guardianships', fn($q) => $q->whereIn('child_id', $childIds))
                ->get();

            $second = $related->firstWhere('role', 'parent2');
            $guardian = $related->firstWhere('role', 'guardian');

            $families->push([
                'main' => $main,
                'second' => $second,
                'guardian' => $guardian,
                'children' => $main->children,
                'childCount' => $main->children->count(),
            ]);

            $seenUserIds[] = $main->id;
            if ($second) $seenUserIds[] = $second->id;
            if ($guardian) $seenUserIds[] = $guardian->id;
        }

        // Add orphan parent2/guardian users not linked to any parent1
        $orphans = \App\Models\User::whereIn('role', ['parent2', 'guardian'])
            ->whereNotIn('id', $seenUserIds)
            ->with('children')
            ->get();

        foreach ($orphans as $orphan) {
            $families->push([
                'main' => $orphan,
                'second' => null,
                'guardian' => null,
                'children' => $orphan->children,
                'childCount' => $orphan->children->count(),
            ]);
        }

        return view('parent.index', compact('families'));
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

        // Find related family members — other users linked to the same children
        $childIds = $parent->children->pluck('id');
        $relatedUsers = \App\Models\User::where('id', '!=', $parent->id)
            ->whereHas('guardianships', function ($q) use ($childIds) {
                $q->whereIn('child_id', $childIds);
            })
            ->with(['guardianships' => function ($q) use ($childIds) {
                $q->whereIn('child_id', $childIds);
            }])
            ->get()
            ->groupBy('role');

        return view('parent.show', compact('parent', 'relatedUsers'));
    }

    public function edit($id)
    {
        $currentUser = \App\Models\User::with('children')->findOrFail($id);
        $childIds = $currentUser->children->pluck('id')->toArray();

        // Find all family members sharing these children
        $familyUsers = \App\Models\User::whereHas('guardianships', fn($q) => $q->whereIn('child_id', $childIds))
            ->whereIn('role', ['parent1', 'parent2', 'guardian'])
            ->with('children')
            ->get()
            ->keyBy('role');

        $main = $familyUsers->get('parent1');
        $second = $familyUsers->get('parent2');
        $guardian = $familyUsers->get('guardian');

        // If editing a parent2 or guardian, ensure the main parent is also loaded
        if (!$main && $currentUser->role !== 'parent1') {
            $main = $currentUser; // fallback if somehow no parent1 found
        }
        if (!$main) $main = $currentUser;

        // All children linked to any family member
        $allFamilyChildIds = \App\Models\Guardianship::whereIn('user_id', $familyUsers->pluck('id'))
            ->pluck('child_id')->unique()->toArray();
        $familyChildren = \App\Models\Child::whereIn('id', $allFamilyChildIds)->with('classroom')->get();
        $allChildren = \App\Models\Child::with('classroom')->orderBy('name')->get();

        return view('parent.edit', compact(
            'currentUser', 'main', 'second', 'guardian',
            'familyChildren', 'allChildren', 'allFamilyChildIds'
        ));
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
            'child_ids' => 'nullable|array',
            'child_ids.*' => 'exists:children,id',
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

        // Sync children only when child_ids is present in the request
        if ($request->has('child_ids')) {
            $childIds = $request->input('child_ids', []);
            $relationship = match($parent->role) {
                'parent2' => 'second_parent',
                'guardian' => 'guardian',
                default => 'main_parent',
            };

            \App\Models\Guardianship::where('user_id', $parent->id)
                ->whereNotIn('child_id', $childIds)
                ->delete();

            foreach ($childIds as $childId) {
                \App\Models\Guardianship::where('child_id', $childId)
                    ->where('relationship', $relationship)
                    ->where('user_id', '!=', $parent->id)
                    ->delete();

                \App\Models\Guardianship::updateOrCreate(
                    ['user_id' => $parent->id, 'child_id' => $childId],
                    ['relationship' => $relationship, 'is_emergency_contact' => $parent->role === 'parent1']
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Updated.']);
        }

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
