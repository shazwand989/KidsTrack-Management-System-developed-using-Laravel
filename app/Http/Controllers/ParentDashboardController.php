<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\ParentModel;
use App\Models\Guardian;
use App\Models\SecondParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $children = collect();

        // PARENT
        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $children = $parent->children;
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
            }
        }
        
        // SECOND PARENT - GUNA SECOND PARENT UNTUK NAMA
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                // Dapatkan children dari main parent
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $children = $mainParent->children;
                }
                // ✅ SECOND PARENT GUNA NAMA SENDIRI
                // $secondParent dah ada nama dia sendiri
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil second parent anda.');
            }
        }
        
        // GUARDIAN
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $children = $guardian->children;
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil guardian anda.');
            }
        }

        if (!$parent && !$secondParent && !$guardian) {
            return redirect()->route('dashboard')->with('error', 'Akses tidak dibenarkan.');
        }

        $totalChildren = $children->count();
        
        $todayAttendance = Attendance::whereIn('child_id', $children->pluck('id'))
            ->whereDate('date', today())
            ->get();
        
        $unreadNotifications = 0;

        return view('parent.dashboard', compact(
            'parent',
            'secondParent',
            'guardian',
            'children',
            'totalChildren',
            'todayAttendance',
            'unreadNotifications'
        ));
    }

    public function children()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $children = collect();

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $children = $parent->children()->with('classroom')->get();
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $children = $mainParent->children()->with('classroom')->get();
                }
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $children = $guardian->children()->with('classroom')->get();
            }
        }

        return view('parent.children', compact('parent', 'secondParent', 'guardian', 'children'));
    }

    public function childDetail($id)
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $child = null;

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $child = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->with(['classroom', 'attendances'])
                    ->findOrFail($id);
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $child = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->with(['classroom', 'attendances'])
                        ->findOrFail($id);
                }
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $child = Child::where('guardian_id', $guardian->id)
                    ->with(['classroom', 'attendances'])
                    ->findOrFail($id);
            }
        }

        if (!$child) {
            return redirect()->route('parent.children')->with('error', 'Anak tidak ditemui.');
        }
        
        $totalPresent = $child->attendances->where('status', 'present')->count();
        $totalAbsent = $child->attendances->where('status', 'absent')->count();
        $totalLate = $child->attendances->where('status', 'late')->count();
        
        return view('parent.child-detail', compact('parent', 'secondParent', 'guardian', 'child', 'totalPresent', 'totalAbsent', 'totalLate'));
    }

    public function attendance()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $children = collect();

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $children = $parent->children;
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $children = $mainParent->children;
                }
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $children = $guardian->children;
            }
        }
        
        $attendance = Attendance::whereIn('child_id', $children->pluck('id'))
            ->with(['child', 'child.classroom'])
            ->whereDate('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        
        return view('parent.attendance', compact('parent', 'secondParent', 'guardian', 'children', 'attendance'));
    }

    public function attendanceCalendar()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $children = collect();

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $children = $parent->children;
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $children = $mainParent->children;
                }
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $children = $guardian->children;
            }
        }
        
        return view('parent.attendance-calendar', compact('parent', 'secondParent', 'guardian', 'children'));
    }

    public function childAttendance($id)
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $child = null;

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $child = Child::where('parent_id', $parent->id)
                    ->orWhere('second_parent_id', $parent->id)
                    ->findOrFail($id);
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) {
                    $child = Child::where('parent_id', $mainParent->id)
                        ->orWhere('second_parent_id', $mainParent->id)
                        ->findOrFail($id);
                }
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $child = Child::where('guardian_id', $guardian->id)
                    ->findOrFail($id);
            }
        }

        if (!$child) {
            return redirect()->route('parent.children')->with('error', 'Anak tidak ditemui.');
        }
        
        $attendance = Attendance::where('child_id', $child->id)
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        return view('parent.child-attendance', compact('parent', 'secondParent', 'guardian', 'child', 'attendance'));
    }

    public function profile()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
        }
        
        return view('parent.profile', compact('parent', 'secondParent', 'guardian', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'age' => 'nullable|integer|min:18|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        Auth::user()->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        $photoPath = null;
        if ($request->hasFile('photo')) {
            if ($parent && $parent->photo && Storage::disk('public')->exists($parent->photo)) {
                Storage::disk('public')->delete($parent->photo);
            }
            if ($secondParent && $secondParent->photo && Storage::disk('public')->exists($secondParent->photo)) {
                Storage::disk('public')->delete($secondParent->photo);
            }
            if ($guardian && $guardian->photo && Storage::disk('public')->exists($guardian->photo)) {
                Storage::disk('public')->delete($guardian->photo);
            }
            $photoPath = $request->file('photo')->store('profile-photos', 'public');
        }
        
        if ($parent) {
            $parent->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'age' => $validated['age'] ?? null,
                'photo' => $photoPath ?? $parent->photo,
            ]);
        }
        
        if ($secondParent) {
            $secondParent->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'age' => $validated['age'] ?? null,
                'photo' => $photoPath ?? $secondParent->photo,
            ]);
        }
        
        if ($guardian) {
            $guardian->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'age' => $validated['age'] ?? null,
                'photo' => $photoPath ?? $guardian->photo,
            ]);
        }
        
        return redirect()->route('parent.profile')->with('success', 'Profil berjaya dikemaskini!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Kata laluan semasa tidak tepat.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('parent.profile')->with('success', 'Kata laluan berjaya dikemaskini!');
    }

    public function settings()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
        }
        
        return view('parent.settings', compact('parent', 'secondParent', 'guardian'));
    }
}