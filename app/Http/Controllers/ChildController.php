<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::with(['parent', 'secondParent', 'guardian', 'classroom'])->latest()->get();
        return view('children.index', compact('children'));
    }

    public function create(Request $request)
    {
        $classrooms = Classroom::all();
        $parents = \App\Models\User::whereIn('role', ['parent1'])->get();
        $preSelectedParentId = $request->query('parent_id');

        return view('children.create', compact('classrooms', 'parents', 'preSelectedParentId'));
    }

    // AJAX CHECK IC NUMBER
    public function checkIc(Request $request)
    {
        $ic = $request->input('ic', '');
        if (!$ic) {
            return response()->json(['available' => false, 'message' => 'IC number is required']);
        }
        $exists = Child::where('ic_number', $ic)->exists();
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'IC number already registered' : 'IC number available'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'children.*.classroom_id' => 'nullable|exists:classrooms,id',
            'address' => 'nullable|string',
            'parent_id' => 'required|exists:users,id',
            'children' => 'required|array|min:1',
            'children.*.name' => 'required|string|max:255',
            'children.*.classroom_id' => 'nullable|exists:classrooms,id',
            'children.*.age' => 'required|integer|min:0|max:17',
            'children.*.ic_number' => 'required|string|distinct|unique:children,ic_number',
            'children.*.dob' => 'nullable|date',
            'children.*.photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'children.*.medical_notes' => 'nullable|string',
            'children.*.dietary' => 'nullable|string',
        ]);

        $shared = $request->only(['address']);
        $shared['enrollment_date'] = now();
        $parentId = $request->input('parent_id');
        $children = $request->input('children', []);
        $count = 0;

        foreach ($children as $i => $childInput) {
            $data = $shared;
            $data['name'] = $childInput['name'];
            $data['age'] = $childInput['age'];
            $data['ic_number'] = $childInput['ic_number'];
            $data['classroom_id'] = $childInput['classroom_id'] ?? null;
            $data['dob'] = $childInput['dob'] ?? null;
            $data['medical_notes'] = $childInput['medical_notes'] ?? null;
            $data['dietary'] = $childInput['dietary'] ?? null;

            if ($request->hasFile("children.{$i}.photo")) {
                $data['photo'] = $request->file("children.{$i}.photo")->store('children', 'public');
            }

            $nextId = Child::max('id') + 1;
            $qrData = 'KID-' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . '-' . time() . '-' . Str::random(8);
            $data['qr_code'] = $qrData;
            $data['qr_code_url'] = rtrim(config('app.url'), '/') . '/scan-qr/' . $qrData;

            $child = Child::create($data);
            $this->generateQRImage($child->id, $qrData);

            // Remove any existing main_parent for this child (one family constraint)
            \App\Models\Guardianship::where('child_id', $child->id)
                ->where('relationship', 'main_parent')
                ->delete();

            // Create guardianship for main parent
            \App\Models\Guardianship::create([
                'user_id' => $parentId,
                'child_id' => $child->id,
                'relationship' => 'main_parent',
                'is_emergency_contact' => true,
            ]);

            $count++;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} child(ren) registered!",
                'child_id' => $child->id ?? null,
            ]);
        }

        return redirect()->route('children.index')
            ->with('success', "{$count} child(ren) registered successfully! QR Codes generated.");
    }

    public function show(Child $child)
    {
        $child->load(['classroom', 'guardianships.user', 'attendances']);

        // Main parent from this child
        $mainParent = $child->guardianships->where('relationship', 'main_parent')->first()?->user;

        // Second parent & guardian — use siblings to find family's shared ones
        $secondParent = null;
        $guardian = null;
        $siblings = collect();

        if ($mainParent) {
            $siblingIds = \App\Models\Guardianship::where('user_id', $mainParent->id)
                ->where('relationship', 'main_parent')
                ->where('child_id', '!=', $child->id)
                ->pluck('child_id');
            $siblings = Child::with('classroom')->whereIn('id', $siblingIds)->get();

            // Find family's second_parent from any sibling
            if ($siblings->isNotEmpty()) {
                $siblingGuardianships = \App\Models\Guardianship::whereIn('child_id', $siblingIds)->get();
                $secondParent = $siblingGuardianships
                    ->where('relationship', 'second_parent')->first()?->user;
                $guardian = $siblingGuardianships
                    ->where('relationship', 'guardian')->first()?->user;
            }

            // Fallback to this child's own records
            if (!$secondParent) {
                $secondParent = $child->guardianships->where('relationship', 'second_parent')->first()?->user;
            }
            if (!$guardian) {
                $guardian = $child->guardianships->where('relationship', 'guardian')->first()?->user;
            }
        }

        return view('children.show', compact('child', 'mainParent', 'secondParent', 'guardian', 'siblings'));
    }

    public function edit(Child $child)
    {
        $classrooms = Classroom::all();
        $parents = \App\Models\User::whereIn('role', ['parent1'])->get();
        $secondParents = \App\Models\User::whereIn('role', ['parent2'])->get();
        $guardians = \App\Models\User::whereIn('role', ['guardian'])->get();

        return view('children.edit', compact('child', 'classrooms', 'parents', 'secondParents', 'guardians'));
    }

    public function update(Request $request, Child $child)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:17',
            'ic_number' => 'required|string|unique:children,ic_number,' . $child->id,
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'parent_id' => 'required|exists:users,id',
            'second_parent_id' => 'nullable|exists:users,id',
            'guardian_id' => 'nullable|exists:users,id',
            'medical_notes' => 'nullable|string',
            'dietary' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'age', 'ic_number', 'dob', 'address', 'classroom_id', 'medical_notes', 'dietary']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('photo')) {
            if ($child->photo) {
                Storage::disk('public')->delete($child->photo);
            }
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }

        $child->update($data);

        // Find siblings (children sharing the same main parent as this child currently has)
        $currentMainParentId = $child->guardianships()
            ->where('relationship', 'main_parent')->value('user_id');
        $siblingIds = [];
        if ($currentMainParentId) {
            $siblingIds = \App\Models\Guardianship::where('user_id', $currentMainParentId)
                ->where('relationship', 'main_parent')
                ->pluck('child_id')->toArray();
        }
        // Always include this child
        if (!in_array($child->id, $siblingIds)) {
            $siblingIds[] = $child->id;
        }

        // Build guardianship data for second_parent & guardian (shared across family)
        $secondParentId = $request->second_parent_id;
        $guardianId = $request->guardian_id;

        foreach ($siblingIds as $sid) {
            $sibling = Child::find($sid);
            if (!$sibling) continue;

            $gsData = [];

            // Keep existing main_parent for each sibling (don't change it)
            $sibMainParent = $sibling->guardianships()
                ->where('relationship', 'main_parent')->value('user_id');
            if ($sibMainParent) {
                $gsData[$sibMainParent] = ['relationship' => 'main_parent', 'is_emergency_contact' => true];
            } elseif ($request->parent_id) {
                // No existing main parent — use the new one
                $gsData[$request->parent_id] = ['relationship' => 'main_parent', 'is_emergency_contact' => true];
            }

            if ($secondParentId && $secondParentId != ($sibMainParent ?? $request->parent_id)) {
                $gsData[$secondParentId] = ['relationship' => 'second_parent', 'is_emergency_contact' => false];
            }
            if ($guardianId && !isset($gsData[$guardianId])) {
                $gsData[$guardianId] = ['relationship' => 'guardian', 'is_emergency_contact' => false];
            }

            $sibling->linkedUsers()->sync($gsData);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Child updated.']);
        }

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

    private function generateQRImage($childId, $qrData)
    {
        try {
            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrData);
            $contents = file_get_contents($qrImageUrl);

            if ($contents) {
                $path = storage_path('app/public/qrcodes/child-' . $childId . '.png');

                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }

                file_put_contents($path, $contents);
            }
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
        }
    }

    public function showQR($id)
    {
        $child = Child::findOrFail($id);
        return view('children.qr-code', compact('child'));
    }

    public function downloadQR($id)
    {
        $child = Child::findOrFail($id);

        $localPath = storage_path('app/public/qrcodes/child-' . $child->id . '.png');

        if (file_exists($localPath)) {
            return response()->download($localPath, 'qrcode-' . $child->name . '.png');
        }

        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($child->qr_code);
        $contents = file_get_contents($qrImageUrl);

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qrcode-' . $child->name . '.png"',
            ]);
    }

    public function getQR($id)
    {
        $child = Child::findOrFail($id);

        $localPath = storage_path('app/public/qrcodes/child-' . $child->id . '.png');

        if (file_exists($localPath)) {
            return response()->file($localPath);
        }

        return redirect('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($child->qr_code));
    }

    public function generateQR($id)
    {
        $child = Child::findOrFail($id);

        $qrData = 'KID-' . str_pad($child->id, 4, '0', STR_PAD_LEFT) . '-' . time() . '-' . Str::random(8);
        $qrCodeUrl = rtrim(config('app.url'), '/') . '/scan-qr/' . $qrData;

        $child->update([
            'qr_code' => $qrData,
            'qr_code_url' => $qrCodeUrl,
        ]);

        $this->generateQRImage($child->id, $qrData);

        return redirect()->route('children.show', $child->id)
            ->with('success', 'QR Code generated successfully!');
    }
}
