@extends('layouts.template')

@section('title', 'Edit Day')
@section('page-title', 'Edit Day')

@section('content')

<style>
    .edit-day-page {
        padding-top: 5px;
    }

    .day-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .day-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .day-form-header h3,
    .day-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .day-form-icon {
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

    .day-form-icon i {
        color: white;
        font-size: 38px;
    }

    .day-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .day-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .day-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .day-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .day-form-card .card-body {
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

    .btn-update-day {
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

    .btn-update-day:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-day {
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

    .btn-back-day:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }
</style>

<div class="edit-day-page">

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
    <div class="day-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="day-form-icon">
                        <i class="material-symbols-rounded">edit_calendar</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Edit Academic Day</h3>
                        <p class="mb-0">Update the day name used for timetable scheduling.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

  {{-- Form Card --}}
<div class="card day-form-card">
    <div class="card-header">
        <h4 class="day-form-title">Day Information</h4>
        <p class="day-form-subtitle">Modify the field below and save the updated day record.</p>
    </div>

    <div class="card-body">

        <div class="current-record-box">
            <h6>Current Day Record</h6>
            <p>
                Day ID: {{ $day->id }} |
                Day Name: {{ $day->day_name }}
            </p>
        </div>

        <form action="{{ route('days.update', $day->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section-title">
                Day Details
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="day_name" class="form-label">Day Name *</label>

                    <div class="input-icon-wrapper">
                        <select
                            name="day_name"
                            id="day_name"
                            required
                            class="form-control @error('day_name') is-invalid @enderror">

                            <option value="">-- Select Day --</option>

                            <option value="Monday" {{ old('day_name', $day->day_name) == 'Monday' ? 'selected' : '' }}>
                                Monday
                            </option>

                            <option value="Tuesday" {{ old('day_name', $day->day_name) == 'Tuesday' ? 'selected' : '' }}>
                                Tuesday
                            </option>

                            <option value="Wednesday" {{ old('day_name', $day->day_name) == 'Wednesday' ? 'selected' : '' }}>
                                Wednesday
                            </option>

                            <option value="Thursday" {{ old('day_name', $day->day_name) == 'Thursday' ? 'selected' : '' }}>
                                Thursday
                            </option>

                            <option value="Friday" {{ old('day_name', $day->day_name) == 'Friday' ? 'selected' : '' }}>
                                Friday
                            </option>

                            <option value="Saturday" {{ old('day_name', $day->day_name) == 'Saturday' ? 'selected' : '' }}>
                                Saturday
                            </option>

                            <option value="Sunday" {{ old('day_name', $day->day_name) == 'Sunday' ? 'selected' : '' }}>
                                Sunday
                            </option>
                        </select>

                        <i class="material-symbols-rounded field-icon">calendar_month</i>
                    </div>

                    @error('day_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-update-day">
                    <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                    Update Day
                </button>

                <a href="{{ route('days.index') }}" class="btn-back-day">
                    <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                    Back
                </a>
            </div>
        </form>
    </div>
</div>

</div>

@endsection