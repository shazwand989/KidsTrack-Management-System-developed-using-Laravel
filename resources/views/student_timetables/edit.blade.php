@extends('layouts.template')

@section('title', 'Edit Student Timetable')
@section('page-title', 'Edit Student Timetable')

@section('content')

<style>
    .edit-timetable-page {
        padding-top: 5px;
    }

    .timetable-form-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .timetable-form-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .timetable-form-header h3,
    .timetable-form-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .timetable-form-icon {
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

    .timetable-form-icon i {
        color: white;
        font-size: 38px;
    }

    .timetable-form-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        overflow: hidden;
        background: white;
    }

    .timetable-form-card .card-header {
        background: white;
        padding: 24px 28px 18px 28px;
        border-bottom: 1px solid #fce7f3;
    }

    .timetable-form-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 3px;
    }

    .timetable-form-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .timetable-form-card .card-body {
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

    .form-control,
    .form-select {
        border: 1px solid #f3c4dc !important;
        border-radius: 14px !important;
        padding: 12px 14px !important;
        min-height: 48px;
        box-shadow: none !important;
        color: #374151;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #db2777 !important;
        box-shadow: 0 0 0 4px rgba(219, 39, 119, 0.12) !important;
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

    .btn-update-timetable {
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

    .btn-update-timetable:hover {
        opacity: 0.92;
        color: white !important;
    }

    .btn-back-timetable {
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

    .btn-back-timetable:hover {
        background: #e5e7eb;
        color: #111827 !important;
    }
</style>

<div class="edit-timetable-page">

    {{-- Success Alert --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Custom Error Alert e.g. timetable clash --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation Error Alert --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following error:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="timetable-form-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="timetable-form-icon">
                        <i class="material-symbols-rounded">edit_calendar</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Edit Scheduled Timetable</h3>
                        <p class="mb-0">Update student schedule details including subject, day, hall, class group, and class time.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card timetable-form-card">
        <div class="card-header">
            <h4 class="timetable-form-title">Timetable Information</h4>
            <p class="timetable-form-subtitle">Modify the fields below and save the updated timetable record.</p>
        </div>

        <div class="card-body">

            <div class="current-record-box">
                <h6>Current Timetable Record</h6>
                <p>
                    Timetable ID: {{ $student_timetable->id }}
                    @if($student_timetable->user)
                        | Student: {{ $student_timetable->user->name }}
                    @endif
                </p>
            </div>

            <form action="{{ route('student-timetables.update', $student_timetable->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="form-section-title">
                    Schedule Details
                </div>

                <div class="row">

                    {{-- Student --}}
                    <div class="col-md-6 mb-4">
                        <label for="user_id" class="form-label">Student *</label>

                        <select
                            name="user_id"
                            id="user_id"
                            class="form-select @error('user_id') is-invalid @enderror">

                            <option value="">-- Select Student --</option>

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $student_timetable->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('user_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Subject --}}
                    <div class="col-md-6 mb-4">
                        <label for="subject_id" class="form-label">Subject *</label>

                        <select
                            name="subject_id"
                            id="subject_id"
                            class="form-select @error('subject_id') is-invalid @enderror">

                            <option value="">-- Select Subject --</option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ old('subject_id', $student_timetable->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                                    @if(!empty($subject->lecturer_name))
                                        | Lecturer: {{ $subject->lecturer_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('subject_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Day --}}
                    <div class="col-md-6 mb-4">
                        <label for="day_id" class="form-label">Day *</label>

                        <select
                            name="day_id"
                            id="day_id"
                            class="form-select @error('day_id') is-invalid @enderror">

                            <option value="">-- Select Day --</option>

                            @foreach ($days as $day)
                                <option value="{{ $day->id }}"
                                    {{ old('day_id', $student_timetable->day_id) == $day->id ? 'selected' : '' }}>
                                    {{ $day->day_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('day_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Hall --}}
                    <div class="col-md-6 mb-4">
                        <label for="hall_id" class="form-label">Lecture Hall *</label>

                        <select
                            name="hall_id"
                            id="hall_id"
                            class="form-select @error('hall_id') is-invalid @enderror">

                            <option value="">-- Select Hall --</option>

                            @foreach ($halls as $hall)
                                <option value="{{ $hall->id }}"
                                    {{ old('hall_id', $student_timetable->hall_id) == $hall->id ? 'selected' : '' }}>
                                    {{ $hall->lecture_hall_name }} - {{ $hall->lecture_hall_place }}
                                </option>
                            @endforeach
                        </select>

                        @error('hall_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Class Group --}}
                    <div class="col-md-6 mb-4">
                        <label for="lecturer_group_id" class="form-label">Class Group *</label>

                        <select
                            name="lecturer_group_id"
                            id="lecturer_group_id"
                            class="form-select @error('lecturer_group_id') is-invalid @enderror">

                            <option value="">-- Select Class Group --</option>

                            @foreach ($lecturerGroups as $lecturerGroup)
                                <option value="{{ $lecturerGroup->id }}"
                                    {{ old('lecturer_group_id', $student_timetable->lecturer_group_id) == $lecturerGroup->id ? 'selected' : '' }}>
                                    {{ $lecturerGroup->name }} - Part {{ $lecturerGroup->part }}
                                </option>
                            @endforeach
                        </select>

                        @error('lecturer_group_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="form-section-title mt-2">
                    Time Details
                </div>

                <div class="row">
                    {{-- Time From --}}
                    <div class="col-md-6 mb-4">
                        <label for="time_from" class="form-label">Time From *</label>

                        <div class="input-icon-wrapper">
                            <input
                                type="time"
                                name="time_from"
                                id="time_from"
                                value="{{ old('time_from', substr($student_timetable->time_from, 0, 5)) }}"
                                class="form-control @error('time_from') is-invalid @enderror">

                            <i class="material-symbols-rounded field-icon">schedule</i>
                        </div>

                        @error('time_from')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Time To --}}
                    <div class="col-md-6 mb-4">
                        <label for="time_to" class="form-label">Time To *</label>

                        <div class="input-icon-wrapper">
                            <input
                                type="time"
                                name="time_to"
                                id="time_to"
                                value="{{ old('time_to', substr($student_timetable->time_to, 0, 5)) }}"
                                class="form-control @error('time_to') is-invalid @enderror">

                            <i class="material-symbols-rounded field-icon">schedule</i>
                        </div>

                        @error('time_to')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-update-timetable">
                        <i class="material-symbols-rounded" style="font-size:20px;">save</i>
                        Update Timetable
                    </button>

                    <a href="{{ route('student-timetables.index') }}" class="btn-back-timetable">
                        <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection