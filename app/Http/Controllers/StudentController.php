<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index()
    {
        // Only show users with role = student
        $students = User::where('role', 'student')->get();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $autoPassword = Str::random(8) . rand(10, 99) . '!';
        return view('students.create', compact('autoPassword'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z\s\.\'\-]+$/'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/',
                'confirmed',
            ],

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
                'not_regex:/^[0-9]+$/'
            ],
        ], [
            'name.required' => 'The student name is required.',
            'name.regex' => 'Student name must contain letters and can only include spaces, dot, dash, or apostrophe.',

            'email.required' => 'The email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Only Gmail address is allowed. Example: student@gmail.com',
            'email.unique' => 'This email already exists.',

            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'Password must contain uppercase letter, lowercase letter, number, and special character.',
            'password.confirmed' => 'Password confirmation does not match.',

            'phone_number.required' => 'The phone number is required.',
            'phone_number.regex' => 'Phone number must be a valid Malaysian number. Example: 0123456789',
            'phone_number.unique' => 'This phone number already exists.',

            'address.required' => 'The address is required.',
            'address.not_regex' => 'Address cannot be numbers only.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'role' => 'student',
        ]);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully!');
    }

    public function show(User $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(User $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?=.*[A-Za-z])[A-Za-z\s\.\'\-]+$/'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $student->id,
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/',
                'confirmed',
            ],

            'phone_number' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone_number,' . $student->id,
                'regex:/^01[0-9]{8,9}$/',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
                'not_regex:/^[0-9]+$/'
            ],
        ], [
            'name.required' => 'The student name is required.',
            'name.regex' => 'Student name must contain letters and can only include spaces, dot, dash, or apostrophe.',

            'email.required' => 'The email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Only Gmail address is allowed. Example: student@gmail.com',
            'email.unique' => 'This email already exists.',

            'password.min' => 'The password must be at least 8 characters.',
            'password.regex' => 'Password must contain uppercase letter, lowercase letter, number, and special character.',
            'password.confirmed' => 'Password confirmation does not match.',

            'phone_number.required' => 'The phone number is required.',
            'phone_number.regex' => 'Phone number must be a valid Malaysian number. Example: 0123456789',
            'phone_number.unique' => 'This phone number already exists.',

            'address.required' => 'The address is required.',
            'address.not_regex' => 'Address cannot be numbers only.',
        ]);

        $student->name = $validated['name'];
        $student->email = strtolower($validated['email']);
        $student->phone_number = $validated['phone_number'];
        $student->address = $validated['address'];
        $student->role = 'student';

        if (!empty($validated['password'])) {
            $student->password = Hash::make($validated['password']);
        }

        $student->save();

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(User $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully!');
    }
}