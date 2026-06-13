@extends('layouts.template')

@section('title', 'Hall Details')
@section('page-title', 'Hall Details')

@section('content')

<style>
    .hall-card-page {
        padding-top: 5px;
    }

    .hall-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .hall-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .hall-view-header h3,
    .hall-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .hall-view-icon {
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

    .hall-view-icon i {
        color: white;
        font-size: 38px;
    }

    .hall-card-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .hall-id-card {
        width: 460px;
        max-width: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 45px rgba(219, 39, 119, 0.18);
        border: 1px solid #fce7f3;
    }

    .hall-card-top {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hall-card-top::after {
        content: "";
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        position: absolute;
        right: -45px;
        top: -45px;
    }

    .hall-card-title {
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .hall-card-profile {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .hall-photo {
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

    .hall-name {
        color: white !important;
        font-weight: 900;
        margin-bottom: 4px;
        font-size: 22px;
    }

    .hall-role {
        color: rgba(255,255,255,0.85);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .hall-card-body {
        padding: 26px;
    }

    .hall-info-row {
        display: flex;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #fce7f3;
    }

    .hall-info-row:last-child {
        border-bottom: none;
    }

    .hall-info-icon {
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

    .hall-info-icon i {
        color: #be185d;
        font-size: 22px;
    }

    .hall-info-label {
        color: #be185d;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .hall-info-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
        word-break: break-word;
    }

    .hall-status {
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

    .hall-card-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-edit-hall {
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

    .btn-edit-hall:hover {
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

    @media (max-width: 576px) {
        .hall-card-profile {
            flex-direction: column;
            text-align: center;
        }

        .hall-photo {
            margin-right: 0;
            margin-bottom: 14px;
        }

        .hall-status {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div class="hall-card-page">

    {{-- Header --}}
    <div class="hall-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="hall-view-icon">
                        <i class="material-symbols-rounded">apartment</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Hall Details</h3>
                        <p class="mb-0">View lecture hall information in a clean hall card format.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hall Card --}}
    <div class="hall-card-wrapper">
        <div class="hall-id-card">

            <div class="hall-card-top">
                <div class="hall-card-title">
                    Lecture Hall Record
                </div>

                <div class="hall-card-profile">
                    <div class="hall-photo">
                        {{ strtoupper(substr($hall->lecture_hall_name, 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="hall-name">{{ $hall->lecture_hall_name }}</h3>
                        <p class="hall-role">{{ $hall->lecture_hall_place }}</p>
                    </div>
                </div>
            </div>

            <div class="hall-card-body">

                <div class="hall-info-row">
                    <div class="hall-info-icon">
                        <i class="material-symbols-rounded">confirmation_number</i>
                    </div>
                    <div>
                        <div class="hall-info-label">Hall ID</div>
                        <p class="hall-info-value">{{ $hall->id }}</p>
                    </div>
                </div>

                <div class="hall-info-row">
                    <div class="hall-info-icon">
                        <i class="material-symbols-rounded">apartment</i>
                    </div>
                    <div>
                        <div class="hall-info-label">Hall Name</div>
                        <p class="hall-info-value">{{ $hall->lecture_hall_name }}</p>
                    </div>
                </div>

                <div class="hall-info-row">
                    <div class="hall-info-icon">
                        <i class="material-symbols-rounded">location_on</i>
                    </div>
                    <div>
                        <div class="hall-info-label">Hall Place</div>
                        <p class="hall-info-value">{{ $hall->lecture_hall_place }}</p>
                    </div>
                </div>

                <div class="hall-status">
                    <p class="status-label">Record Status</p>
                    <span class="status-badge">Active Hall</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="hall-card-actions">
        <a href="{{ route('halls.edit', $hall->id) }}" class="btn-edit-hall">
            <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
            Edit Hall
        </a>

        <a href="{{ route('halls.index') }}" class="btn-back-hall">
            <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
            Back to List
        </a>
    </div>

</div>

@endsection