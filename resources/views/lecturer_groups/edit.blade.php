@extends('layouts.template')

@section('title', 'Edit Lecturer Group')
@section('page-title', 'Edit Lecturer Group')

@section('content')

<style>
    .edit-lecturer-group-page {
        padding-top: 5px;
    }

    .lecturer-group-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .lecturer-group-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .lecturer-group-form-header h3,
    .lecturer-group-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .lecturer-group-form-icon {
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

    .lecturer-group-form-icon i {
        color: white;
        font-size: 38px;
    }

    .lecturer-group-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .lecturer-group-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .lecturer-group-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .lecturer-group-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .lecturer-group-form-card .card-body {
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

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .btn-update-lecturer-group {
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

    .btn-update-lecturer-group:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-lecturer-group {
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

    .btn-back-lecturer-group:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }
</style>

<div class="edit-lecturer-group-page">

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
    <div class="lecturer-group-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="lecturer-group-form-icon">
                        <i class="material-symbols-rounded">edit</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Edit Class Group</h3>
                        <p class="mb-0">Update class group name and part information in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card lecturer-group-form-card">
        <div class="card-header">
            <h4 class="lecturer-group-form-title">Class Group Information</h4>
            <p class="lecturer-group-form-subtitle">Modify the fields below and save the updated lecturer group record.</p>
        </div>

        <div class="card-body">

            <div class="current-record-box">
                <h6>Current Lecturer Group Record</h6>
                <p>
                    Group ID: {{ $lecturer_group->id }} |
                    Name: {{ $lecturer_group->name }} |
                    Part: {{ $lecturer_group->part }}
                </p>
            </div>

            <form action="{{ route('lecturer-groups.update', $lecturer_group->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section-title">
                    Group Details
                </div>

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label">Group Name *</label>
                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $lecturer_group->name) }}"
                                required
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Enter lecturer group name">
                            <i class="material-symbols-rounded field-icon">groups</i>
                        </div>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Part --}}
                    <div class="col-md-6 mb-4">
                        <label for="part" class="form-label">Part *</label>
                        <div class="input-icon-wrapper">
                            <input
                                type="text"
                                name="part"
                                id="part"
                                value="{{ old('part', $lecturer_group->part) }}"
                                required
                                class="form-control @error('part') is-invalid @enderror"
                                placeholder="Enter part">
                            <i class="material-symbols-rounded field-icon">school</i>
                        </div>

                        @error('part')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-update-lecturer-group">
                        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                        Update Lecturer Group
                    </button>

                    <a href="{{ route('lecturer-groups.index') }}" class="btn-back-lecturer-group">
                        <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection