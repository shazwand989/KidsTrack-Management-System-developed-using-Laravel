@extends('layouts.template')

@section('title', 'Teachers List')
@section('page-title', 'Teachers')

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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-icon.pink { background: #FFF5F2; color: #FF6B6B; }
    .stat-icon.blue { background: #eff6ff; color: #3b82f6; }
    .stat-icon.green { background: #f0fdf4; color: #16a34a; }
    .stat-icon.orange { background: #fffbeb; color: #f59e0b; }

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

    .teacher-cell { display: flex; align-items: center; gap: 12px; }

    .teacher-avatar {
        width: 45px; height: 45px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        flex-shrink: 0;
        overflow: hidden;
    }

    .teacher-avatar img { width:100%; height:100%; object-fit:cover; }

    .teacher-name { font-weight: 800; color: #1e293b; font-size: 14px; margin: 0 0 2px; }
    .teacher-sub { font-size: 11px; color: #94a3b8; margin: 0; }

    .position-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .position-badge.head { background: #fef2f2; color: #dc2626; }
    .position-badge.senior { background: #fff1f2; color: #f43f5e; }
    .position-badge.class { background: #eff6ff; color: #3b82f6; }
    .position-badge.assistant { background: #f0fdf4; color: #16a34a; }
    .position-badge.nursery { background: #fffbeb; color: #d97706; }
    .position-badge.default { background: #FFF5F2; color: #FF6B6B; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.active { background: #f0fdf4; color: #16a34a; }
    .status-badge.inactive { background: #fef2f2; color: #dc2626; }
    .status-badge.on-leave { background: #fffbeb; color: #d97706; }

    .classroom-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

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

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); }
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
        <h2><span>👩‍<i class="fas fa-school"></i></span> Our Teachers</h2>
        <p>Dedicated educators shaping young minds</p>
    </div>
    <div class="pg-header-right">
        <a href="#" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('teachers.create') }}" class="btn-register">
            <i class="fas fa-plus"></i> Register Teacher
        </a>
    </div>
</div>

{{-- Stat Cards --}}
@php
    $total = $teachers->count();
    $active = $teachers->where('status', 'active')->count();
    $onLeave = $teachers->where('status', 'on_leave')->count();
    $classroomsCount = $teachers->whereNotNull('classroom_id')->unique('classroom_id')->count();
@endphp

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon pink"><span>👩‍<i class="fas fa-school"></i></span></div>
        <div>
            <div class="stat-num">{{ $total }}</div>
            <div class="stat-label">Total Teachers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-num">{{ $active }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><span>⏳</span></div>
        <div>
            <div class="stat-num">{{ $onLeave }}</div>
            <div class="stat-label">On Leave</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><span><i class="fas fa-school"></i></span></div>
        <div>
            <div class="stat-num">{{ $classroomsCount }}</div>
            <div class="stat-label">Classrooms</div>
        </div>
    </div>
</div>

{{-- Search + Filter --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search name, position, phone...">
    </div>
    <select class="filter-select" id="filterPosition">
        <option value="">All Positions</option>
        <option value="Head Teacher">👩‍<i class="fas fa-school"></i> Head Teacher</option>
        <option value="Senior Teacher"><i class="fas fa-star"></i> Senior Teacher</option>
        <option value="Class Teacher">📚 Class Teacher</option>
        <option value="Assistant Teacher">📖 Assistant Teacher</option>
        <option value="Nursery Teacher">🍼 Nursery Teacher</option>
        <option value="Kindergarten Teacher">🎨 Kindergarten Teacher</option>
        <option value="Trainee Teacher">📝 Trainee Teacher</option>
    </select>
    <select class="filter-select" id="filterStatus">
        <option value="">All Status</option>
        <option value="active"><i class="fas fa-check-circle"></i> Active</option>
        <option value="inactive"><i class="fas fa-times-circle"></i> Inactive</option>
        <option value="on_leave">⏳ On Leave</option>
    </select>
    <span class="record-count" id="recordCount">{{ $total }} records</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="pg-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Teacher</th>
                <th>Position</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Classroom</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($teachers as $i => $teacher)
            @php
                $positionClass = 'default';
                if (str_contains(strtolower($teacher->position), 'head')) $positionClass = 'head';
                elseif (str_contains(strtolower($teacher->position), 'senior')) $positionClass = 'senior';
                elseif (str_contains(strtolower($teacher->position), 'class')) $positionClass = 'class';
                elseif (str_contains(strtolower($teacher->position), 'assistant')) $positionClass = 'assistant';
                elseif (str_contains(strtolower($teacher->position), 'nursery')) $positionClass = 'nursery';
            @endphp
            <tr>
                <td style="color:#94a3b8; font-weight:700;">{{ $i + 1 }}</td>
                
                {{-- Teacher Info --}}
                <td>
                    <div class="teacher-cell">
                        <div class="teacher-avatar">
                            @if($teacher->photo)
                                <img src="{{ asset('storage/'.$teacher->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="teacher-name">{{ $teacher->name }}</p>
                            <p class="teacher-sub">
                                @if($teacher->email)
                                    ✉️ {{ $teacher->email }}
                                @else
                                    No email
                                @endif
                            </p>
                        </div>
                    </div>
                </td>
                
                {{-- Position --}}
                <td>
                    <span class="position-badge {{ $positionClass }}">
                        @if($positionClass == 'head') 👩‍<i class="fas fa-school"></i>
                        @elseif($positionClass == 'senior') <i class="fas fa-star"></i>
                        @elseif($positionClass == 'class') 📚
                        @elseif($positionClass == 'assistant') 📖
                        @elseif($positionClass == 'nursery') 🍼
                        @else 👩‍<i class="fas fa-school"></i>
                        @endif
                        {{ $teacher->position }}
                    </span>
                </td>
                
                {{-- Age --}}
                <td>{{ $teacher->age }} yrs</td>
                
                {{-- Phone --}}
                <td>{{ $teacher->phone ?? '-' }}</td>
                
                {{-- Classroom (dari relationship) --}}
                <td>
                    @if($teacher->classroom)
                        <span class="classroom-badge">
                            <i class="fas fa-school"></i> {{ $teacher->classroom->name }}
                            <span style="font-size:10px; color:#94a3b8;">({{ $teacher->classroom->code }})</span>
                        </span>
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                
                {{-- Status --}}
                <td>
                    <span class="status-badge {{ $teacher->status_color }}">
                        @if($teacher->status == 'active') <i class="fas fa-check-circle"></i> Active
                        @elseif($teacher->status == 'inactive') <i class="fas fa-times-circle"></i> Inactive
                        @else ⏳ On Leave
                        @endif
                    </span>
                </td>
                
                {{-- Actions --}}
                <td>
                    <div class="action-btns">
                        <a href="{{ route('teachers.show', $teacher->id) }}"
                            class="act-btn view" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('teachers.edit', $teacher->id) }}"
                            class="act-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('teachers.destroy', $teacher->id) }}"
                            method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="act-btn delete" title="Delete"
                                onclick="return confirm('Delete {{ addslashes($teacher->name) }}? This action cannot be undone.')">
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
                        <div class="empty-icon">👩‍<i class="fas fa-school"></i></div>
                        <h5>No teachers registered yet</h5>
                        <p>Start by registering your first teacher to the nursery.</p>
                        <a href="{{ route('teachers.create') }}"><i class="fas fa-plus"></i> Register New Teacher</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($teachers->count() > 0)
    <div class="table-footer">
        <span>ℹ️</span>
        <span>Click any row to view full profile</span>
        <span>{{ $total }} total teachers</span>
    </div>
    @endif
</div>

<script>
    // Search and Filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('filterPosition').addEventListener('change', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);

    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const position = document.getElementById('filterPosition').value.toLowerCase();
        const status = document.getElementById('filterStatus').value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');
        let visible = 0;

        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const text = row.innerText.toLowerCase();
            const matchSearch = search === '' || text.includes(search);
            const matchPosition = position === '' || text.includes(position);
            const matchStatus = status === '' || text.includes(status);
            
            if (matchSearch && matchPosition && matchStatus) {
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
                <td colspan="8">
                    <div class="empty-state" style="padding: 40px;">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
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

    // Row click to view profile
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