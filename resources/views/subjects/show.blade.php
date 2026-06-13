@extends('layouts.template')

@section('title', 'Subject Details')
@section('page-title', 'Subject Details')

@section('content')

<style>
    .subject-card-page {
        padding-top: 5px;
    }

    .subject-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .subject-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .subject-view-header h3,
    .subject-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .subject-view-icon {
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

    .subject-view-icon i {
        color: white;
        font-size: 38px;
    }

    .subject-card-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .subject-id-card {
        width: 460px;
        max-width: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 45px rgba(219, 39, 119, 0.18);
        border: 1px solid #fce7f3;
    }

    .subject-card-top {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .subject-card-top::after {
        content: "";
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        position: absolute;
        right: -45px;
        top: -45px;
    }

    .subject-card-title {
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .subject-card-profile {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .subject-photo {
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

    .subject-name {
        color: white !important;
        font-weight: 900;
        margin-bottom: 4px;
        font-size: 22px;
    }

    .subject-role {
        color: rgba(255,255,255,0.85);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .subject-card-body {
        padding: 26px;
    }

    .subject-info-row {
        display: flex;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #fce7f3;
    }

    .subject-info-row:last-child {
        border-bottom: none;
    }

    .subject-info-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #fce7f3;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        flex-shrink: 0;
    }

    .subject-info-icon i {
        color: #be185d;
        font-size: 22px;
    }

    .subject-info-label {
        color: #be185d;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .subject-info-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
        word-break: break-word;
    }

    .subject-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff7fb;
        border-radius: 18px;
        padding: 14px 16px;
        margin-top: 18px;
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

    .subject-card-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-edit-subject {
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

    .empty-text {
        color: #9ca3af;
        font-style: italic;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .subject-card-profile {
            flex-direction: column;
            text-align: center;
        }

        .subject-photo {
            margin-right: 0;
            margin-bottom: 14px;
        }

        .subject-status {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div class="subject-card-page">

    {{-- Header --}}
    <div class="subject-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="subject-view-icon">
                        <i class="material-symbols-rounded">menu_book</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Subject Details</h3>
                        <p class="mb-0">View subject information in a clean subject card format.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subject Card --}}
    <div class="subject-card-wrapper">
        <div class="subject-id-card">

            <div class="subject-card-top">
                <div class="subject-card-title">
                    Academic Subject Record
                </div>

                <div class="subject-card-profile">
                    <div class="subject-photo">
                        {{ strtoupper(substr($subject->subject_name, 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="subject-name">{{ $subject->subject_name }}</h3>
                        <p class="subject-role">Subject Code: {{ $subject->subject_code }}</p>
                    </div>
                </div>
            </div>

            <div class="subject-card-body">

                <div class="subject-info-row">
                    <div class="subject-info-icon">
                        <i class="material-symbols-rounded">qr_code</i>
                    </div>
                    <div>
                        <div class="subject-info-label">Subject Code</div>
                        <p class="subject-info-value">{{ $subject->subject_code }}</p>
                    </div>
                </div>

                <div class="subject-info-row">
                    <div class="subject-info-icon">
                        <i class="material-symbols-rounded">menu_book</i>
                    </div>
                    <div>
                        <div class="subject-info-label">Subject Name</div>
                        <p class="subject-info-value">{{ $subject->subject_name }}</p>
                    </div>
                </div>

                <div class="subject-info-row">
                    <div class="subject-info-icon">
                        <i class="material-symbols-rounded">person</i>
                    </div>
                    <div>
                        <div class="subject-info-label">Lecturer Name</div>

                        @if(!empty($subject->lecturer_name))
                            <p class="subject-info-value">{{ $subject->lecturer_name }}</p>
                        @else
                            <p class="subject-info-value empty-text">Not assigned</p>
                        @endif
                    </div>
                </div>

                <div class="subject-status">
                    <p class="status-label">Record Status</p>
                    <span class="status-badge">Active Subject</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="subject-card-actions">
        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn-edit-subject">
            <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
            Edit Subject
        </a>

        <a href="{{ route('subjects.index') }}" class="btn-back-subject">
            <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
            Back to List
        </a>
    </div>

</div>

@endsection
