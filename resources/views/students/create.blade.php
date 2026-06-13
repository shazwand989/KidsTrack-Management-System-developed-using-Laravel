@extends('layouts.template')

@section('title', 'Create Student')
@section('page-title', 'Create Student')

@section('content')

<style>
    .create-student-page {
        padding-top: 5px;
    }

    .student-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .student-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .student-form-header h3,
    .student-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .student-form-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        position: relative;
        z-index: 2;
    }

    .student-form-icon i {
        color: white;
        font-size: 38px;
    }

    .student-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .student-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .student-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .student-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .student-form-card .card-body {
        padding: 28px;
    }

    .form-label {
        color: #831843;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #f3c4dc !important;
        border-radius: 14px !important;
        padding: 12px 14px !important;
        min-height: 48px;
        box-shadow: none !important;
        color: #374151;
    }

    .form-control:focus {
        border-color: #db2777 !important;
        box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.12) !important;
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper .field-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #be185d;
        font-size: 22px;
        pointer-events: none;
    }

    .password-eye-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
        z-index: 20;
        color: #be185d;
    }

    .password-eye-btn span {
        font-size: 23px;
        pointer-events: none;
    }

    .password-note {
        background: #fce7f3;
        color: #9d174d;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 8px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .btn-save-student {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white !important;
        border: none;
        border-radius: 14px;
        padding: 12px 22px;
        font-weight: 900;
        text-decoration: none;
        box-shadow: 0 10px 22px rgba(219, 39, 119, 0.22);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .btn-save-student:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-student {
        background: #f3f4f6;
        color: #374151 !important;
        border: none;
        border-radius: 14px;
        padding: 12px 22px;
        font-weight: 900;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back-student:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }

    .invalid-feedback {
        display: block;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        margin-top: 6px;
    }

    .form-section-title {
        color: #831843;
        font-weight: 900;
        font-size: 15px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #fce7f3;
    }
</style>

<div class="create-student-page">

    {{-- Header --}}
    <div class="student-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="student-form-icon">
                        <i class="material-symbols-rounded">person_add</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Register New Student</h3>
                        <p class="mb-0">Fill in the student details to create a new student account in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card student-form-card">
        <div class="card-header">
            <h4 class="student-form-title">Student Information</h4>
            <p class="student-form-subtitle">Please complete all required fields before saving the record.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('students.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-section-title">
                    Basic Details
                </div>

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label">Student Name</label>
                        <div class="input-icon-wrapper">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Enter student name">
                            <i class="material-symbols-rounded field-icon">badge</i>
                        </div>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6 mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-icon-wrapper">
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="Enter email address">
                            <i class="material-symbols-rounded field-icon">mail</i>
                        </div>

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-md-6 mb-4">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <div class="input-icon-wrapper">
                            <input type="text"
                                   name="phone_number"
                                   id="phone_number"
                                   class="form-control @error('phone_number') is-invalid @enderror"
                                   value="{{ old('phone_number') }}"
                                   placeholder="Enter phone number">
                            <i class="material-symbols-rounded field-icon">call</i>
                        </div>

                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="col-md-6 mb-4">
                        <label for="address" class="form-label">Address</label>
                        <div class="input-icon-wrapper">
                            <input type="text"
                                   name="address"
                                   id="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address') }}"
                                   placeholder="Enter student address">
                            <i class="material-symbols-rounded field-icon">home</i>
                        </div>

                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section-title mt-2">
                    Account Security
                </div>

                <div class="row">
                    {{-- Password --}}
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label">Password</label>

                        <div style="position: relative;">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                value="{{ old('password', $autoPassword ?? '') }}"
                                placeholder="Enter password"
                                style="padding-right: 50px !important;">

                            <button
                                type="button"
                                class="password-eye-btn"
                                onclick="togglePassword('password', 'password_icon')">
                                <span id="password_icon" class="material-symbols-rounded">visibility</span>
                            </button>
                        </div>

                        <div class="password-note">
                            Password must contain at least 8 characters, 1 uppercase letter, 1 number, and 1 special character.
                        </div>

                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                  {{-- Confirm Password --}}
<div class="col-md-6 mb-4">
    <label for="password_confirmation" class="form-label">Confirm Password</label>

    <div style="position: relative;">
        <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            class="form-control @error('password') is-invalid @enderror"
            value="{{ old('password_confirmation', $autoPassword ?? '') }}"
            placeholder="Confirm password"
            style="padding-right: 50px !important;">

        <button
            type="button"
            class="password-eye-btn"
            onclick="togglePassword('password_confirmation', 'password_confirmation_icon')">
            <span id="password_confirmation_icon" class="material-symbols-rounded">visibility</span>
        </button>
    </div>

    @error('password')
        @if ($message == 'Password confirmation does not match.')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @endif
    @enderror
</div>

                <div class="form-actions">
                    <button type="submit" class="btn-save-student">
                        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                        Save Student
                    </button>

                    <a href="{{ route('students.index') }}" class="btn-back-student">
                        <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>

@endsection