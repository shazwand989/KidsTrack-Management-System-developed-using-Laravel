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
            'address' => 'required|string',
            'parent_id' => 'required|exists:users,id',
            'children' => 'required|array|min:1',
            'children.*.name' => 'required|string|max:255',
            'children.*.classroom_id' => 'nullable|exists:classrooms,id',
            'children.*.age' => 'required|integer|min:0|max:17',
            'children.*.ic_number' => 'required|string|distinct',
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

            // Create guardianship for main parent
            \App\Models\Guardianship::create([
                'user_id' => $parentId,
                'child_id' => $child->id,
                'relationship' => 'main_parent',
                'is_emergency_contact' => true,
            ]);

            $count++;
        }

        return redirect()->route('children.index')
            ->with('success', "{$count} child(ren) registered successfully! QR Codes generated.");
    }

    public function show(Child $child)
    {
        $child->load(['classroom', 'guardianships.user', 'attendances']);
        return view('children.show', compact('child'));
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
            'address' => 'required|string',
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

        // Sync guardianships
        $guardianshipData = [];
        if ($request->parent_id) {
            $guardianshipData[$request->parent_id] = ['relationship' => 'main_parent', 'is_emergency_contact' => true];
        }
        if ($request->second_parent_id && $request->second_parent_id != $request->parent_id) {
            $guardianshipData[$request->second_parent_id] = ['relationship' => 'second_parent', 'is_emergency_contact' => false];
        }
        if ($request->guardian_id && !isset($guardianshipData[$request->guardian_id])) {
            $guardianshipData[$request->guardian_id] = ['relationship' => 'guardian', 'is_emergency_contact' => false];
        }
        $child->linkedUsers()->sync($guardianshipData);

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
