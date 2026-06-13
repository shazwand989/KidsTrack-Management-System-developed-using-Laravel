<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            // Email must be Gmail and cannot duplicate
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            ],

            // Phone number cannot duplicate
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone_number',
                'regex:/^01[0-9]{8,9}$/',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
            ],

            // Strong password
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'email.regex' => 'Only Gmail address is allowed. Example: user@gmail.com',
            'email.unique' => 'This email is already registered.',
            'phone_number.regex' => 'Phone number must be a valid Malaysian number. Example: 0123456789',
            'phone_number.unique' => 'This phone number is already registered.',
            'password.mixed' => 'Password must contain uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'role' => 'admin',
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}