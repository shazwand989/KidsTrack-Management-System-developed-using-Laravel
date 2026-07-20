<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $selectedRole = $request->input('role', 'parent');

        // FIRST: CHECK REDIRECT URL FROM SESSION
        if (session()->has('redirect_after_login')) {
            $redirectUrl = session('redirect_after_login');
            session()->forget('redirect_after_login');
            return redirect($redirectUrl);
        }

        // SECOND: CHECK REDIRECT URL FROM QUERY STRING
        if ($request->has('redirect')) {
            return redirect($request->input('redirect'));
        }

        // THIRD: CHECK SELECTED ROLE

        // 1. ADMIN
        if ($selectedRole === 'admin') {
            if (!in_array($user->role, ['admin', 'teacher'])) {
                Auth::logout();
                return redirect()->route('login')->with('error', '❌ Akses ditolak. Anda bukan admin.');
            }
            return redirect()->intended(route('dashboard'));
        }

        // 2. PARENT & SECOND PARENT
        if ($selectedRole === 'parent' || $selectedRole === 'parent1' || $selectedRole === 'parent2') {
            if (!in_array($user->role, ['parent', 'parent1', 'parent2'])) {
                Auth::logout();
                return redirect()->route('login')->with('error', '❌ Akses ditolak. Anda bukan parent.');
            }

            if (in_array($user->role, ['parent', 'parent1'])) {
                $parent = \App\Models\ParentModel::where('id', $user->id)->first();
                if (!$parent) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', '❌ Profil parent tidak ditemui.');
                }
            }

            if ($user->role === 'parent2') {
                $secondParent = \App\Models\SecondParent::where('id', $user->id)->first();
                if (!$secondParent) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', '❌ Profil second parent tidak ditemui.');
                }
            }

            return redirect()->route('parent.dashboard');
        }

        // 3. GUARDIAN
        if ($selectedRole === 'guardian') {
            if ($user->role !== 'guardian') {
                Auth::logout();
                return redirect()->route('login')->with('error', '❌ Akses ditolak. Anda bukan guardian.');
            }

            $guardian = \App\Models\Guardian::where('id', $user->id)->first();
            if (!$guardian) {
                Auth::logout();
                return redirect()->route('login')->with('error', '❌ Profil guardian tidak ditemui.');
            }

            return redirect()->route('parent.dashboard');
        }

        // BACKWARD COMPATIBILITY
        if (in_array($user->role, ['admin', 'teacher'])) {
            return redirect()->intended(route('dashboard'));
        }

        if (in_array($user->role, ['parent', 'parent1', 'parent2', 'guardian'])) {
            return redirect()->route('parent.dashboard');
        }

        Auth::logout();
        return redirect('/login')->with('error', '❌ Akses tidak dibenarkan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}