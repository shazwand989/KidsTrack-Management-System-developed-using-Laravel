@extends('layouts.template')

@section('title', 'Attendance Records')
@section('page-title', 'Attendance Records')

@section('content')

<style>
    .pg-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pg-header-left h2 {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pg-header-left h2 span { color: #FF6B6B; }
    .pg-header-left p { font-size: 13px; color: #94a3b8; margin: 0; }

    .pg-header-right { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-take {
        background: linear-gradient(to right, #FF6B6B, #FF9E7D);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 7px;
        box-shadow: 0 6px 16px rgba(255,107,107,0.28);
        transition: .2s;
    }

    .btn-take:hover { opacity: .9; transform: translateY(-1px); color: white; }

    .btn-export {
        background: white;
        border: 1px solid #FFE4D6;
        color: #FF6B6B;
        padding: 10px 18px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: .2s;
    }

    .btn-export:hover { background: #FFF5F2; color: #C2410C; }

    .btn-pdf {
        background: #dc2626;
        color: white !important;
        border: none;
        padding: 10px 18px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: .2s;
        box-shadow: 0 6px 16px rgba(220,38,38,0.28);
    }

    .btn-pdf:hover { opacity: .9; transform: translateY(-1px); color: white; }

    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 16px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border-left: 5px solid #FF6B6B;
        transition: transform .2s;
        height: 100%;
    }

    .stat-card:hover { transform: translateY(-3px); }

    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon.pink { background: #FFF5F2; color: #FF6B6B; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-icon.red { background: #fef2f2; color: #dc2626; }
    .stat-icon.blue { background: #eff6ff; color: #3b82f6; }

    .stat-num { font-size: 22px; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 2px; }
    .stat-label { font-size: 11px; color: #94a3b8; font-weight: 600; white-space: nowrap; }

    @media (min-width: 576px) {
        .stat-card { padding: 20px; gap: 16px; }
        .stat-icon { width: 46px; height: 46px; border-radius: 13px; font-size: 20px; }
        .stat-num { font-size: 26px; margin-bottom: 3px; }
        .stat-label { font-size: 12px; }
    }

    .filter-bar {
        background: white;
        border-radius: 18px;
        padding: 16px 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        border: 1px solid #FFF0EC;
        flex-wrap: wrap;
    }

    .search-wrap { flex: 1; position: relative; min-width: 200px; }

    .search-wrap i, .search-wrap span {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #FFB3A0;
        font-size: 14px;
    }

    .search-input {
        width: 100%;
        border: 1px solid #FFE4D6;
        border-radius: 12px;
        padding: 10px 14px 10px 38px;
        font-size: 13px;
        color: #1e293b;
        outline: none;
        background: #fffcfb;
    }

    .search-input:focus { border-color: #FF9E7D; box-shadow: 0 0 0 3px rgba(255,158,125,0.15); }

    .filter-select {
        border: 1px solid #FFE4D6;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
        outline: none;
        background: #fffcfb;
        cursor: pointer;
        min-width: 140px;
    }

    .filter-select option { padding: 8px; }

    .date-input {
        border: 1px solid #FFE4D6;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
        background: #fffcfb;
        cursor: pointer;
    }

    .record-count { font-size: 13px; font-weight: 700; color: #94a3b8; white-space: nowrap; }

    .table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #FFF0EC;
    }

    .pg-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .pg-table thead tr { background: #FFF5F2; }

    .pg-table thead th {
        padding: 12px 14px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        border: none;
        white-space: nowrap;
    }

    .pg-table tbody tr { border-bottom: 1px solid #FFF5F2; transition: background .15s; cursor: pointer; }
    .pg-table tbody tr:last-child { border-bottom: none; }
    .pg-table tbody tr:hover { background: #FFFAF9; }
    .pg-table tbody td { padding: 12px 14px; font-size: 13px; color: #475569; border: none; vertical-align: middle; }

    /* ── MOBILE CARD VIEW ── */
    .mobile-cards { display: none; }

    .mobile-card {
        background: white;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        border: 1px solid #FFF0EC;
        transition: transform .15s;
    }
    .mobile-card:active { transform: scale(0.98); }

    .mobile-card .mc-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .mobile-card .mc-avatar {
        width: 44px; height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 800; font-size: 18px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .mobile-card .mc-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .mobile-card .mc-name { font-weight: 800; color: #1e293b; font-size: 15px; }
    .mobile-card .mc-sub { font-size: 11px; color: #94a3b8; }

    .mobile-card .mc-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .mobile-card .mc-item { font-size: 12px; }
    .mobile-card .mc-item .mc-label { color: #94a3b8; font-weight: 600; font-size: 10px; text-transform: uppercase; }
    .mobile-card .mc-item .mc-value { color: #1e293b; font-weight: 700; font-size: 13px; margin-top: 2px; }

    .mobile-card .mc-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }
    .mobile-card .mc-actions .act-btn {
        flex: 1;
        height: 36px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    /* Show/hide based on screen */
    @media (max-width: 768px) {
        .pg-table { display: none !important; }
        .table-footer { display: none !important; }
        .mobile-cards { display: block; }
        .pg-table thead { display: none; }
    }
    @media (min-width: 769px) {
        .mobile-cards { display: none !important; }
        .pg-table { display: table !important; }
        .table-footer { display: flex !important; }
    }

    .child-cell { display: flex; align-items: center; gap: 12px; }

    .child-avatar {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 16px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .child-avatar img { width:100%; height:100%; object-fit:cover; }

    .child-name { font-weight: 800; color: #1e293b; font-size: 14px; margin: 0 0 2px; }
    .child-sub { font-size: 11px; color: #94a3b8; margin: 0; }

    .classroom-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
    }

    .classroom-badge .color-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.checkin { background: #f0fdf4; color: #16a34a; }
    .status-badge.checkout { background: #eff6ff; color: #3b82f6; }
    .status-badge.late { background: #fef3c7; color: #d97706; }
    .status-badge.absent { background: #fef2f2; color: #dc2626; }
    .status-badge.present { background: #dbeafe; color: #2563eb; }

    .action-btns { display: flex; gap: 6px; flex-wrap: wrap; }

    .act-btn {
        width: 32px; height: 32px;
        border-radius: 9px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        transition: .2s;
    }

    .act-btn.view   { background: #eff6ff; color: #2563eb; }
    .act-btn.edit   { background: #fffbeb; color: #d97706; }
    .act-btn.delete { background: #fef2f2; color: #dc2626; }
    .act-btn.view:hover   { background: #dbeafe; }
    .act-btn.edit:hover   { background: #fef3c7; }
    .act-btn.delete:hover { background: #fee2e2; }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-icon { font-size: 52px; color: #FFD4C8; margin-bottom: 16px; }
    .empty-state h5 { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
    .empty-state p { font-size: 13px; color: #94a3b8; margin-bottom: 20px; }
    .empty-state a { color: #FF6B6B; font-weight: 700; text-decoration: none; }

    .table-footer {
        padding: 14px 20px;
        background: #FFF5F2;
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #16a34a;
        font-weight: 700;
    }

    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 20px;
        background: white;
        border-radius: 16px;
        padding: 14px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }
    .pagination-info {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .pagination-links {
        display: flex;
        gap: 4px;
        align-items: center;
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .pagination-links .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-decoration: none;
        background: white;
        border: 1px solid #e2e8f0;
        transition: all .15s;
    }
    .pagination-links .page-link:hover {
        background: #FFF5F2;
        border-color: #FFD4C8;
        color: #FF6B6B;
    }
    .pagination-links .active .page-link {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(255,107,107,0.25);
    }
    .pagination-links .disabled .page-link {
        color: #cbd5e1;
        pointer-events: none;
        background: #f8fafc;
    }

    /* 🔥 PRINT STYLES */
    @media print {
        .no-print { display: none !important; }
        .btn-take, .btn-export, .btn-pdf, .filter-bar, .pg-header-right, .action-btns { display: none !important; }
        .pg-table { min-width: 100% !important; }
        .stat-row { page-break-inside: avoid; }
    }

    @media (max-width: 768px) {
        .filter-bar { flex-direction: column; }
        .filter-bar .search-wrap { width: 100%; }
        .filter-bar .filter-select,
        .filter-bar .date-input,
        .filter-bar .record-count { width: 100%; }
        .pg-header-right { flex-direction: column; width: 100%; }
        .pg-header-right .btn-take,
        .pg-header-right .btn-export,
        .pg-header-right .btn-pdf { width: 100%; justify-content: center; }
    }
</style>

{{-- Alert Success --}}
@if(session('success'))
<div class="alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="pg-header">
    <div class="pg-header-left">
        <h2><span><i class="fas fa-clipboard-list"></i></span> Attendance Records</h2>
        <p>Manage daily attendance for children</p>
    </div>
    <div class="pg-header-right">
        {{-- 🔥 BUTTON EXPORT PDF --}}
        <a href="#" class="btn-pdf no-print" id="exportPdfBtn" target="_blank" onclick="exportPDF(event)">
            <span>📄</span> Export PDF Report
        </a>
        <a href="#" class="btn-export no-print" onclick="exportCSV()">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('attendance.create') }}" class="btn-take no-print">
            <span>📝</span> Take Attendance
        </a>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-icon pink"><span><i class="fas fa-clipboard-list"></i></span></div>
            <div>
                <div class="stat-num">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-label">Total Records</div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-num">{{ $stats['checkin'] ?? 0 }}</div>
                <div class="stat-label">Check-ins</div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card">
            <div class="stat-icon blue"><span><i class="fas fa-upload"></i></span></div>
            <div>
                <div class="stat-num">{{ $stats['checkout'] ?? 0 }}</div>
                <div class="stat-label">Check-outs</div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="stat-card" style="border-left-color: #d97706;">
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><span>⏰</span></div>
            <div>
                <div class="stat-num">{{ $stats['late'] ?? 0 }}</div>
                <div class="stat-label">Late</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar no-print">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search by child name..." value="{{ request('search') }}">
    </div>

    {{-- FILTER BY CLASSROOM --}}
    <select class="filter-select" id="filterClassroom">
        <option value=""><i class="fas fa-school"></i> All Classrooms</option>
        @foreach($classrooms ?? [] as $classroom)
            <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                <i class="fas fa-school"></i> {{ $classroom->name }}
            </option>
        @endforeach
    </select>

    <select class="filter-select" id="filterStatus">
        <option value="">All Status</option>
        <option value="checkin" {{ request('status') == 'checkin' ? 'selected' : '' }}><i class="fas fa-check-circle"></i> Check-in</option>
        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}><i class="fas fa-check-circle"></i> Present</option>
        <option value="checkout" {{ request('status') == 'checkout' ? 'selected' : '' }}><i class="fas fa-upload"></i> Check-out</option>
        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>⏰ Late</option>
        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}><i class="fas fa-times-circle"></i> Absent</option>
    </select>

    <input type="date" class="date-input" id="filterDate" value="{{ request('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" placeholder="Filter by date">
    <input type="hidden" id="searchValue" value="{{ request('search') }}">
    <span class="record-count" id="recordCount">{{ $stats['total'] ?? 0 }} records</span>
</div>

{{-- Table --}}
<div class="table-card" id="attendanceTable">
    <table class="pg-table" id="attendanceTableContent">
        <thead>
            <tr>
                <th>#</th>
                <th>Child</th>
                <th><i class="fas fa-school"></i> Classroom</th>
                <th>Date</th>
                <th>Status</th>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
                <th>Drop Off By</th>
                <th>Pickup By</th>
                <th class="no-print">Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($attendances as $i => $attendance)
            @php
                $child = $attendance->child;
                $classroom = $child ? $child->classroom : null;
                $classroomName = $classroom ? $classroom->name : 'No Class';
                $classroomColor = $classroom && $classroom->color ? $classroom->color : '#94a3b8';
                $classroomId = $classroom ? $classroom->id : 0;

                $dropOff = $attendance->drop_off_by;
                if ($dropOff && is_numeric($dropOff)) {
                    $parent = \App\Models\User::find($dropOff);
                    if ($parent) {
                        $dropOff = $parent->name;
                    } else {
                        $user = \App\Models\User::find($dropOff);
                        if ($user) {
                            $dropOff = $user->name;
                        }
                    }
                }

                $pickup = $attendance->pickup_by;
                if ($pickup && is_numeric($pickup)) {
                    $parent = \App\Models\User::find($pickup);
                    if ($parent) {
                        $pickup = $parent->name;
                    } else {
                        $user = \App\Models\User::find($pickup);
                        if ($user) {
                            $pickup = $user->name;
                        }
                    }
                }

                $status = $attendance->status;

                // 🔥 CHECK-IN BADGE
                $checkinBadgeClass = 'absent';
                $checkinBadgeIcon = '<i class="fas fa-times-circle"></i>';
                $checkinBadgeText = 'Absent';

                if (in_array($status, ['checkin', 'present'])) {
                    $checkinBadgeClass = 'checkin';
                    $checkinBadgeIcon = '<i class="fas fa-check-circle"></i>';
                    $checkinBadgeText = 'Check-in';
                } elseif ($status == 'checkout') {
                    // If status is checkout, it means they checked in earlier (show present for check-in part)
                    $checkinBadgeClass = 'checkin';
                    $checkinBadgeIcon = '<i class="fas fa-check-circle"></i>';
                    $checkinBadgeText = 'Check-in';
                } elseif ($status == 'late') {
                    $checkinBadgeClass = 'late';
                    $checkinBadgeIcon = '⏰';
                    $checkinBadgeText = 'Late';
                }

                // 🔥 CHECK-OUT BADGE
                $hasCheckout = $attendance->checkout_time ? true : false;
                $checkoutBadgeClass = 'checkout';
                $checkoutBadgeIcon = '<i class="fas fa-upload"></i>';
                $checkoutBadgeText = 'Check-out';
            @endphp
            <tr
                data-child="{{ strtolower($child->name ?? '') }}"
                data-status="{{ $status }}{{ $hasCheckout ? ' checkout' : '' }}"
                data-date="{{ $attendance->date }}"
                data-classroom="{{ $classroomId }}"
            >
                <td style="color:#94a3b8; font-weight:700;">{{ $attendances->firstItem() + $i }}</td>

                <td>
                    <div class="child-cell">
                        <div class="child-avatar">
                            @if($child && $child->photo)
                                <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($child->name ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="child-name">{{ $child->name ?? 'Child not found' }}</p>
                            <p class="child-sub">
                                <i class="fas fa-child"></i> {{ $child->age ?? 'N/A' }} years old
                            </p>
                        </div>
                    </div>
                </td>

                {{-- CLASSROOM COLUMN --}}
                <td>
                    <span class="classroom-badge">
                        <span class="color-dot" style="background: {{ $classroomColor }};"></span>
                        {{ $classroomName }}
                    </span>
                </td>

                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>

                <td>
                    <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        {{-- CHECK-IN STATUS BADGE --}}
                        <span class="status-badge {{ $checkinBadgeClass }}">
                            {!! $checkinBadgeIcon !!} {{ $checkinBadgeText }}
                            @if($status == 'late' && $attendance->late_reason)
                                <span style="font-size:9px; opacity:0.7;">({{ $attendance->late_reason }})</span>
                            @endif
                        </span>
                        {{-- CHECK-OUT STATUS BADGE (only if checked out) --}}
                        @if($hasCheckout)
                            <span class="status-badge {{ $checkoutBadgeClass }}">
                                {!! $checkoutBadgeIcon !!} {{ $checkoutBadgeText }}
                            </span>
                        @endif
                    </div>
                </td>

                <td>{{ $attendance->checkin_time ? \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->checkout_time ? \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') : '-' }}</td>

                <td>{{ $dropOff ?? '-' }}</td>
                <td>{{ $pickup ?? '-' }}</td>

                {{-- 🔥 ACTION BUTTONS - TAMBAH VIEW --}}
                <td class="no-print">
                    <div class="action-btns">
                        {{-- 🔥 BUTTON VIEW --}}
                        <a href="{{ route('attendance.show', $attendance->id) }}" class="act-btn view" title="View Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('attendance.edit', $attendance->id) }}" class="act-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(in_array(auth()->user()->role ?? '', ['admin', 'teacher']))
                        <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="act-btn delete" title="Delete"
                                onclick="return confirm('Delete this attendance record?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                        <h5>No attendance records found</h5>
                        <p>Start by taking attendance for today.</p>
                        <a href="{{ route('attendance.create') }}">📝 Take Attendance</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($attendances->count() > 0)
    <div class="table-footer">
        <span>ℹ️</span>
        <span>Click any row to view details</span>
        <span>{{ $stats['total'] ?? 0 }} total records</span>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if(method_exists($attendances, 'links') && $attendances->hasPages())
<div class="pagination-wrap no-print">
    <div class="pagination-info">
        Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} results
    </div>
    <ul class="pagination-links">
        @if($attendances->onFirstPage())
            <li class="disabled"><span class="page-link">« Prev</span></li>
        @else
            <li><a class="page-link" href="{{ $attendances->previousPageUrl() }}">« Prev</a></li>
        @endif

        @foreach($attendances->getUrlRange(1, $attendances->lastPage()) as $page => $url)
            <li class="{{ $page == $attendances->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
        @endforeach

        @if($attendances->hasMorePages())
            <li><a class="page-link" href="{{ $attendances->nextPageUrl() }}">Next »</a></li>
        @else
            <li class="disabled"><span class="page-link">Next »</span></li>
        @endif
    </ul>
</div>
@endif

<script>
    // 🔥 SEARCH AND FILTER FUNCTIONALITY
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') submitFilters();
    });
    document.getElementById('filterStatus').addEventListener('change', submitFilters);
    document.getElementById('filterDate').addEventListener('change', submitFilters);
    document.getElementById('filterClassroom').addEventListener('change', submitFilters);

    function getFilterParams() {
        var params = new URLSearchParams();
        var search = document.getElementById('searchInput').value.trim();
        var status = document.getElementById('filterStatus').value;
        var date = document.getElementById('filterDate').value;
        var classroom = document.getElementById('filterClassroom').value;
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        if (date) params.set('date', date);
        if (classroom) params.set('classroom_id', classroom);
        return params.toString();
    }

    function submitFilters() {
        var qs = getFilterParams();
        window.location.href = '{{ route('attendance.index') }}' + (qs ? '?' + qs : '');
    }

    function exportPDF(e) {
        e.preventDefault();
        var qs = getFilterParams();
        var url = '{{ route('attendance.export.pdf') }}';
        if (qs) url += '?' + qs;
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = url;
        document.body.appendChild(iframe);
        setTimeout(function(){ iframe.remove(); }, 10000);
    }

    function exportCSV() {
        var qs = getFilterParams();
        window.location.href = '{{ route('attendance.index') }}/export-csv' + (qs ? '?' + qs : '');
    }
</script>

@endsection
