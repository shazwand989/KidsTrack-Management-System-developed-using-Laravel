@extends('layouts.template')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('content')

<style>
    .edit-student-page {
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

    .form-section-title {
        color: #831843;
        font-weight: 900;
        font-size: 15px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #fce7f3;
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

    .password-wrapper {
        position: relative;
    }

    .password-wrapper .form-control {
        padding-right: 52px !important;
    }

    .toggle-password-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border: none;
        border-radius: 12px;
        background: #fce7f3;
        color: #be185d;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .toggle-password-btn:hover {
        background: #fbcfe8;
    }

    .toggle-password-btn i {
        color: #be185d;
        font-size: 20px;
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

    .error-alert {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .error-alert ul {
        margin-bottom: 0;
    }

    .invalid-feedback {
        display: block;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        margin-top: 6px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .btn-update-student {
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

    .btn-update-student:hover {
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
</style>

<div class="edit-student-page">

    @if($errors->any())
        <div class="error-alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="student-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="student-form-icon">
                        <i class="material-symbols-rounded">edit</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Edit Student Record</h3>
                        <p class="mb-0">Update student details and account information in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card student-form-card">
        <div class="card-header">
            <h4 class="student-form-title">Student Information</h4>
            <p class="student-form-subtitle">Modify the fields below and save the updated student record.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                   value="{{ old('name', $student->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Enter student name"
                                   required>
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
                                   value="{{ old('email', $student->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Enter email address"
                                   required>
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
                                   value="{{ old('phone_number', $student->phone_number) }}"
                                   class="form-control @error('phone_number') is-invalid @enderror"
                                   placeholder="Enter phone number"
                                   required>
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
                                   value="{{ old('address', $student->address) }}"
                                   class="form-control @error('address') is-invalid @enderror"
                                   placeholder="Enter student address"
                                   required>
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
        <label for="password" class="form-label">
            New Password <small class="text-muted">(leave blank if unchanged)</small>
        </label>

        <div class="password-wrapper">
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Enter new password"
                   autocomplete="new-password">

            <button type="button"
                    class="toggle-password-btn"
                    onclick="togglePassword('password', 'eyeIcon1')">
                <i class="material-symbols-rounded" id="eyeIcon1">visibility</i>
            </button>
        </div>

        <div class="password-note">
            Leave this field empty if you do not want to change the current password.
        </div>

        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="col-md-6 mb-4">
        <label for="password_confirmation" class="form-label">
            Confirm New Password
        </label>

        <div class="password-wrapper">
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Confirm new password"
                   autocomplete="new-password">

            <button type="button"
                    class="toggle-password-btn"
                    onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                <i class="material-symbols-rounded" id="eyeIcon2">visibility</i>
            </button>
        </div>

        @error('password')
            @if ($message == 'Password confirmation does not match.')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @endif
        @enderror
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn-update-student">
        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
        Update Student
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
    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);

        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            field.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>

@endsection