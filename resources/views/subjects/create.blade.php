@extends('layouts.template')

@section('title', 'Create Subject')
@section('page-title', 'Create Subject')

@section('content')

<style>
    .create-subject-page {
        padding-top: 5px;
    }

    .subject-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .subject-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .subject-form-header h3,
    .subject-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .subject-form-icon {
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

    .subject-form-icon i {
        color: white;
        font-size: 38px;
    }

    .subject-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .subject-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .subject-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .subject-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .subject-form-card .card-body {
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

    .btn-save-subject {
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

    .btn-save-subject:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-subject {
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

    .btn-back-subject:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }
</style>

<div class="create-subject-page">

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
    <div class="subject-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="subject-form-icon">
                        <i class="material-symbols-rounded">add</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Add New Subject</h3>
                        <p class="mb-0">Fill in the subject details and assigned lecturer information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card subject-form-card">
        <div class="card-header">
            <h4 class="subject-form-title">Subject Information</h4>
            <p class="subject-form-subtitle">Please complete all required fields before saving the subject record.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

                <div class="form-section-title">
                    Subject Details
                </div>

                <div class="row">
                    {{-- Subject Code --}}
                    <div class="col-md-6 mb-4">
                        <label for="subject_code" class="form-label">Subject Code *</label>
                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="subject_code"
                                id="subject_code"
                                value="{{ old('subject_code') }}"
                                required
                                class="form-control @error('subject_code') is-invalid @enderror"
                                placeholder="Enter subject code">
                            <i class="material-symbols-rounded field-icon">qr_code</i>
                        </div>

                        @error('subject_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Subject Name --}}
                    <div class="col-md-6 mb-4">
                        <label for="subject_name" class="form-label">Subject Name *</label>
                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="subject_name"
                                id="subject_name"
                                value="{{ old('subject_name') }}"
                                required
                                class="form-control @error('subject_name') is-invalid @enderror"
                                placeholder="Enter subject name">
                            <i class="material-symbols-rounded field-icon">menu_book</i>
                        </div>

                        @error('subject_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Lecturer Name --}}
                    <div class="col-md-6 mb-4">
                        <label for="lecturer_name" class="form-label">Lecturer Name</label>
                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="lecturer_name"
                                id="lecturer_name"
                                value="{{ old('lecturer_name') }}"
                                class="form-control @error('lecturer_name') is-invalid @enderror"
                                placeholder="Enter lecturer name">
                            <i class="material-symbols-rounded field-icon">person</i>
                        </div>

                        @error('lecturer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save-subject">
                        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                        Save Subject
                    </button>

                    <a href="{{ route('subjects.index') }}" class="btn-back-subject">
                        <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection