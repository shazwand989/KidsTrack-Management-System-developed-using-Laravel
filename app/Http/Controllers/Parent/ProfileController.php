<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = $this->getParent($user);
        $secondParent = $this->getSecondParent($user);
        $guardian = $this->getGuardian($user);
        return view('parent.profile.index', compact('user', 'parent', 'secondParent', 'guardian'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $parent = $this->getParent($user);
        $secondParent = $this->getSecondParent($user);
        $guardian = $this->getGuardian($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'age' => 'nullable|integer|min:18|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        Auth::user()->update(['name' => $validated['name'], 'email' => $validated['email']]);
        
        $photoPath = null;
        if ($request->hasFile('photo')) {
            foreach ([$parent, $secondParent, $guardian] as $model) {
                if ($model && $model->photo && Storage::disk('public')->exists($model->photo)) {
                    Storage::disk('public')->delete($model->photo);
                }
            }
            $photoPath = $request->file('photo')->store('profile-photos', 'public');
        }
        
        $data = [
            'name' => $validated['name'], 'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null, 'age' => $validated['age'] ?? null,
        ];
        if ($photoPath) $data['photo'] = $photoPath;
        
        if ($parent) $parent->update($data);
        if ($secondParent) $secondParent->update($data);
        if ($guardian) $guardian->update($data);
        
        return redirect()->route('parent.profile.index')->with('success', 'Profil berjaya dikemaskini!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['current_password' => 'required|string', 'password' => 'required|string|min:8|confirmed']);
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Kata laluan semasa tidak tepat.']);
        }
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return redirect()->route('parent.profile.index')->with('success', 'Kata laluan berjaya dikemaskini!');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('parent.settings.index', [
            'parent' => $this->getParent($user),
            'secondParent' => $this->getSecondParent($user),
            'guardian' => $this->getGuardian($user),
        ]);
    }

    private function getParent($user) {
        return in_array($user->role, ['parent','parent1']) ? ParentModel::where('user_id', Auth::id())->first() : null;
    }
    private function getSecondParent($user) {
        return $user->role === 'parent2' ? SecondParent::where('user_id', Auth::id())->first() : null;
    }
    private function getGuardian($user) {
        return $user->role === 'guardian' ? Guardian::where('user_id', Auth::id())->first() : null;
    }
}
