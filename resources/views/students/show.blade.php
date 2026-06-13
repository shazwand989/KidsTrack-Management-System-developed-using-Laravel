@extends('layouts.template')

@section('title', 'View Student')
@section('page-title', 'View Student')

@section('content')

<style>
    .view-student-page {
        padding-top: 5px;
    }

    .student-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .student-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .student-view-header h3,
    .student-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .student-view-icon {
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

    .student-view-icon i {
        color: white;
        font-size: 38px;
    }

    .student-profile-card {
        border: none !important;
        border-radius: 22px !important;
        box-shadow: 0 12px 30px rgba(219, 39, 119, 0.08) !important;
        background: white;
        overflow: hidden;
    }

    .student-profile-top {
        padding: 30px;
        border-bottom: 1px solid #fce7f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .student-main-info {
        display: flex;
        align-items: center;
    }

    .student-avatar-large {
        width: 84px;
        height: 84px;
        border-radius: 24px;
        background: linear-gradient(135deg, #c026d3, #ec4899);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 900;
        margin-right: 18px;
        box-shadow: 0 12px 24px rgba(219, 39, 119, 0.22);
        text-transform: uppercase;
    }

    .student-profile-name {
        color: #831843;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .student-profile-subtitle {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .student-status-badge {
        background: #fce7f3;
        color: #be185d;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .student-details-body {
        padding: 30px;
    }

    .detail-section-title {
        color: #831843;
        font-weight: 900;
        font-size: 16px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #fce7f3;
    }

    .detail-box {
        background: #fff7fb;
        border: 1px solid #fce7f3;
        border-radius: 18px;
        padding: 18px;
        height: 100%;
        transition: 0.2s ease;
    }

    .detail-box:hover {
        background: #fdf2f8;
        transform: translateY(-2px);
    }

    .detail-label {
        color: #be185d;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-label i {
        color: #be185d;
        font-size: 19px;
    }

    .detail-value {
        color: #111827;
        font-size: 16px;
        font-weight: 700;
        word-break: break-word;
        margin-bottom: 0;
    }

    .empty-text {
        color: #9ca3af;
        font-style: italic;
        font-weight: 600;
    }

    .button-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 26px;
    }

    .btn-edit-student {
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

    .btn-edit-student:hover {
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

    @media (max-width: 768px) {
        .student-profile-top {
            align-items: flex-start;
        }

        .student-main-info {
            align-items: flex-start;
        }

        .student-avatar-large {
            width: 70px;
            height: 70px;
            font-size: 30px;
        }
    }
</style>

<div class="view-student-page">

    {{-- Header --}}
    <div class="student-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="student-view-icon">
                        <i class="material-symbols-rounded">visibility</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Student Details</h3>
                        <p class="mb-0">View complete student information stored in the system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Student Detail Card --}}
    <div class="card student-profile-card">

        <div class="student-profile-top">
            <div class="student-main-info">
                <div class="student-avatar-large">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>

                <div>
                    <h3 class="student-profile-name">{{ $student->name }}</h3>
                    <p class="student-profile-subtitle">Student ID: {{ $student->id }}</p>
                </div>
            </div>

            <span class="student-status-badge">
                <i class="material-symbols-rounded" style="font-size:18px;">verified</i>
                Active Student
            </span>
        </div>

        <div class="student-details-body">
            <div class="detail-section-title">
                Student Information
            </div>

            <div class="row">
                {{-- Name --}}
                <div class="col-md-6 mb-4">
                    <div class="detail-box">
                        <div class="detail-label">
                            <i class="material-symbols-rounded">badge</i>
                            Student Name
                        </div>
                        <p class="detail-value">{{ $student->name }}</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-md-6 mb-4">
                    <div class="detail-box">
                        <div class="detail-label">
                            <i class="material-symbols-rounded">mail</i>
                            Email Address
                        </div>
                        <p class="detail-value">{{ $student->email ?? '-' }}</p>
                    </div>
                </div>

                {{-- Phone Number --}}
                <div class="col-md-6 mb-4">
                    <div class="detail-box">
                        <div class="detail-label">
                            <i class="material-symbols-rounded">call</i>
                            Phone Number
                        </div>

                        @if(!empty($student->phone_number))
                            <p class="detail-value">{{ $student->phone_number }}</p>
                        @else
                            <p class="detail-value empty-text">Not provided</p>
                        @endif
                    </div>
                </div>

                {{-- Address --}}
                <div class="col-md-6 mb-4">
                    <div class="detail-box">
                        <div class="detail-label">
                            <i class="material-symbols-rounded">home</i>
                            Address
                        </div>

                        @if(!empty($student->address))
                            <p class="detail-value">{{ $student->address }}</p>
                        @else
                            <p class="detail-value empty-text">Not provided</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="button-row">
                <a href="{{ route('students.edit', $student->id) }}" class="btn-edit-student">
                    <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
                    Edit Student
                </a>

                <a href="{{ route('students.index') }}" class="btn-back-student">
                    <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
                    Back to List
                </a>
            </div>
        </div>

    </div>

</div>

@endsection