@extends('layouts.template')

@section('title', 'Edit Hall')
@section('page-title', 'Edit Hall')

@section('content')

<style>
    .edit-hall-page {
        padding-top: 5px;
    }

    .hall-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .hall-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .hall-form-header h3,
    .hall-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .hall-form-icon {
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

    .hall-form-icon i {
        color: white;
        font-size: 38px;
    }

    .hall-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .hall-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .hall-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .hall-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .hall-form-card .card-body {
        padding: 28px;
    }

    .current-record-box {
        background: #fff7fb;
        border: 1px solid #fce7f3;
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 22px;
    }

    .current-record-box h6 {
        color: #831843;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .current-record-box p {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
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

    .btn-update-hall {
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

    .btn-update-hall:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-hall {
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

    .btn-back-hall:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }
</style>

<div class="edit-hall-page">

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
    <div class="hall-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="hall-form-icon">
                        <i class="material-symbols-rounded">edit_location</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Edit Lecture Hall</h3>
                        <p class="mb-0">Update lecture hall name and location information in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card hall-form-card">
        <div class="card-header">
            <h4 class="hall-form-title">Lecture Hall Information</h4>
            <p class="hall-form-subtitle">Modify the fields below and save the updated hall record.</p>
        </div>

        <div class="card-body">

            <div class="current-record-box">
                <h6>Current Hall Record</h6>
                <p>
                    Hall ID: {{ $hall->id }} |
                    Name: {{ $hall->lecture_hall_name }} |
                    Place: {{ $hall->lecture_hall_place }}
                </p>
            </div>

            <form action="{{ route('halls.update', $hall->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section-title">
                    Hall Details
                </div>

                <div class="row">
                    {{-- Hall Name --}}
                    <div class="col-md-6 mb-4">
                        <label for="lecture_hall_name" class="form-label">Hall Name *</label>

                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="lecture_hall_name"
                                id="lecture_hall_name"
                                value="{{ old('lecture_hall_name', $hall->lecture_hall_name) }}"
                                required
                                class="form-control @error('lecture_hall_name') is-invalid @enderror"
                                placeholder="Enter hall name">
                            <i class="material-symbols-rounded field-icon">apartment</i>
                        </div>

                        @error('lecture_hall_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hall Place --}}
                    <div class="col-md-6 mb-4">
                        <label for="lecture_hall_place" class="form-label">Hall Place *</label>

                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="lecture_hall_place"
                                id="lecture_hall_place"
                                value="{{ old('lecture_hall_place', $hall->lecture_hall_place) }}"
                                required
                                class="form-control @error('lecture_hall_place') is-invalid @enderror"
                                placeholder="Enter hall place">
                            <i class="material-symbols-rounded field-icon">location_on</i>
                        </div>

                        @error('lecture_hall_place')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-update-hall">
                        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                        Update Hall
                    </button>

                    <a href="{{ route('halls.index') }}" class="btn-back-hall">
                        <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection