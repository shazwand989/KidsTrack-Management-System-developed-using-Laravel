@extends('layouts.template')

@section('title', 'Classroom Details')
@section('page-title', 'Classroom Details')

@section('content')

<style>
    .classroom-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .classroom-header {
        background: linear-gradient(135deg, {{ $classroom->color ?? '#FF6B6B' }}, {{ $classroom->color ?? '#FF6B6B' }}dd);
        border-radius: 25px;
        padding: 30px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .classroom-header::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .classroom-info h1 {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .classroom-info .code {
        font-size: 14px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .classroom-actions {
        display: flex;
        gap: 12px;
    }

    .btn-edit {
        background: white;
        color: {{ $classroom->color ?? '#FF6B6B' }} !important;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-back {
        background: rgba(255,255,255,0.2);
        color: white !important;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-edit:hover, .btn-back:hover { transform: translateY(-2px); }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 18px;
        text-align: center;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border: 1px solid #FFF0EC;
        transition: transform .2s;
    }

    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon { font-size: 28px; margin-bottom: 8px; }
    .stat-number { font-size: 28px; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .stat-label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .info-card {
        background: white;
        border-radius: 18px;
        padding: 20px;
        border: 1px solid #FFF0EC;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    }

    .info-card-title {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFE4D6;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #FFF5F2;
    }

    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; }
    .info-value { font-size: 14px; font-weight: 700; color: #1e293b; }

    .seatmap-section {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #FFF0EC;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        margin-bottom: 24px;
    }

    .seatmap-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .seatmap-title {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend { display: flex; gap: 15px; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; }
    .legend-color { width: 14px; height: 14px; border-radius: 4px; }
    .legend-color.checkin  { background: #22c55e; }
    .legend-color.checkout { background: #ef4444; }
    .legend-color.absent   { background: #f59e0b; }

    .seatmap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }

    .seat-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px;
        transition: all .2s;
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
    }

    .seat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

    .seat-card.checkin {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border-color: #22c55e;
    }

    .seat-card.checkout {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .seat-card.absent {
        background: #fffbeb;
        border-color: #f59e0b;
        opacity: 0.8;
    }

    .seat-avatar {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 12px;
        overflow: hidden;
    }

    .seat-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .seat-name { font-weight: 800; color: #1e293b; font-size: 14px; margin-bottom: 4px; }

    .seat-status {
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
    }

    .empty-seat {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        text-align: center;
        padding: 30px;
        border-radius: 16px;
        color: #94a3b8;
    }

    .progress-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-fill {
        background: {{ $classroom->color ?? '#FF6B6B' }};
        height: 100%;
        border-radius: 10px;
        transition: width .3s;
    }

    .live-dot {
        width: 8px; height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .info-grid { grid-template-columns: 1fr; }
        .seatmap-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        .header-content { flex-direction: column; text-align: center; }
    }
</style>

<div class="classroom-container">

    {{-- Header --}}
    <div class="classroom-header">
        <div class="header-content">
            <div class="classroom-info">
                <h1>
                    <span>🏫</span> {{ $classroom->name }}
                </h1>
                <div class="code">
                    <span>📛 Code: {{ $classroom->code }}</span>
                    <span>👥 Capacity: {{ $classroom->capacity }} children</span>
                    <span>📊 Enrollment: {{ $children->count() }}/{{ $classroom->capacity }}</span>
                    <div class="progress-bar" style="width: 150px;">
                        <div class="progress-fill" style="width: {{ $stats['capacity_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
            <div class="classroom-actions">
                <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn-edit">
                    <span>✏️</span> Edit Class
                </a>
                <a href="{{ route('classrooms.index') }}" class="btn-back">
                    <span>⬅️</span> Back
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👶</div>
            <div class="stat-number">{{ $stats['total_children'] }}</div>
            <div class="stat-label">Total Children</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-number" style="color:#16a34a;">{{ $stats['total_present'] }}</div>
            <div class="stat-label">Checked In</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📤</div>
            <div class="stat-number" style="color:#dc2626;">{{ $stats['total_checkout'] }}</div>
            <div class="stat-label">Checked Out</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏰</div>
            <div class="stat-number" style="color:#d97706;">{{ $stats['total_absent'] }}</div>
            <div class="stat-label">Belum Hadir</div>
        </div>
    </div>

    {{-- Information Grid --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-title">
                <span>📋</span> Class Details
            </div>
            <div class="info-row">
                <div class="info-label"><span>🏷️</span> Class Name</div>
                <div class="info-value">{{ $classroom->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>🔢</span> Class Code</div>
                <div class="info-value">{{ $classroom->code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>👥</span> Age Group</div>
                <div class="info-value">{{ $classroom->age_group }}</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>📊</span> Age Range</div>
                <div class="info-value">{{ $classroom->min_age }} - {{ $classroom->max_age }} years</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>⚙️</span> Status</div>
                <div class="info-value">
                    @if($classroom->status == 'active')
                        <span style="color:#16a34a;">✅ Active</span>
                    @else
                        <span style="color:#dc2626;">❌ Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-title">
                <span>⏰</span> Schedule & Teacher
            </div>
            <div class="info-row">
                <div class="info-label"><span>🕐</span> Start Time</div>
                <div class="info-value">{{ date('h:i A', strtotime($classroom->start_time)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>🕔</span> End Time</div>
                <div class="info-value">{{ date('h:i A', strtotime($classroom->end_time)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>👩‍🏫</span> Class Teacher</div>
                <div class="info-value">
                    @if($classroom->teacher)
                        {{ $classroom->teacher->name }}
                        <span style="font-size:11px; color:#94a3b8;">({{ $classroom->teacher->position }})</span>
                    @else
                        <span style="color:#cbd5e1;">Not assigned</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label"><span>📝</span> Description</div>
                <div class="info-value">{{ $classroom->description ?? 'No description' }}</div>
            </div>
        </div>
    </div>

    {{-- Seatmap Section --}}
    <div class="seatmap-section">
        <div class="seatmap-header">
            <div class="seatmap-title">
                <span class="live-dot"></span>
                🪑 Attendance Hari Ini
                <span style="font-size:12px; color:#94a3b8;">({{ $children->count() }} children)</span>
            </div>
            <div class="legend">
                <div class="legend-item"><div class="legend-color checkin"></div><span>Checked In</span></div>
                <div class="legend-item"><div class="legend-color checkout"></div><span>Checked Out</span></div>
                <div class="legend-item"><div class="legend-color absent"></div><span>Belum Hadir</span></div>
            </div>
        </div>

        <div class="seatmap-grid">
            @forelse($children as $child)
                @php
                    $att       = $attendances->get($child->id);
                    $attStatus = $att ? $att->status : 'absent';
                @endphp
                <div class="seat-card {{ $attStatus }}"
                     onclick="window.location='{{ route('children.show', $child->id) }}'">
                    <div class="seat-avatar">
                        @if($child->photo)
                            <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                        @else
                            {{ strtoupper(substr($child->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="seat-name">{{ $child->name }}</div>
                    <div class="seat-status">
                        @if($attStatus == 'checkin')
                            <span>✅</span>
                            <span style="color:#16a34a; font-weight:700;">Checked In</span>
                            <span style="color:#94a3b8; margin-left:auto; font-size:10px;">
                                {{ $att->checkin_time ? date('h:i A', strtotime($att->checkin_time)) : '' }}
                            </span>
                        @elseif($attStatus == 'checkout')
                            <span>📤</span>
                            <span style="color:#dc2626; font-weight:700;">Checked Out</span>
                            <span style="color:#94a3b8; margin-left:auto; font-size:10px;">
                                {{ $att->checkout_time ? date('h:i A', strtotime($att->checkout_time)) : '' }}
                            </span>
                        @else
                            <span>⏰</span>
                            <span style="color:#d97706; font-weight:700;">Belum Hadir</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-seat">
                    <span>👶</span>
                    <p>No children enrolled in this class yet</p>
                    <small>Add children to see seatmap</small>
                </div>
            @endforelse

            {{-- Empty slots --}}
            @php $emptySlots = $classroom->capacity - $children->count(); @endphp
            @for($i = 0; $i < $emptySlots; $i++)
                <div class="empty-seat">
                    <span>🪑</span>
                    <p>Empty Seat</p>
                    <small>Available for enrollment</small>
                </div>
            @endfor
        </div>
    </div>

</div>

{{-- Auto refresh every 30 seconds --}}
<script>
    setTimeout(() => window.location.reload(), 30000);
</script>

@endsection