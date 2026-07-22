@extends('layouts.template')

@section('title', 'Parents List')
@section('page-title', 'Parents')

@section('content')

<style>
    .pg-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
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

    .pg-header-left h2 i { color: #FF6B6B; }
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

    .btn-register:hover { opacity: .9; color: white; transform: translateY(-1px); }

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
    .stat-card.pink  { border-left-color: #FF6B6B; }
    .stat-card.rose  { border-left-color: #f43f5e; }
    .stat-card.blue  { border-left-color: #3b82f6; }
    .stat-card.amber { border-left-color: #f59e0b; }

    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-icon.pink  { background: #FFF5F2; color: #FF6B6B; }
    .stat-icon.rose  { background: #fff1f2; color: #f43f5e; }
    .stat-icon.blue  { background: #eff6ff; color: #3b82f6; }
    .stat-icon.amber { background: #fffbeb; color: #f59e0b; }

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
    }

    .search-wrap { flex: 1; position: relative; }

    .search-wrap i {
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
        transition: .2s;
        font-family: 'Inter', sans-serif;
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
        font-family: 'Inter', sans-serif;
        cursor: pointer;
        min-width: 150px;
    }

    .filter-select:focus { border-color: #FF9E7D; }
    .record-count { font-size: 13px; font-weight: 700; color: #94a3b8; white-space: nowrap; }

    .table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #FFF0EC;
    }

    .pg-table { width: 100%; border-collapse: collapse; }
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

    .guardian-cell { display: flex; align-items: center; gap: 12px; }

    .guardian-avatar {
        width: 40px; height: 40px;
        border-radius: 11px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
        font-size: 16px;
        font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        text-transform: uppercase;
        flex-shrink: 0;
        overflow: hidden;
    }

    .guardian-avatar img { width:100%; height:100%; object-fit:cover; }

    .guardian-name { font-weight: 700; color: #1e293b; font-size: 13px; margin: 0 0 2px; }
    .guardian-sub { font-size: 11px; color: #94a3b8; margin: 0; }

    .relation-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .relation-badge.mother   { background: #fff1f2; color: #f43f5e; }
    .relation-badge.father   { background: #eff6ff; color: #3b82f6; }
    .relation-badge.guardian { background: #f0fdf4; color: #16a34a; }
    .relation-badge.other    { background: #FFF5F2; color: #FF6B6B; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.verified   { background: #f0fdf4; color: #16a34a; }
    .status-badge.unverified { background: #FFF5F2; color: #FF6B6B; }
    .status-badge.emergency  { background: #fffbeb; color: #d97706; }

    .qr-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .action-btns { display: flex; gap: 6px; }

    .act-btn {
        width: 32px; height: 32px;
        border-radius: 9px;
        border: none;
        display: flex; align-items: center; justify-content: center;
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
    .empty-state a { color: #FF6B6B; font-weight: 700; text-decoration: none; font-size: 14px; }
    .empty-state a:hover { text-decoration: underline; }

    .table-footer {
        padding: 14px 20px;
        background: #FFF5F2;
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
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
</style>

{{-- Alert --}}
@if(session('success'))
<div class="alert-success">
    <span style="margin-right:6px;">✔️</span>{{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="pg-header">
    <div class="pg-header-left">
        <h2><span><i class="fas fa-users"></i></span> Loving Guardians</h2>
        <p>Registered parents & guardians in the system</p>
    </div>
    <div class="pg-header-right">
        <a href="#" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('parents.create') }}" class="btn-register">
            <i class="fas fa-plus"></i> Register Guardian
        </a>
    </div>
</div>

{{-- Stat Cards --}}
@php
    $total     = $parents->count();
    $mothers   = $parents->where('relation', 'mother')->count();
    $fathers   = $parents->where('relation', 'father')->count();
    $emergency = $parents->where('emergency', 1)->count();
@endphp

<div class="stat-row">
    <div class="stat-card pink">
        <div class="stat-icon pink"><span>👥</span></div>
        <div>
            <div class="stat-num">{{ $total }}</div>
            <div class="stat-label">Total Guardians</div>
        </div>
    </div>
    <div class="stat-card rose">
        <div class="stat-icon rose"><span>👩</span></div>
        <div>
            <div class="stat-num">{{ $mothers }}</div>
            <div class="stat-label">Mothers</div>
        </div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon blue"><span>👨</span></div>
        <div>
            <div class="stat-num">{{ $fathers }}</div>
            <div class="stat-label">Fathers</div>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><span><i class="fas fa-exclamation-triangle"></i></span></div>
        <div>
            <div class="stat-num">{{ $emergency }}</div>
            <div class="stat-label">Emergency Contact</div>
        </div>
    </div>
</div>

{{-- Search + Filter --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search name, phone...">
    </div>
    <select class="filter-select" id="filterRelation">
        <option value="">All Relations</option>
        <option value="mother">Mother</option>
        <option value="father">Father</option>
        <option value="guardian">Guardian</option>
        <option value="other">Other</option>
    </select>
    <select class="filter-select" id="filterStatus">
        <option value="">All Status</option>
        <option value="verified">Verified</option>
        <option value="unverified">Unverified</option>
        <option value="emergency">Emergency</option>
    </select>
    <span class="record-count" id="recordCount">{{ $total }} records</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="pg-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Guardian</th>
                <th>Relation</th>
                <th>Phone</th>
                <th>Second Parent</th>
                <th>Status</th>
                <th>QR Check-in</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($parents as $i => $parent)
            <tr>
                <td style="color:#94a3b8; font-weight:700;">{{ $i + 1 }}</td>

                {{-- Guardian --}}
                <td>
                    <div class="guardian-cell">
                        <div class="guardian-avatar">
                            @if($parent->photo)
                                <img src="{{ asset('storage/'.$parent->photo) }}" alt="">
                            @else
                                {{ strtoupper(substr($parent->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p class="guardian-name">{{ $parent->name }}</p>
                            <p class="guardian-sub">{{ $parent->age ? $parent->age.' yrs' : '' }}{{ $parent->age && $parent->phone_number ? ' · ' : '' }}{{ $parent->phone_number ?? '' }}</p>
                        </div>
                    </div>
                </td>

                {{-- Relation --}}
                <td>
                    @php $rel = strtolower($parent->relation ?? 'other'); @endphp
                    <span class="relation-badge {{ $rel }}">
                        @if($rel === 'mother')
                            <span>👩</span> Mother
                        @elseif($rel === 'father')
                            <span>👨</span> Father
                        @elseif($rel === 'guardian')
                            <span>🛡️</span> Guardian
                        @else
                            <span><i class="fas fa-user"></i></span> {{ ucfirst($rel) ?: 'Other' }}
                        @endif
                    </span>
                </td>

                {{-- Phone --}}
                <td>{{ $parent->phone_number ?? '-' }}</td>

                {{-- Second Parent --}}
                <td>
                    @if($parent->secondParent)
                        <div class="guardian-cell">
                            <div class="guardian-avatar" style="width:30px;height:30px;font-size:12px;border-radius:8px;">
                                @if($parent->secondParent->photo)
                                    <img src="{{ asset('storage/'.$parent->secondParent->photo) }}" alt="">
                                @else
                                    {{ strtoupper(substr($parent->secondParent->name, 0, 1)) }}
                                @endif
                            </div>
                            <span style="font-size:12px; color:#475569; font-weight:600;">
                                {{ $parent->secondParent->name }}
                            </span>
                        </div>
                    @else
                        <span style="font-size:12px; color:#cbd5e1;">—</span>
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    @if($parent->emergency)
                        <span class="status-badge emergency">
                            <span><i class="fas fa-exclamation-triangle"></i></span> Emergency
                        </span>
                    @elseif($parent->verified)
                        <span class="status-badge verified">
                            <i class="fas fa-check-circle"></i> Verified
                        </span>
                    @else
                        <span class="status-badge unverified">
                            <span>⏰</span> Unverified
                        </span>
                    @endif
                </td>

                {{-- QR --}}
                <td>
                    <span class="qr-badge">
                        <span>📱</span> QR-{{ str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </td>

                {{-- Actions --}}
                <td>
                    <div class="action-btns">
                        <a href="{{ route('parents.show', $parent->id) }}"
                            class="act-btn view" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('parents.edit', $parent->id) }}"
                            class="act-btn edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('parents.destroy', $parent->id) }}"
                            method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="act-btn delete" title="Delete"
                                onclick="return confirm('Delete {{ addslashes($parent->name) }}?')">
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
                        <div class="empty-icon">
                            <span><i class="fas fa-users"></i></span>
                        </div>
                        <h5>No records found.</h5>
                        <p>Start by registering your first guardian.</p>
                        <a href="{{ route('parents.create') }}">+ Register new guardian</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
     </table>

    @if($parents->count() > 0)
    <div class="table-footer">
        <span>ℹ️</span>
        Click any row to view full profile &nbsp;·&nbsp; {{ $total }} total records
    </div>
    @endif
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('filterRelation').addEventListener('change', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);

    function filterTable() {
        const search   = document.getElementById('searchInput').value.toLowerCase();
        const relation = document.getElementById('filterRelation').value.toLowerCase();
        const status   = document.getElementById('filterStatus').value.toLowerCase();
        const rows     = document.querySelectorAll('#tableBody tr');
        let visible    = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const matchSearch   = text.includes(search);
            const matchRelation = relation === '' || text.includes(relation);
            const matchStatus   = status === ''   || text.includes(status);

            if (matchSearch && matchRelation && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('recordCount').textContent = visible + ' records';
    }

    document.querySelectorAll('#tableBody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-btns')) return;
            const viewBtn = row.querySelector('.act-btn.view');
            if (viewBtn) viewBtn.click();
        });
    });
</script>

@endsection
