@extends('layouts.template')

@section('title', 'Day Details')
@section('page-title', 'Day Details')

@section('content')

<style>
    .day-card-page {
        padding-top: 5px;
    }

    .day-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .day-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .day-view-header h3,
    .day-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .day-view-icon {
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

    .day-view-icon i {
        color: white;
        font-size: 38px;
    }

    .day-card-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .day-id-card {
        width: 460px;
        max-width: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 45px rgba(219, 39, 119, 0.18);
        border: 1px solid #fce7f3;
    }

    .day-card-top {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .day-card-top::after {
        content: "";
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        position: absolute;
        right: -45px;
        top: -45px;
    }

    .day-card-title {
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .day-card-profile {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .day-photo {
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

    .day-name {
        color: white !important;
        font-weight: 900;
        margin-bottom: 4px;
        font-size: 22px;
    }

    .day-role {
        color: rgba(255,255,255,0.85);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .day-card-body {
        padding: 26px;
    }

    .day-info-row {
        display: flex;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #fce7f3;
    }

    .day-info-row:last-child {
        border-bottom: none;
    }

    .day-info-icon {
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

    .day-info-icon i {
        color: #be185d;
        font-size: 22px;
    }

    .day-info-label {
        color: #be185d;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .day-info-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
        word-break: break-word;
    }

    .day-status {
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

    .day-card-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-edit-day {
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

    .btn-edit-day:hover {
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

    @media (max-width: 576px) {
        .day-card-profile {
            flex-direction: column;
            text-align: center;
        }

        .day-photo {
            margin-right: 0;
            margin-bottom: 14px;
        }

        .day-status {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div class="day-card-page">

    {{-- Header --}}
    <div class="day-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="day-view-icon">
                        <i class="material-symbols-rounded">today</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Day Details</h3>
                        <p class="mb-0">View academic day information in a clean day card format.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Day Card --}}
    <div class="day-card-wrapper">
        <div class="day-id-card">

            <div class="day-card-top">
                <div class="day-card-title">
                    Academic Day Record
                </div>

                <div class="day-card-profile">
                    <div class="day-photo">
                        {{ strtoupper(substr($day->day_name, 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="day-name">{{ $day->day_name }}</h3>
                        <p class="day-role">Timetable Scheduling Day</p>
                    </div>
                </div>
            </div>

            <div class="day-card-body">

                <div class="day-info-row">
                    <div class="day-info-icon">
                        <i class="material-symbols-rounded">confirmation_number</i>
                    </div>
                    <div>
                        <div class="day-info-label">Day ID</div>
                        <p class="day-info-value">{{ $day->id }}</p>
                    </div>
                </div>

                <div class="day-info-row">
                    <div class="day-info-icon">
                        <i class="material-symbols-rounded">calendar_month</i>
                    </div>
                    <div>
                        <div class="day-info-label">Day Name</div>
                        <p class="day-info-value">{{ $day->day_name }}</p>
                    </div>
                </div>

                <div class="day-status">
                    <p class="status-label">Record Status</p>
                    <span class="status-badge">Active Day</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="day-card-actions">
        <a href="{{ route('days.edit', $day->id) }}" class="btn-edit-day">
            <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
            Edit Day
        </a>

        <a href="{{ route('days.index') }}" class="btn-back-day">
            <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
            Back to List
        </a>
    </div>

</div>

@endsection