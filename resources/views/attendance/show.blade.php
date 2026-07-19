@extends('layouts.template')

@section('title', 'Attendance Details')
@section('page-title', 'Attendance Details')

@section('content')

<style>
    .detail-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .detail-header h2 {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-header .sub {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 400;
    }

    .btn-back {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-back:hover { background: #e2e8f0; color: #1e293b; }

    .btn-edit {
        background: #fffbeb;
        color: #d97706;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-edit:hover { background: #fef3c7; }

    .btn-delete {
        background: #fef2f2;
        color: #dc2626;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
        cursor: pointer;
    }

    .btn-delete:hover { background: #fee2e2; }

    .btn-pdf-single {
        background: #dc2626;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-pdf-single:hover { opacity: .9; color: white; }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .detail-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #FFF0EC;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    }

    .detail-card .card-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFE4D6;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #FFF5F2;
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-row .label {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-row .value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }

    .status-badge-large {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
    }

    .status-badge-large.checkin { background: #f0fdf4; color: #16a34a; }
    .status-badge-large.checkout { background: #eff6ff; color: #3b82f6; }
    .status-badge-large.late { background: #fef3c7; color: #d97706; }
    .status-badge-large.absent { background: #fef2f2; color: #dc2626; }
    .status-badge-large.present { background: #dbeafe; color: #2563eb; }

    .child-profile {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 14px;
        margin-bottom: 16px;
    }

    .child-profile .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 24px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .child-profile .avatar img { width:100%; height:100%; object-fit:cover; }

    .child-profile .info .name {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .child-profile .info .sub {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #e2e8f0;
        border-radius: 3px;
    }

    .timeline-item {
        position: relative;
        padding: 12px 0 12px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .timeline-item:last-child { border-bottom: none; }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 16px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #cbd5e1;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #cbd5e1;
    }

    .timeline-item.checkin::before { background: #22c55e; box-shadow: 0 0 0 2px #22c55e; }
    .timeline-item.checkout::before { background: #ef4444; box-shadow: 0 0 0 2px #ef4444; }
    .timeline-item.late::before { background: #d97706; box-shadow: 0 0 0 2px #d97706; }

    .timeline-item .time {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
    }

    .timeline-item .event {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }

    .timeline-item .detail {
        font-size: 12px;
        color: #64748b;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #FFF0EC;
    }

    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
        .detail-header { flex-direction: column; align-items: flex-start; }
        .detail-header .actions { width: 100%; flex-wrap: wrap; }
        .detail-row { flex-direction: column; gap: 4px; }
        .detail-row .value { text-align: left; max-width: 100%; }
    }
</style>

<div class="detail-container">

    {{-- Header --}}
    <div class="detail-header">
        <div>
            <h2>
                <span>📋</span> Attendance Details
                <span class="sub">#{{ $attendance->id }}</span>
            </h2>
        </div>
        <div class="actions" style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('attendance.index') }}" class="btn-back">
                <span>⬅️</span> Back
            </a>
            <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn-edit">
                <span>✏️</span> Edit
            </a>
            <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" onclick="return confirm('Delete this attendance record?')">
                    <span>🗑️</span> Delete
                </button>
            </form>
            <a href="{{ route('attendance.export.single', $attendance->id) }}" class="btn-pdf-single" target="_blank">
                <span>📄</span> Export PDF
            </a>
        </div>
    </div>

    {{-- Detail Grid --}}
    <div class="detail-grid">

        {{-- Left Column: Child Info & Status --}}
        <div class="detail-card">
            <div class="card-title">
                <span>👶</span> Child Information
            </div>

            <div class="child-profile">
                <div class="avatar">
                    @if($attendance->child && $attendance->child->photo)
                        <img src="{{ asset('storage/'.$attendance->child->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr($attendance->child->name ?? '?', 0, 1)) }}
                    @endif
                </div>
                <div class="info">
                    <p class="name">{{ $attendance->child->name ?? 'Child not found' }}</p>
                    <p class="sub">
                        @if($attendance->child && $attendance->child->classroom)
                            🏫 {{ $attendance->child->classroom->name }}
                        @else
                            No class
                        @endif
                    </p>
                </div>
            </div>

            <div class="detail-row">
                <span class="label">📊 Status</span>
                <span class="value">
                    @php
                        $status = $attendance->status;
                        $badgeClass = 'absent';
                        $badgeIcon = '❌';
                        $badgeText = 'Absent';
                        
                        if (in_array($status, ['checkin', 'present'])) {
                            $badgeClass = 'checkin';
                            $badgeIcon = '✅';
                            $badgeText = 'Check-in';
                        } elseif ($status == 'checkout') {
                            $badgeClass = 'checkout';
                            $badgeIcon = '📤';
                            $badgeText = 'Check-out';
                        } elseif ($status == 'late') {
                            $badgeClass = 'late';
                            $badgeIcon = '⏰';
                            $badgeText = 'Late';
                        }
                    @endphp
                    <span class="status-badge-large {{ $badgeClass }}">
                        {{ $badgeIcon }} {{ $badgeText }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="label">📅 Date</span>
                <span class="value">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y (l)') }}</span>
            </div>

            <div class="detail-row">
                <span class="label">⏰ Check-in Time</span>
                <span class="value">{{ $attendance->checkin_time ? \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') : '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="label">⏰ Check-out Time</span>
                <span class="value">{{ $attendance->checkout_time ? \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') : '-' }}</span>
            </div>

            @if($attendance->is_late)
            <div class="detail-row" style="background:#fef3c7; padding:10px 12px; border-radius:8px; margin-top:8px;">
                <span class="label">⏰ Late Reason</span>
                <span class="value" style="color:#d97706; font-size:13px;">{{ $attendance->late_reason ?? 'No reason provided' }}</span>
            </div>
            @endif
        </div>

        {{-- Right Column: Parent Info --}}
        <div class="detail-card">
            <div class="card-title">
                <span>👨‍👩‍👦</span> Parent / Guardian Information
            </div>

            <div class="detail-row">
                <span class="label">📥 Drop Off By</span>
                <span class="value">
                    @php
                        $dropOff = $attendance->drop_off_by;
                        if ($dropOff && is_numeric($dropOff)) {
                            $parent = \App\Models\ParentModel::find($dropOff);
                            if ($parent) {
                                $dropOff = $parent->name;
                            } else {
                                $user = \App\Models\User::find($dropOff);
                                if ($user) {
                                    $dropOff = $user->name;
                                }
                            }
                        }
                    @endphp
                    {{ $dropOff ?? '-' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="label">📤 Pickup By</span>
                <span class="value">
                    @php
                        $pickup = $attendance->pickup_by;
                        if ($pickup && is_numeric($pickup)) {
                            $parent = \App\Models\ParentModel::find($pickup);
                            if ($parent) {
                                $pickup = $parent->name;
                            } else {
                                $user = \App\Models\User::find($pickup);
                                if ($user) {
                                    $pickup = $user->name;
                                }
                            }
                        }
                    @endphp
                    {{ $pickup ?? '-' }}
                </span>
            </div>

            <div class="detail-row">
                <span class="label">✅ Verified</span>
                <span class="value">
                    @if($attendance->is_verified)
                        <span style="color:#16a34a;">✅ Yes</span>
                    @else
                        <span style="color:#dc2626;">❌ No</span>
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="label">🆔 Record ID</span>
                <span class="value" style="font-size:12px; color:#94a3b8;">#{{ $attendance->id }}</span>
            </div>

            <div class="detail-row">
                <span class="label">📝 Created At</span>
                <span class="value" style="font-size:12px; color:#94a3b8;">{{ $attendance->created_at ? \Carbon\Carbon::parse($attendance->created_at)->format('d M Y h:i A') : '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="label">🔄 Updated At</span>
                <span class="value" style="font-size:12px; color:#94a3b8;">{{ $attendance->updated_at ? \Carbon\Carbon::parse($attendance->updated_at)->format('d M Y h:i A') : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Timeline / Audit Trail --}}
    <div class="detail-card" style="margin-bottom:24px;">
        <div class="card-title">
            <span>🕐</span> Audit Trail / Timeline
        </div>

        <div class="timeline">
            {{-- Check-in Event --}}
            @if($attendance->checkin_time)
            <div class="timeline-item checkin">
                <div class="time">{{ \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') }}</div>
                <div class="event">✅ Check-in</div>
                <div class="detail">
                    Child checked in at {{ \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') }}
                    @if($attendance->is_late)
                        <span style="color:#d97706;">(Late)</span>
                    @endif
                </div>
            </div>
            @endif

            {{-- Check-out Event --}}
            @if($attendance->checkout_time)
            <div class="timeline-item checkout">
                <div class="time">{{ \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') }}</div>
                <div class="event">📤 Check-out</div>
                <div class="detail">
                    Child checked out at {{ \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') }}
                </div>
            </div>
            @endif

            {{-- No Events --}}
            @if(!$attendance->checkin_time && !$attendance->checkout_time)
            <div class="timeline-item" style="text-align:center; color:#94a3b8; padding:20px 0;">
                <span style="font-size:32px;">📭</span>
                <p style="margin-top:8px;">No activity recorded for this attendance</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="action-bar">
        <a href="{{ route('attendance.index') }}" class="btn-back">
            <span>⬅️</span> Back to List
        </a>
        <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn-edit">
            <span>✏️</span> Edit Record
        </a>
        <a href="{{ route('attendance.export.single', $attendance->id) }}" class="btn-pdf-single" target="_blank">
            <span>📄</span> Export PDF
        </a>
    </div>

</div>

@endsection