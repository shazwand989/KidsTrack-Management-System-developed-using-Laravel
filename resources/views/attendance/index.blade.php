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

    .pg-header-right { display: flex; gap: 10px; }

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

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border-left: 5px solid #FF6B6B;
        transition: transform .2s;
    }

    .stat-card:hover { transform: translateY(-3px); }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-icon.pink { background: #FFF5F2; color: #FF6B6B; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-icon.red { background: #fef2f2; color: #dc2626; }
    .stat-icon.blue { background: #eff6ff; color: #3b82f6; }

    .stat-num { font-size: 26px; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 3px; }
    .stat-label { font-size: 12px; color: #94a3b8; font-weight: 600; }

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

    .search-wrap span {
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
        overflow-x: auto;
        border: 1px solid #FFF0EC;
    }

    .pg-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .pg-table thead tr { background: #FFF5F2; }

    .pg-table thead th {
        padding: 14px 18px;
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
    .pg-table tbody td { padding: 14px 18px; font-size: 13px; color: #475569; border: none; vertical-align: middle; }

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
    .status-badge.absent { background: #fef2f2; color: #dc2626; }

    .action-btns { display: flex; gap: 6px; }

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

    .act-btn.edit   { background: #fffbeb; color: #d97706; }
    .act-btn.delete { background: #fef2f2; color: #dc2626; }
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

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
</style>

{{-- Alert Success --}}
@if(session('success'))
<div class="alert-success">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="pg-header">
    <div class="pg-header-left">
        <h2><span>📋</span> Attendance Records</h2>
        <p>Manage daily attendance for children</p>
    </div>
    <div class="pg-header-right">
        <a href="#" class="btn-export">
            <span>⬇️</span> Export CSV
        </a>
        <a href="{{ route('attendance.create') }}" class="btn-take">
            <span>📝</span> Take Attendance
        </a>
    </div>
</div>

{{-- Stats Cards --}}
@php
    $totalPresent = $attendances->where('status', 'checkin')->count();
    $totalCheckout = $attendances->where('status', 'checkout')->count();
    $totalAbsent = $attendances->where('status', 'absent')->count();
    $totalRecords = $attendances->total() ?? $attendances->count();
@endphp

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon pink"><span>📋</span></div>
        <div>
            <div class="stat-num">{{ $totalRecords }}</div>
            <div class="stat-label">Total Records</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><span>✅</span></div>
        <div>
            <div class="stat-num">{{ $totalPresent }}</div>
            <div class="stat-label">Check-ins</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><span>📤</span></div>
        <div>
            <div class="stat-num">{{ $totalCheckout }}</div>
            <div class="stat-label">Check-outs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><span>❌</span></div>
        <div>
            <div class="stat-num">{{ $totalAbsent }}</div>
            <div class="stat-label">Absent</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="search-wrap">
        <span>🔍</span>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search by child name...">
    </div>
    <select class="filter-select" id="filterStatus">
        <option value="">All Status</option>
        <option value="checkin">✅ Check-in</option>
        <option value="checkout">📤 Check-out</option>
        <option value="absent">❌ Absent</option>
    </select>
    <input type="date" class="date-input" id="filterDate" placeholder="Filter by date">
    <span class="record-count" id="recordCount">{{ $totalRecords }} records</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="pg-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Child</th>
                <th>Date</th>
                <th>Status</th>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
                <th>Drop Off By</th>
                <th>Pickup By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($attendances as $i => $attendance)
            <tr>
                <td style="color:#94a3b8; font-weight:700;">{{ $attendances->firstItem() + $i }}</td>
                
                <td>
                    <div class="child-cell">
                        <div class="child-avatar">
                            @if($attendance->child && $attendance->child->photo)
                                <img src="{{ asset('storage/'.$attendance->child->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($attendance->child->name ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="child-name">{{ $attendance->child->name ?? 'Child not found' }}</p>
                            <p class="child-sub">
                                @if($attendance->child && $attendance->child->classroom)
                                    🏫 {{ $attendance->child->classroom->name }}
                                @else
                                    No class
                                @endif
                            </p>
                        </div>
                    </div>
                </td>
                
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                
                <td>
                    @if($attendance->status == 'checkin')
                        <span class="status-badge checkin">✅ Check-in</span>
                    @elseif($attendance->status == 'checkout')
                        <span class="status-badge checkout">📤 Check-out</span>
                    @else
                        <span class="status-badge absent">❌ Absent</span>
                    @endif
                </td>
                
                <td>{{ $attendance->checkin_time ? \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->checkout_time ? \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->drop_off_by ?? '-' }}</td>
                <td>{{ $attendance->pickup_by ?? '-' }}</td>
                
                <td>
                    <div class="action-btns">
                        <a href="{{ route('attendance.edit', $attendance->id) }}" class="act-btn edit" title="Edit">
                            <span>✏️</span>
                        </a>
                        <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="act-btn delete" title="Delete"
                                onclick="return confirm('Delete this attendance record?')">
                                <span>🗑️</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
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
        <span>{{ $totalRecords }} total records</span>
    </div>
    @endif
</div>

{{-- Pagination --}}
@if(method_exists($attendances, 'links'))
    <div class="pagination">
        {{ $attendances->links() }}
    </div>
@endif

<script>
    // Search and Filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);
    document.getElementById('filterDate').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('filterStatus').value.toLowerCase();
        const date = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('#tableBody tr');
        let visible = 0;

        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const text = row.innerText.toLowerCase();
            const rowDate = row.cells[2]?.innerText || '';
            const rowDateFormatted = rowDate;
            
            let matchSearch = search === '' || text.includes(search);
            let matchStatus = status === '' || text.includes(status);
            let matchDate = date === '' || rowDateFormatted.includes(date);
            
            if (matchSearch && matchStatus && matchDate) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('recordCount').textContent = visible + ' records';
        
        // Show empty message if no results
        const tbody = document.getElementById('tableBody');
        const existingEmpty = tbody.querySelector('.empty-row-message');
        
        if (visible === 0 && !existingEmpty && rows.length > 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.className = 'empty-row-message';
            emptyRow.innerHTML = `
                <td colspan="9">
                    <div class="empty-state" style="padding: 40px;">
                        <div class="empty-icon">🔍</div>
                        <h5>No matching records found</h5>
                        <p>Try adjusting your search or filter criteria</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        } else if (visible > 0 && existingEmpty) {
            existingEmpty.remove();
        }
    }

    // Row click to view details
    document.querySelectorAll('#tableBody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-btns')) return;
            if (this.querySelector('.empty-state')) return;
            // Add view detail functionality if needed
        });
    });
</script>

@endsection