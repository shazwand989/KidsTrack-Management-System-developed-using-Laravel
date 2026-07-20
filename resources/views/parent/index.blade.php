@extends('layouts.template')

@section('title', 'Loving Guardians')
@section('page-title', 'Loving Guardians')

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
        min-width: 750px;
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

    .parent-cell { display: flex; align-items: center; gap: 12px; }

    .parent-avatar {
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

    .parent-avatar img { width:100%; height:100%; object-fit:cover; }

    .parent-name { font-weight: 800; color: #1e293b; font-size: 14px; margin: 0 0 2px; }
    .parent-sub { font-size: 11px; color: #94a3b8; margin: 0; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.verified { background: #f0fdf4; color: #16a34a; }
    .status-badge.pending { background: #fffbeb; color: #d97706; }
    .status-badge.emergency { background: #fef2f2; color: #dc2626; }

    .relation-badge {
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

    .relation-badge.main { background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; }
    .relation-badge.second { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .relation-badge.guardian { background: #fef3c7; color: #d97706; border-color: #fde68a; }

    .action-btns { display: flex; gap: 6px; }

    .act-btn {
        width: 32px; height: 32px;
        border-radius: 9px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
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

    /* Pagination */
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
        <h2><span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">family_restroom</i></span> Loving Guardians</h2>
        <p>View, update, and manage parent and guardian records.</p>
    </div>
    <div class="pg-header-right">
        <a href="#" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('parents.create') }}" class="btn-register">
            <i class="fas fa-plus"></i> Register Parent
        </a>
    </div>
</div>

{{-- Stat Cards --}}
@php
    $totalFamilies = $families->count();
    $totalChildren = $families->sum('childCount');
    $verified = $families->filter(fn($f) => $f['main']->verified)->count();
    $guardianCount = $families->filter(fn($f) => $f['guardian'])->count();
@endphp

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-num">{{ $totalFamilies }}</div>
            <div class="stat-label">Families</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-num">{{ $verified }}</div>
            <div class="stat-label">Verified</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-child"></i></div>
        <div>
            <div class="stat-num">{{ $totalChildren }}</div>
            <div class="stat-label">Children</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="stat-num">{{ $guardianCount }}</div>
            <div class="stat-label">With Guardian</div>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search family name, phone, email...">
    </div>
    <span class="record-count">{{ $totalFamilies }} families</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="pg-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Main Parent</th>
                <th>Second Parent</th>
                <th>Guardian</th>
                <th>👶 Children</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($families as $i => $family)
            @php
                $main = $family['main'];
                $second = $family['second'];
                $guardian = $family['guardian'];
                $children = $family['children'];
            @endphp
            <tr data-search="{{ strtolower($main->name) }} {{ strtolower($main->email ?? '') }} {{ strtolower($main->phone_number ?? '') }} {{ $second ? strtolower($second->name) : '' }} {{ $guardian ? strtolower($guardian->name) : '' }}">
                <td style="color:#94a3b8;font-weight:700;">{{ $i + 1 }}</td>

                {{-- Main Parent --}}
                <td>
                    <div class="parent-cell">
                        <div class="parent-avatar" style="width:40px;height:40px;border-radius:10px;font-size:14px;">
                            @if($main->photo)
                                <img src="{{ Storage::url($main->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($main->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="parent-name" style="font-size:13px;">{{ $main->name }}</p>
                            <p class="parent-sub">📞 {{ $main->phone_number ?? '-' }}</p>
                            @if($main->verified)
                                <span class="status-badge verified" style="font-size:9px;padding:1px 6px;">✓</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Second Parent --}}
                <td>
                    @if($second)
                    <div class="parent-cell">
                        <div class="parent-avatar" style="width:34px;height:34px;border-radius:8px;font-size:12px;background:linear-gradient(135deg,#3b82f6,#60a5fa);">
                            {{ strtoupper(substr($second->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="parent-name" style="font-size:12px;">{{ $second->name }}</p>
                            <p class="parent-sub">📞 {{ $second->phone_number ?? '-' }}</p>
                        </div>
                    </div>
                    @else
                        <span style="color:#cbd5e1;font-size:12px;">—</span>
                    @endif
                </td>

                {{-- Guardian --}}
                <td>
                    @if($guardian)
                    <div class="parent-cell">
                        <div class="parent-avatar" style="width:34px;height:34px;border-radius:8px;font-size:12px;background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                            {{ strtoupper(substr($guardian->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="parent-name" style="font-size:12px;">{{ $guardian->name }}</p>
                            <p class="parent-sub">📞 {{ $guardian->phone_number ?? '-' }}</p>
                        </div>
                    </div>
                    @else
                        <span style="color:#cbd5e1;font-size:12px;">—</span>
                    @endif
                </td>

                {{-- Children --}}
                <td>
                    @if($children->count())
                        @foreach($children as $child)
                            <span class="child-tag">{{ $child->name }}</span>
                        @endforeach
                    @else
                        <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td>
                    <div class="action-btns">
                        <a href="{{ route('parents.show', $main->id) }}" class="act-btn view" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('parents.edit', $main->id) }}" class="act-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:60px 20px;">
                    <div style="font-size:48px;margin-bottom:12px;">👨‍👩‍👧‍👦</div>
                    <h5 style="color:#1e293b;font-weight:800;">No families registered yet</h5>
                    <p style="color:#94a3b8;">Register a parent to get started.</p>
                    <a href="{{ route('parents.create') }}" class="btn-register" style="display:inline-flex;margin-top:8px;">
                        <i class="fas fa-plus"></i> Register Parent
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($totalFamilies > 0)
    <div class="table-footer">
        <span>{{ $totalFamilies }} families</span>
    </div>
    @endif
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#tableBody tr').forEach(row => {
            if (row.querySelector('td[colspan]')) return;
            row.style.display = search === '' || (row.dataset.search || '').includes(search) ? '' : 'none';
        });
    });
</script>

<style>
    .child-tag {
        display: inline-block;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        margin: 1px;
    }
</style>

@endsection
