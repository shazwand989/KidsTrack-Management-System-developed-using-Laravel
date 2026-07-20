<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::with(['parent', 'secondParent', 'guardian', 'classroom'])->latest()->get();
        return view('children.index', compact('children'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        $parents = ParentModel::all();
        $secondParents = SecondParent::all();
        $guardians = Guardian::all();
        
        return view('children.create', compact('classrooms', 'parents', 'secondParents', 'guardians'));
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
            'classroom_id' => 'nullable|exists:classrooms,id',
            'parent_id' => 'required|exists:parents,id',
            // 🔥🔥🔥 FIX: GUNA exists:parents BUKAN second_parents! 🔥🔥🔥
           // RUJUK second_parents (BUKAN parents!)
'second_parent_id' => 'nullable|exists:second_parents,id',  // ✅
            'guardian_id' => 'nullable|exists:guardians,id',
            'medical_notes' => 'nullable|string',
            'dietary' => 'nullable|string',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('children', 'public');
        }
        
        $data['enrollment_date'] = now();

        $nextId = Child::max('id') + 1;
        $qrData = 'KID-' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . '-' . time() . '-' . Str::random(8);
        $qrCodeUrl = rtrim(config('app.url'), '/') . '/scan-qr/' . $qrData;
        
        $data['qr_code'] = $qrData;
        $data['qr_code_url'] = $qrCodeUrl;
        
        $child = Child::create($data);
        
        $this->generateQRImage($child->id, $qrData);
        
        return redirect()->route('children.index')
            ->with('success', 'Child registered successfully! QR Code generated.');
    }

    public function show(Child $child)
    {
        $child->load(['parent', 'secondParent', 'guardian', 'classroom']);
        return view('children.show', compact('child'));
    }

    public function edit(Child $child)
    {
        $classrooms = Classroom::all();
        $parents = ParentModel::all();
        $secondParents = SecondParent::all();
        $guardians = Guardian::all();
        
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
            'parent_id' => 'required|exists:parents,id',
            // 🔥🔥🔥 FIX: GUNA exists:parents BUKAN second_parents! 🔥🔥🔥
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
            \Log::error('QR Code generation failed: ' . $e->getMessage());
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