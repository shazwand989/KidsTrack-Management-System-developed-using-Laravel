@extends('layouts.template')

@section('title', 'Student Timetable Details')
@section('page-title', 'Student Timetable Details')

@section('content')

<style>
    .timetable-card-page {
        padding-top: 5px;
    }

    .timetable-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .timetable-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .timetable-view-header h3,
    .timetable-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .timetable-view-icon {
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

    .timetable-view-icon i {
        color: white;
        font-size: 38px;
    }

    .schedule-card-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .schedule-card {
        width: 760px;
        max-width: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 45px rgba(219, 39, 119, 0.18);
        border: 1px solid #fce7f3;
    }

    .schedule-card-top {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        padding: 26px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .schedule-card-top::after {
        content: "";
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        position: absolute;
        right: -45px;
        top: -45px;
    }

    .schedule-card-title {
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .schedule-profile {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .schedule-photo {
        width: 88px;
        height: 88px;
        border-radius: 22px;
        background: white;
        color: #be185d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 900;
        margin-right: 18px;
        box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        text-transform: uppercase;
    }

    .schedule-main-title {
        color: white !important;
        font-weight: 900;
        margin-bottom: 4px;
        font-size: 22px;
    }

    .schedule-main-subtitle {
        color: rgba(255,255,255,0.86);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .schedule-card-body {
        padding: 28px;
    }

    .schedule-section-title {
        color: #831843;
        font-weight: 900;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #fce7f3;
    }

    .schedule-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .schedule-info-box {
        background: #fff7fb;
        border: 1px solid #fce7f3;
        border-radius: 18px;
        padding: 18px;
        min-height: 115px;
        transition: 0.2s ease;
    }

    .schedule-info-box:hover {
        background: #fdf2f8;
        transform: translateY(-2px);
    }

    .schedule-info-label {
        color: #be185d;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .schedule-info-label i {
        color: #be185d;
        font-size: 20px;
    }

    .schedule-info-value {
        color: #111827;
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 3px;
        word-break: break-word;
    }

    .schedule-info-subvalue {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .time-box {
        background: linear-gradient(135deg, #fdf2f8, #fff7fb);
        border: 1px solid #fce7f3;
        border-radius: 20px;
        padding: 20px;
        margin-top: 22px;
    }

    .time-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .time-pill {
        background: #fce7f3;
        color: #be185d;
        padding: 12px 18px;
        border-radius: 18px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .time-pill i {
        color: #be185d;
        font-size: 20px;
    }

    .time-arrow {
        color: #be185d;
        font-weight: 900;
        font-size: 22px;
    }

    .schedule-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff7fb;
        border-radius: 18px;
        padding: 14px 16px;
        margin-top: 22px;
    }

    .status-label {
        color: #831843;
        font-weight: 900;
        margin-bottom: 0;
    }

    .status-badge {
        background: #fce7f3;
        color: #be185d;
        border-radius: 20px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 900;
    }

    .schedule-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-edit-timetable {
        background: linear-gradient(135deg, #f97316, #f59e0b);
        color: white !important;
        border: none;
        border-radius: 14px;
        padding: 12px 22px;
        font-weight: 900;
        text-decoration: none;
        box-shadow: 0 10px 22px rgba(249, 115, 22, 0.20);
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    .empty-text {
        color: #9ca3af;
        font-style: italic;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .schedule-info-grid {
            grid-template-columns: 1fr;
        }

        .schedule-profile {
            flex-direction: column;
            text-align: center;
        }

        .schedule-photo {
            margin-right: 0;
            margin-bottom: 14px;
        }

        .schedule-status {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div class="timetable-card-page">

    {{-- Header --}}
    <div class="timetable-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="timetable-view-icon">
                        <i class="material-symbols-rounded">calendar_month</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Scheduled Timetable Details</h3>
                        <p class="mb-0">View timetable information in a clean schedule card format.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedule Card --}}
    <div class="schedule-card-wrapper">
        <div class="schedule-card">

            <div class="schedule-card-top">
                <div class="schedule-card-title">
                    Student Timetable Record
                </div>

                <div class="schedule-profile">
                    <div class="schedule-photo">
                        {{ strtoupper(substr($student_timetable->user->name ?? 'S', 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="schedule-main-title">
                            {{ $student_timetable->user->name ?? 'Student Not Assigned' }}
                        </h3>

                        <p class="schedule-main-subtitle">
                            Timetable ID: {{ $student_timetable->id }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="schedule-card-body">

                <div class="schedule-section-title">
                    Timetable Information
                </div>

                <div class="schedule-info-grid">

                    {{-- Student --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">person</i>
                            Student
                        </div>

                        <p class="schedule-info-value">
                            {{ $student_timetable->user->name ?? 'Student Not Assigned' }}
                        </p>

                        <p class="schedule-info-subvalue">
                            User ID: {{ $student_timetable->user_id ?? '-' }}
                        </p>
                    </div>

                    {{-- Subject --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">menu_book</i>
                            Subject
                        </div>

                        @if($student_timetable->subject)
                            <p class="schedule-info-value">
                                {{ $student_timetable->subject->subject_code ?? '-' }}
                                -
                                {{ $student_timetable->subject->subject_name ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                Lecturer: {{ $student_timetable->subject->lecturer_name ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                Subject ID: {{ $student_timetable->subject_id ?? '-' }}
                            </p>
                        @else
                            <p class="schedule-info-value empty-text">Subject Not Assigned</p>
                            <p class="schedule-info-subvalue">
                                Subject ID: {{ $student_timetable->subject_id ?? '-' }}
                            </p>
                        @endif
                    </div>

                    {{-- Day --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">today</i>
                            Day
                        </div>

                        <p class="schedule-info-value">
                            {{ $student_timetable->day->day_name ?? 'Day Not Assigned' }}
                        </p>

                        <p class="schedule-info-subvalue">
                            Day ID: {{ $student_timetable->day_id ?? '-' }}
                        </p>
                    </div>

                    {{-- Hall --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">apartment</i>
                            Lecture Hall
                        </div>

                        @if($student_timetable->hall)
                            <p class="schedule-info-value">
                                {{ $student_timetable->hall->lecture_hall_name ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                {{ $student_timetable->hall->lecture_hall_place ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                Hall ID: {{ $student_timetable->hall_id ?? '-' }}
                            </p>
                        @else
                            <p class="schedule-info-value empty-text">Hall Not Assigned</p>
                            <p class="schedule-info-subvalue">
                                Hall ID: {{ $student_timetable->hall_id ?? '-' }}
                            </p>
                        @endif
                    </div>

                    {{-- Class Group --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">school</i>
                            Class Group
                        </div>

                        @if($student_timetable->lecturerGroup)
                            <p class="schedule-info-value">
                                {{ $student_timetable->lecturerGroup->name ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                Part {{ $student_timetable->lecturerGroup->part ?? '-' }}
                            </p>

                            <p class="schedule-info-subvalue">
                                Group ID: {{ $student_timetable->lecturer_group_id ?? '-' }}
                            </p>
                        @else
                            <p class="schedule-info-value empty-text">Class Group Not Assigned</p>
                            <p class="schedule-info-subvalue">
                                Group ID: {{ $student_timetable->lecturer_group_id ?? '-' }}
                            </p>
                        @endif
                    </div>

                    {{-- Record --}}
                    <div class="schedule-info-box">
                        <div class="schedule-info-label">
                            <i class="material-symbols-rounded">confirmation_number</i>
                            Record
                        </div>

                        <p class="schedule-info-value">
                            Timetable #{{ $student_timetable->id }}
                        </p>

                        <p class="schedule-info-subvalue">
                            Active schedule record
                        </p>
                    </div>

                </div>

                {{-- Time --}}
                <div class="time-box">
                    <div class="time-row">
                        <span class="time-pill">
                            <i class="material-symbols-rounded">schedule</i>
                            From: {{ $student_timetable->time_from ?? '-' }}
                        </span>

                        <span class="time-arrow">→</span>

                        <span class="time-pill">
                            <i class="material-symbols-rounded">schedule</i>
                            To: {{ $student_timetable->time_to ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="schedule-status">
                    <p class="status-label">Schedule Status</p>
                    <span class="status-badge">Active Timetable</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="schedule-actions">
        <a href="{{ route('student-timetables.edit', $student_timetable->id) }}" class="btn-edit-timetable">
            <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
            Edit Timetable
        </a>

        <a href="{{ route('student-timetables.index') }}" class="btn-back-timetable">
            <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
            Back to List
        </a>
    </div>

</div>

@endsection