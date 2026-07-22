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

    /* ============================================
       SEATMAP SECTION - IMPROVED
       ============================================ */
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
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* SEARCH & FILTER */
    .seatmap-controls {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 20px;
    }

    .seatmap-controls .search-box {
        flex: 1;
        min-width: 200px;
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 8px 16px;
        border: 2px solid transparent;
        transition: all .3s;
    }

    .seatmap-controls .search-box:focus-within {
        border-color: {{ $classroom->color ?? '#6d28d9' }};
        background: white;
        box-shadow: 0 0 0 4px rgba(109, 40, 217, 0.1);
    }

    .seatmap-controls .search-box input {
        border: none;
        background: transparent;
        padding: 8px 10px;
        font-size: 14px;
        width: 100%;
        outline: none;
        font-family: inherit;
    }

    .seatmap-controls .search-box .icon {
        color: #94a3b8;
        font-size: 18px;
    }

    .seatmap-controls .filter-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .seatmap-controls .filter-btn {
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        background: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .3s;
        color: #6b7280;
    }

    .seatmap-controls .filter-btn:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .seatmap-controls .filter-btn.active {
        border-color: {{ $classroom->color ?? '#6d28d9' }};
        background: {{ $classroom->color ?? '#6d28d9' }};
        color: white;
    }

    .seatmap-controls .filter-btn.active-all {
        border-color: #6d28d9;
        background: #6d28d9;
        color: white;
    }

    .seatmap-controls .filter-btn.active-checkin {
        border-color: #22c55e;
        background: #22c55e;
        color: white;
    }

    .seatmap-controls .filter-btn.active-checkout {
        border-color: #ef4444;
        background: #ef4444;
        color: white;
    }

    .seatmap-controls .filter-btn.active-absent {
        border-color: #f59e0b;
        background: #f59e0b;
        color: white;
    }

    /* LEGEND */
    .legend { 
        display: flex; 
        gap: 15px; 
        flex-wrap: wrap; 
        align-items: center;
    }
    .legend-item { 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        font-size: 12px; 
        font-weight: 500;
        color: #374151;
    }
    .legend-color { 
        width: 14px; 
        height: 14px; 
        border-radius: 4px; 
    }
    .legend-color.checkin  { background: #22c55e; }
    .legend-color.checkout { background: #ef4444; }
    .legend-color.absent   { background: #f59e0b; }

    /* SEAT GRID */
    .seatmap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .seat-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        transition: all .3s;
        cursor: pointer;
        border: 3px solid transparent;
        position: relative;
        text-align: center;
    }

    .seat-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 30px rgba(0,0,0,0.12); 
    }

    .seat-card.checkin, .seat-card.present, .seat-card.late {
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

    /* SEAT AVATAR - BIGGER */
    .seat-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 12px;
        background: linear-gradient(135deg, {{ $classroom->color ?? '#6d28d9' }}, {{ $classroom->color ?? '#9333ea' }});
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 28px;
        overflow: hidden;
        border: 3px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all .3s;
    }

    .seat-card:hover .seat-avatar {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    .seat-avatar img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }

    .seat-name { 
        font-weight: 700; 
        color: #1e293b; 
        font-size: 16px; 
        margin-bottom: 4px;
    }

    .seat-status {
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    .seat-status .time {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
    }

    .seat-status .reason {
        font-size: 10px;
        color: #d97706;
        width: 100%;
        background: #fef3c7;
        padding: 2px 8px;
        border-radius: 4px;
        margin-top: 2px;
    }

    .seat-card .badge-status {
        position: absolute;
        top: -6px;
        right: -6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }

    .badge-status.checkin { background: #22c55e; }
    .badge-status.checkout { background: #ef4444; }
    .badge-status.absent { background: #f59e0b; }
    .badge-status.late { background: #d97706; }

    .empty-seat {
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        text-align: center;
        padding: 30px;
        border-radius: 16px;
        color: #94a3b8;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 180px;
    }

    .empty-seat span { font-size: 32px; margin-bottom: 8px; }
    .empty-seat p { font-size: 14px; font-weight: 500; }
    .empty-seat small { font-size: 11px; color: #cbd5e1; }

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

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #94a3b8;
    }
    .no-results span { font-size: 48px; display: block; margin-bottom: 10px; }

    /* COUNTER */
    .result-counter {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }
    .result-counter strong { color: #1e293b; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .info-grid { grid-template-columns: 1fr; }
        .seatmap-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        .header-content { flex-direction: column; text-align: center; }
        .seatmap-controls { flex-direction: column; }
        .seatmap-controls .search-box { width: 100%; }
        .seat-avatar { width: 60px; height: 60px; font-size: 20px; }
        .seat-card { padding: 14px; }
    }
</style>

<div class="classroom-container">

    {{-- Header --}}
    <div class="classroom-header">
        <div class="header-content">
            <div class="classroom-info">
                <h1>
                    <span><i class="fas fa-school"></i></span> {{ $classroom->name }}
                </h1>
                <div class="code">
                    <span><i class="fas fa-id-badge"></i> Code: {{ $classroom->code }}</span>
                    <span>👥 Capacity: {{ $classroom->capacity }} children</span>
                    <span><i class="fas fa-chart-bar"></i> Enrollment: {{ $children->count() }}/{{ $classroom->capacity }}</span>
                    <div class="progress-bar" style="width: 150px;">
                        <div class="progress-fill" style="width: {{ $stats['capacity_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
            <div class="classroom-actions">
                <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit Class
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
            <div class="stat-icon"><i class="fas fa-child"></i></div>
            <div class="stat-number">{{ $stats['total_children'] }}</div>
            <div class="stat-label">Total Children</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-number" style="color:#16a34a;">{{ $stats['total_present'] }}</div>
            <div class="stat-label">Checked In</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-upload"></i></div>
            <div class="stat-number" style="color:#dc2626;">{{ $stats['total_checkout'] }}</div>
            <div class="stat-label">Checked Out</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"></div>
            <div class="stat-number" style="color:#d97706;">{{ $stats['total_absent'] }}</div>
            <div class="stat-label">Belum Hadir</div>
        </div>
    </div>

    {{-- Information Grid --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-title">
                <span><i class="fas fa-clipboard-list"></i></span> Class Details
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
                <div class="info-label"><span><i class="fas fa-chart-bar"></i></span> Age Range</div>
                <div class="info-value">{{ $classroom->min_age }} - {{ $classroom->max_age }} years</div>
            </div>
            <div class="info-row">
                <div class="info-label"><span><i class="fas fa-cog"></i></span> Status</div>
                <div class="info-value">
                    @if($classroom->status == 'active')
                        <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Active</span>
                    @else
                        <span style="color:#dc2626;"><i class="fas fa-times-circle"></i> Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card-title">
                <span></span> Schedule & Teacher
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
                <div class="info-label"><span>👩‍<i class="fas fa-school"></i></span> Class Teacher</div>
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
                <div class="info-label"><span><i class="fas fa-edit"></i></span> Description</div>
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
                <span class="result-counter">
                    (<strong id="visibleCount">{{ $children->count() }}</strong> / {{ $children->count() }} children)
                </span>
            </div>
            <div class="legend">
                <div class="legend-item"><div class="legend-color checkin"></div><span>Checked In</span></div>
                <div class="legend-item"><div class="legend-color checkout"></div><span>Checked Out</span></div>
                <div class="legend-item"><div class="legend-color absent"></div><span>Belum Hadir</span></div>
            </div>
        </div>

        {{-- SEARCH & FILTER --}}
        <div class="seatmap-controls">
            <div class="search-box">
                <span class="icon"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" placeholder="Cari nama anak..." onkeyup="filterSeats()">
                <span class="icon" id="clearSearch" style="cursor:pointer; display:none;" onclick="clearSearch()">✕</span>
            </div>
            <div class="filter-group">
                <button class="filter-btn active active-all" data-filter="all" onclick="setFilter('all', this)"><i class="fas fa-clipboard-list"></i> Semua</button>
                <button class="filter-btn" data-filter="checkin" onclick="setFilter('checkin', this)"><i class="fas fa-check-circle"></i> Checked In</button>
                <button class="filter-btn" data-filter="checkout" onclick="setFilter('checkout', this)"><i class="fas fa-upload"></i> Checked Out</button>
                <button class="filter-btn" data-filter="absent" onclick="setFilter('absent', this)"> Belum Hadir</button>
            </div>
        </div>

        <div class="seatmap-grid" id="seatGrid">
            @forelse($children as $child)
                @php
                    // 🔥🔥🔥 CHECK ATTENDANCE STATUS - TERMASUK CHECKOUT 🔥🔥🔥
                    $att = $attendances->get($child->id);
                    $attStatus = 'absent';
                    $displayStatus = 'absent';
                    $badgeLabel = 'Absent';
                    $badgeClass = 'absent';
                    $checkinTime = null;
                    $checkoutTime = null;
                    $lateReason = null;
                    $isCheckedInToday = false;
                    $isCheckedOutToday = false;
                    
                    if ($att) {
                        // 🔥 TENTUKAN STATUS - CHECKOUT DIUTAMAKAN
                        if ($att->checkout_time) {
                            $attStatus = 'checkout';
                            $displayStatus = 'checkout';
                            $badgeLabel = 'Checked Out';
                            $badgeClass = 'checkout';
                            $checkoutTime = $att->checkout_time;
                            $checkinTime = $att->checkin_time;
                            $isCheckedOutToday = true;
                            $isCheckedInToday = true;
                        } elseif ($att->checkin_time) {
                            $attStatus = in_array($att->status, ['late']) ? 'late' : 'checkin';
                            $displayStatus = 'checkin';
                            $badgeLabel = $attStatus == 'late' ? ' Late' : '<i class="fas fa-check-circle"></i> Checked In';
                            $badgeClass = $attStatus == 'late' ? 'late' : 'checkin';
                            $checkinTime = $att->checkin_time;
                            $lateReason = $att->late_reason ?? null;
                            $isCheckedInToday = true;
                        } else {
                            $attStatus = 'absent';
                            $displayStatus = 'absent';
                            $badgeLabel = ' Absent';
                            $badgeClass = 'absent';
                        }
                    }
                @endphp
                <div class="seat-card {{ $displayStatus }}" 
                     data-name="{{ strtolower($child->name) }}"
                     data-status="{{ $attStatus }}"
                     onclick="window.location='{{ route('children.show', hash_id($child->id)) }}'">
                    
                    <span class="badge-status {{ $badgeClass }}">{{ $badgeLabel }}</span>
                    
                    <div class="seat-avatar">
                        @if($child->photo)
                            <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                        @else
                            {{ strtoupper(substr($child->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="seat-name">{{ $child->name }}</div>
                    <div class="seat-status">
                        @if($attStatus == 'checkout')
                            <span><i class="fas fa-upload"></i></span>
                            <span style="color:#dc2626; font-weight:600;">Checked Out</span>
                            <span class="time">
                                🕐 {{ $checkoutTime ? date('h:i A', strtotime($checkoutTime)) : '' }}
                            </span>
                            @if($checkinTime)
                                <span class="time" style="color:#6b7280; width:100%;">
                                    <i class="fas fa-download"></i> In: {{ date('h:i A', strtotime($checkinTime)) }}
                                </span>
                            @endif
                        @elseif(in_array($attStatus, ['checkin', 'late', 'present']))
                            <i class="fas fa-check-circle"></i>
                            <span style="color:#16a34a; font-weight:600;">
                                @if($attStatus == 'late')
                                     Late Check-in
                                @else
                                    Checked In
                                @endif
                            </span>
                            <span class="time">
                                🕐 {{ $checkinTime ? date('h:i A', strtotime($checkinTime)) : '' }}
                            </span>
                            @if($lateReason)
                                <span class="reason"><i class="fas fa-edit"></i> {{ $lateReason }}</span>
                            @endif
                        @else
                            <span></span>
                            <span style="color:#d97706; font-weight:600;">Belum Hadir</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-seat">
                    <span><i class="fas fa-child"></i></span>
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

<script>
    let currentFilter = 'all';
    let searchTerm = '';

    function filterSeats() {
        const input = document.getElementById('searchInput');
        const searchTerm = input.value.toLowerCase().trim();
        const seats = document.querySelectorAll('.seat-card:not(.empty-seat)');
        const emptySeats = document.querySelectorAll('.empty-seat');
        let visibleCount = 0;

        seats.forEach(seat => {
            const name = seat.getAttribute('data-name') || '';
            const status = seat.getAttribute('data-status') || 'absent';
            
            // Search match
            const searchMatch = name.includes(searchTerm);
            
            // Filter match
            let filterMatch = true;
            if (currentFilter === 'checkin') {
                filterMatch = ['present', 'checkin', 'late'].includes(status);
            } else if (currentFilter === 'checkout') {
                filterMatch = status === 'checkout';  // 🔥 TERMASUK CHECKOUT
            } else if (currentFilter === 'absent') {
                filterMatch = status === 'absent';
            }
            
            if (searchMatch && filterMatch) {
                seat.style.display = '';
                visibleCount++;
            } else {
                seat.style.display = 'none';
            }
        });

        // Hide empty seats if searching
        emptySeats.forEach(seat => {
            if (searchTerm || currentFilter !== 'all') {
                seat.style.display = 'none';
            } else {
                seat.style.display = '';
            }
        });

        // Update counter
        document.getElementById('visibleCount').textContent = visibleCount;
        
        // Show clear button
        document.getElementById('clearSearch').style.display = searchTerm ? 'inline' : 'none';
    }

    function setFilter(filter, btn) {
        currentFilter = filter;
        
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'active-all', 'active-checkin', 'active-checkout', 'active-absent');
        });
        
        if (btn) {
            btn.classList.add('active');
            if (filter === 'all') btn.classList.add('active-all');
            else if (filter === 'checkin') btn.classList.add('active-checkin');
            else if (filter === 'checkout') btn.classList.add('active-checkout');
            else if (filter === 'absent') btn.classList.add('active-absent');
        }
        
        filterSeats();
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('clearSearch').style.display = 'none';
        filterSeats();
    }

    // Auto refresh every 30 seconds
    setTimeout(() => window.location.reload(), 30000);
</script>

@endsection