@extends('layouts.template')

@section('title', 'Lecturer Group Details')
@section('page-title', 'Lecturer Group Details')

@section('content')

<style>
    .lecturer-group-card-page {
        padding-top: 5px;
    }

    .lecturer-group-view-header {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        color: white;
        border-radius: 22px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 16px 35px rgba(219, 39, 119, 0.22);
        position: relative;
        overflow: hidden;
    }

    .lecturer-group-view-header::after {
        content: "";
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 50%;
        position: absolute;
        right: -55px;
        top: -55px;
    }

    .lecturer-group-view-header h3,
    .lecturer-group-view-header p {
        color: white !important;
        position: relative;
        z-index: 2;
    }

    .lecturer-group-view-icon {
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

    .lecturer-group-view-icon i {
        color: white;
        font-size: 38px;
    }

    .lecturer-group-card-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .lecturer-group-id-card {
        width: 460px;
        max-width: 100%;
        border-radius: 28px;
        overflow: hidden;
        background: white;
        box-shadow: 0 20px 45px rgba(219, 39, 119, 0.18);
        border: 1px solid #fce7f3;
    }

    .lecturer-group-card-top {
        background: linear-gradient(135deg, #c026d3, #db2777, #ec4899);
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .lecturer-group-card-top::after {
        content: "";
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        position: absolute;
        right: -45px;
        top: -45px;
    }

    .lecturer-group-card-title {
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: white;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .lecturer-group-card-profile {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .lecturer-group-photo {
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

    .lecturer-group-name {
        color: white !important;
        font-weight: 900;
        margin-bottom: 4px;
        font-size: 22px;
    }

    .lecturer-group-role {
        color: rgba(255,255,255,0.85);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .lecturer-group-card-body {
        padding: 26px;
    }

    .lecturer-group-info-row {
        display: flex;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid #fce7f3;
    }

    .lecturer-group-info-row:last-child {
        border-bottom: none;
    }

    .lecturer-group-info-icon {
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

    .lecturer-group-info-icon i {
        color: #be185d;
        font-size: 22px;
    }

    .lecturer-group-info-label {
        color: #be185d;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .lecturer-group-info-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 0;
        word-break: break-word;
    }

    .lecturer-group-status {
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

    .lecturer-group-card-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .btn-edit-lecturer-group {
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

    @media (max-width: 576px) {
        .lecturer-group-card-profile {
            flex-direction: column;
            text-align: center;
        }

        .lecturer-group-photo {
            margin-right: 0;
            margin-bottom: 14px;
        }

        .lecturer-group-status {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div class="lecturer-group-card-page">

    {{-- Header --}}
    <div class="lecturer-group-view-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="lecturer-group-view-icon">
                        <i class="material-symbols-rounded">school</i>
                    </div>

                    <div>
                        <h3 class="mb-1">Class Group Details</h3>
                        <p class="mb-0">View class group information in a clean group card format.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lecturer Group Card --}}
    <div class="lecturer-group-card-wrapper">
        <div class="lecturer-group-id-card">

            <div class="lecturer-group-card-top">
                <div class="lecturer-group-card-title">
                    Lecturer Group Record
                </div>

                <div class="lecturer-group-card-profile">
                    <div class="lecturer-group-photo">
                        {{ strtoupper(substr($lecturer_group->name, 0, 1)) }}
                    </div>

                    <div>
                        <h3 class="lecturer-group-name">{{ $lecturer_group->name }}</h3>
                        <p class="lecturer-group-role">Part {{ $lecturer_group->part }}</p>
                    </div>
                </div>
            </div>

            <div class="lecturer-group-card-body">

                <div class="lecturer-group-info-row">
                    <div class="lecturer-group-info-icon">
                        <i class="material-symbols-rounded">confirmation_number</i>
                    </div>
                    <div>
                        <div class="lecturer-group-info-label">Group ID</div>
                        <p class="lecturer-group-info-value">{{ $lecturer_group->id }}</p>
                    </div>
                </div>

                <div class="lecturer-group-info-row">
                    <div class="lecturer-group-info-icon">
                        <i class="material-symbols-rounded">groups</i>
                    </div>
                    <div>
                        <div class="lecturer-group-info-label">Group Name</div>
                        <p class="lecturer-group-info-value">{{ $lecturer_group->name }}</p>
                    </div>
                </div>

                <div class="lecturer-group-info-row">
                    <div class="lecturer-group-info-icon">
                        <i class="material-symbols-rounded">school</i>
                    </div>
                    <div>
                        <div class="lecturer-group-info-label">Part</div>
                        <p class="lecturer-group-info-value">{{ $lecturer_group->part }}</p>
                    </div>
                </div>

                <div class="lecturer-group-status">
                    <p class="status-label">Record Status</p>
                    <span class="status-badge">Active Group</span>
                </div>

            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="lecturer-group-card-actions">
        <a href="{{ route('lecturer-groups.edit', $lecturer_group->id) }}" class="btn-edit-lecturer-group">
            <i class="material-symbols-rounded" style="font-size:20px;">edit</i>
            Edit class Group
        </a>

        <a href="{{ route('lecturer-groups.index') }}" class="btn-back-lecturer-group">
            <i class="material-symbols-rounded" style="font-size:20px;">arrow_back</i>
            Back to List
        </a>
    </div>

</div>

@endsection