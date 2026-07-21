@extends('layouts.template')

@section('title', 'Classrooms List')
@section('page-title', 'Classrooms')

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

    .btn-register {
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
        cursor: pointer;
    }

    .btn-register:hover { opacity: .9; transform: translateY(-1px); }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border-left: 4px solid #FF6B6B;
        transition: transform .2s;
    }

    .stat-card:hover { transform: translateY(-3px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-icon.pink { background: #FFF5F2; color: #FF6B6B; }
    .stat-icon.blue { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-icon.orange { background: #fffbeb; color: #f59e0b; }

    .stat-info { flex: 1; }
    .stat-number { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .stat-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }

    /* Filter Bar */
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

    .record-count { font-size: 13px; font-weight: 700; color: #94a3b8; white-space: nowrap; }

    /* Table */
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
        min-width: 800px;
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

    .classroom-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .classroom-color {
        width: 12px;
        height: 40px;
        border-radius: 6px;
    }

    .classroom-info {
        flex: 1;
    }

    .classroom-name {
        font-weight: 800;
        color: #1e293b;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .classroom-code {
        font-size: 11px;
        color: #94a3b8;
    }

    .teacher-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .teacher-avatar {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
        overflow: hidden;
    }

    .teacher-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .teacher-name { font-size: 13px; font-weight: 600; color: #475569; }

    .stats-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-bar {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bar {
        width: 80px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width .3s;
    }

    .stat-number-small {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.active { background: #f0fdf4; color: #16a34a; }
    .status-badge.inactive { background: #fef2f2; color: #dc2626; }

    .action-btns { display: flex; gap: 6px; }

    .act-btn {
        width: 32px;
        height: 32px;
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

    .act-btn.view   { background: #FFF5F2; color: #FF6B6B; }
    .act-btn.edit   { background: #fffbeb; color: #d97706; }
    .act-btn.delete { background: #fef2f2; color: #dc2626; }
    .act-btn.view:hover   { background: #FFE4D6; }
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

    @media (max-width: 1024px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .record-count { text-align: center; }
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
        <h2><span>🏫</span> Classrooms</h2>
        <p>Manage nursery classes, view statistics and seatmaps</p>
    </div>
    <div class="pg-header-right">
        <a href="#" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('classrooms.create') }}" class="btn-register">
            <i class="fas fa-plus"></i> New Classroom
        </a>
    </div>
</div>

{{-- Statistics Dashboard --}}
@php
    $totalClassrooms = $classrooms->count();
    $totalChildren = $classrooms->sum('total_children');
@endphp

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon pink"><span>🏫</span></div>
        <div class="stat-info">
            <div class="stat-number">{{ $totalClassrooms }}</div>
            <div class="stat-label">Total Classrooms</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><span>👶</span></div>
        <div class="stat-info">
            <div class="stat-number">{{ $totalChildren }}</div>
            <div class="stat-label">Total Children</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><span>🏫</span></div>
        <div class="stat-info">
            <div class="stat-number">{{ $classrooms->where('status', 'active')->count() }}</div>
            <div class="stat-label">Active Classrooms</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><span>📊</span></div>
        <div class="stat-info">
            <div class="stat-number">{{ $totalClassrooms > 0 ? round($totalChildren / $totalClassrooms) : 0 }}</div>
            <div class="stat-label">Avg per Class</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search by name, code, teacher...">
    </div>
    <select class="filter-select" id="filterStatus">
        <option value="">All Status</option>
        <option value="active">✅ Active</option>
        <option value="inactive">❌ Inactive</option>
    </select>
    <span class="record-count" id="recordCount">{{ $classrooms->count() }} classrooms</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="pg-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Classroom</th>
                <th>Teacher</th>
                <th>Age Group</th>
                <th>⏰ Schedule</th>
                <th>Students</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($classrooms as $i => $classroom)
            @php
                $percentage = $classroom->total_children > 0 ? round(($classroom->total_children / $classroom->capacity) * 100) : 0;
            @endphp
            <tr>
                <td style="color:#94a3b8; font-weight:700;">{{ $i + 1 }}</td>
                
                {{-- Classroom Info --}}
                <td>
                    <div class="classroom-name-cell">
                        <div class="classroom-color" style="background-color: {{ $classroom->color ?? '#FF6B6B' }};"></div>
                        <div class="classroom-info">
                            <div class="classroom-name">{{ $classroom->name }}</div>
                            <div class="classroom-code">{{ $classroom->code }}</div>
                        </div>
                    </div>
                </td>
                
                {{-- Teacher --}}
                <td>
                    @if($classroom->teacher)
                    <div class="teacher-cell">
                        <div class="teacher-avatar">
                            @if($classroom->teacher->photo)
                                <img src="{{ asset('storage/'.$classroom->teacher->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($classroom->teacher->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="teacher-name">{{ $classroom->teacher->name }}</div>
                    </div>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                
                {{-- Age Group --}}
                <td>
                    <div><strong>{{ $classroom->age_group }}</strong></div>
                    <div style="font-size: 11px; color: #94a3b8;">{{ $classroom->min_age }} - {{ $classroom->max_age }} yrs</div>
                </td>

                {{-- Schedule --}}
                <td>
                    @if($classroom->start_time && $classroom->end_time)
                        <span style="font-weight:700;color:#1e293b;font-family:monospace;font-size:13px;">
                            {{ substr($classroom->start_time, 0, 5) }} – {{ substr($classroom->end_time, 0, 5) }}
                        </span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>

                {{-- Students --}}
                <td>
                    <div class="stats-cell">
                        <div class="stat-number-small">{{ $classroom->total_children }} enrolled</div>
                    </div>
                </td>
                
                {{-- Capacity Bar --}}
                <td>
                    <div class="stats-cell">
                        <div class="stat-number-small">{{ $classroom->capacity }} capacity</div>
                        <div class="stat-bar">
                            <div class="bar">
                                <div class="bar-fill" style="width: {{ $percentage }}%; background-color: {{ $classroom->color ?? '#FF6B6B' }};"></div>
                            </div>
                            <span style="font-size: 10px; color: #94a3b8;">{{ $percentage }}%</span>
                        </div>
                    </div>
                </td>
                
                {{-- Status --}}
                <td>
                    <span class="status-badge {{ $classroom->status }}">
                        @if($classroom->status == 'active') ✅ Active
                        @else ❌ Inactive
                        @endif
                    </span>
                </td>
                
                {{-- Actions --}}
                <td>
                    <div class="action-btns">
                        <a href="{{ route('classrooms.show', $classroom->id) }}" class="act-btn view" title="View Seatmap">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('classrooms.edit', $classroom->id) }}" class="act-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" style="margin:0;" 
                            onsubmit="return confirm('Delete {{ addslashes($classroom->name) }}? This will affect all children in this class.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="act-btn delete" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <div class="empty-icon">🏫</div>
                        <h5>No classrooms created yet</h5>
                        <p>Start by creating your first classroom.</p>
                        <a href="{{ route('classrooms.create') }}">➕ Create New Classroom</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($classrooms->count() > 0)
    <div class="table-footer">
        <span>ℹ️</span>
        <span>Click any row to view seatmap and details</span>
        <span>{{ $classrooms->count() }} total classrooms</span>
    </div>
    @endif
</div>

<script>
    // Search and Filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('filterStatus').value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');
        let visible = 0;

        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const text = row.innerText.toLowerCase();
            const matchSearch = search === '' || text.includes(search);
            
            let matchStatus = true;
            if (status !== '') {
                const statusCell = row.querySelector('.status-badge');
                if (statusCell) {
                    matchStatus = statusCell.innerText.toLowerCase().includes(status);
                } else {
                    matchStatus = false;
                }
            }
            
            if (matchSearch && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('recordCount').textContent = visible + ' classrooms';
        
        // Show empty message if no results
        const tbody = document.getElementById('tableBody');
        const existingEmpty = tbody.querySelector('.empty-row-message');
        
        if (visible === 0 && !existingEmpty && rows.length > 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.className = 'empty-row-message';
            emptyRow.innerHTML = `
                <td colspan="8">
                    <div class="empty-state" style="padding: 40px;">
                        <div class="empty-icon">🔍</div>
                        <h5>No matching classrooms found</h5>
                        <p>Try adjusting your search or filter criteria</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        } else if (visible > 0 && existingEmpty) {
            existingEmpty.remove();
        }
    }

    // Row click to view seatmap
    document.querySelectorAll('#tableBody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-btns')) return;
            if (this.querySelector('.empty-state')) return;
            const viewBtn = this.querySelector('.act-btn.view');
            if (viewBtn) viewBtn.click();
        });
    });
</script>

@endsection