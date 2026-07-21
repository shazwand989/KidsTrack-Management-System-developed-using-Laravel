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
        <a href="{{ route('parents.export-csv') }}" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
        <a href="{{ route('parents.create') }}" class="btn-register">
            <i class="fas fa-plus"></i> Register Parent
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon pink"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-num">{{ $stats['totalFamilies'] }}</div>
            <div class="stat-label">Families</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-num">{{ $stats['verified'] }}</div>
            <div class="stat-label">Verified</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-child"></i></div>
        <div>
            <div class="stat-num">{{ $stats['totalChildren'] }}</div>
            <div class="stat-label">Children</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="stat-num">{{ $stats['guardianCount'] }}</div>
            <div class="stat-label">With Guardian</div>
        </div>
    </div>
</div>

{{-- Search & Per Page --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" class="search-input" id="searchInput"
            placeholder="Search family name, phone, email...">
    </div>
    <select class="filter-select" id="perPageSelect">
        <option value="10">10 per page</option>
        <option value="25">25 per page</option>
        <option value="50">50 per page</option>
        <option value="100">100 per page</option>
    </select>
    <span class="record-count" id="recordCount">{{ $stats['totalFamilies'] }} families</span>
</div>

{{-- Table --}}
<div class="table-card">
    <div style="min-height:300px;">
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
                <tr id="loadingRow">
                    <td colspan="6" style="text-align:center;padding:60px 20px;">
                        <div style="font-size:28px;margin-bottom:12px;">⏳</div>
                        <p style="color:#94a3b8;font-weight:600;">Loading families...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="pagination-wrap" id="paginationWrap" style="display:none;">
    <div class="pagination-info" id="paginationInfo"></div>
    <div class="pagination-links" id="paginationLinks"></div>
</div>

{{-- Empty State (hidden by default) --}}
<div id="emptyState" class="empty-state" style="display:none;margin-top:20px;">
    <div class="empty-icon">👨‍👩‍👧‍👦</div>
    <h5>No families found</h5>
    <p>Try adjusting your search or <a href="{{ route('parents.create') }}">register a parent</a>.</p>
</div>

<script>
// Pass route URLs to JS
window.parentRouteShow = "{{ route('parents.show', 'PLACEHOLDER') }}";
window.parentRouteEdit = "{{ route('parents.edit', 'PLACEHOLDER') }}";

let currentPage = 1;
let currentSearch = '';
let currentPerPage = 10;
let debounceTimer;

document.addEventListener('DOMContentLoaded', () => {
    loadFamilies();

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentSearch = this.value;
            currentPage = 1;
            loadFamilies();
        }, 300);
    });

    document.getElementById('perPageSelect').addEventListener('change', function() {
        currentPerPage = parseInt(this.value);
        currentPage = 1;
        loadFamilies();
    });
});

function loadFamilies() {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:60px 20px;">
        <div style="font-size:28px;margin-bottom:12px;">⏳</div>
        <p style="color:#94a3b8;font-weight:600;">Loading families...</p>
    </td></tr>`;

    fetch(`?search=${encodeURIComponent(currentSearch)}&per_page=${currentPerPage}&page=${currentPage}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.data.length === 0 && currentSearch === '') {
            // Completely empty
            document.getElementById('emptyState').style.display = 'block';
            tbody.innerHTML = '';
            document.getElementById('paginationWrap').style.display = 'none';
            document.getElementById('recordCount').textContent = '0 families';
            return;
        }

        if (res.data.length === 0) {
            // Search returned nothing
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:60px 20px;">
                <div style="font-size:48px;margin-bottom:12px;">🔍</div>
                <h5 style="color:#1e293b;font-weight:800;">No families match your search</h5>
                <p style="color:#94a3b8;">Try a different keyword.</p>
            </td></tr>`;
            document.getElementById('paginationWrap').style.display = 'none';
            document.getElementById('recordCount').textContent = '0 families';
            document.getElementById('emptyState').style.display = 'none';
            return;
        }

        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('recordCount').textContent = res.total + ' families';
        renderTable(res.data, res.from);
        renderPagination(res);
    })
    .catch(err => {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:60px 20px;">
            <p style="color:#dc2626;">Failed to load data. Please try again.</p>
        </td></tr>`;
    });
}

function renderTable(families, startIndex) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    families.forEach((family, i) => {
        const main    = family.main;
        const second  = family.second;
        const guardian = family.guardian;
        const children = family.children || [];
        const childCount = family.childCount || children.length;

        const mainInitial = main.name ? main.name.charAt(0).toUpperCase() : '?';
        const secInitial  = second ? second.name.charAt(0).toUpperCase() : '';
        const gdInitial   = guardian ? guardian.name.charAt(0).toUpperCase() : '';

        const mainPhoto = main.photo
            ? `<img src="/storage/${main.photo}" alt="">`
            : mainInitial;

        let childrenHtml = '';
        if (children.length) {
            children.forEach(child => {
                childrenHtml += `<span class="child-tag">${child.name}</span>`;
            });
        } else {
            childrenHtml = '<span style="color:#cbd5e1;">—</span>';
        }

        const rowNum = startIndex + i;

        tbody.innerHTML += `
        <tr onclick="window.location='${window.parentRouteShow.replace('PLACEHOLDER', main.id)}'" style="cursor:pointer;">
            <td style="color:#94a3b8;font-weight:700;">${rowNum}</td>
            <td>
                <div class="parent-cell">
                    <div class="parent-avatar" style="width:40px;height:40px;border-radius:10px;font-size:14px;">
                        ${mainPhoto}
                    </div>
                    <div>
                        <p class="parent-name" style="font-size:13px;">${main.name}</p>
                        <p class="parent-sub">📞 ${main.phone_number || '-'}</p>
                        ${main.verified ? '<span class="status-badge verified" style="font-size:9px;padding:1px 6px;">✓</span>' : ''}
                    </div>
                </div>
            </td>
            <td>
                ${second ? `
                <div class="parent-cell">
                    <div class="parent-avatar" style="width:34px;height:34px;border-radius:8px;font-size:12px;background:linear-gradient(135deg,#3b82f6,#60a5fa);">
                        ${secInitial}
                    </div>
                    <div>
                        <p class="parent-name" style="font-size:12px;">${second.name}</p>
                        <p class="parent-sub">📞 ${second.phone_number || '-'}</p>
                    </div>
                </div>` : '<span style="color:#cbd5e1;font-size:12px;">—</span>'}
            </td>
            <td>
                ${guardian ? `
                <div class="parent-cell">
                    <div class="parent-avatar" style="width:34px;height:34px;border-radius:8px;font-size:12px;background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                        ${gdInitial}
                    </div>
                    <div>
                        <p class="parent-name" style="font-size:12px;">${guardian.name}</p>
                        <p class="parent-sub">📞 ${guardian.phone_number || '-'}</p>
                    </div>
                </div>` : '<span style="color:#cbd5e1;font-size:12px;">—</span>'}
            </td>
            <td>${childrenHtml}</td>
            <td>
                <div class="action-btns" onclick="event.stopPropagation();">
                    <a href="${window.parentRouteShow.replace('PLACEHOLDER', main.id)}" class="act-btn view" title="View"><i class="fas fa-eye"></i></a>
                    <a href="${window.parentRouteEdit.replace('PLACEHOLDER', main.id)}" class="act-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                </div>
            </td>
        </tr>`;
    });
}

function renderPagination(res) {
    const wrap = document.getElementById('paginationWrap');
    const info = document.getElementById('paginationInfo');
    const links = document.getElementById('paginationLinks');

    if (res.last_page <= 1) {
        wrap.style.display = 'none';
        return;
    }

    wrap.style.display = 'flex';
    info.textContent = `Showing ${res.from}–${res.to} of ${res.total} families`;

    links.innerHTML = '';

    // Previous
    const prevDisabled = currentPage <= 1 ? 'disabled' : '';
    links.innerHTML += `<span class="${prevDisabled}"><a href="#" class="page-link" data-page="${currentPage - 1}">‹</a></span>`;

    // Page numbers
    const maxPages = 7;
    let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
    let endPage = Math.min(res.last_page, startPage + maxPages - 1);
    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }

    if (startPage > 1) {
        links.innerHTML += `<span><a href="#" class="page-link" data-page="1">1</a></span>`;
        if (startPage > 2) links.innerHTML += `<span class="disabled"><span class="page-link">…</span></span>`;
    }

    for (let p = startPage; p <= endPage; p++) {
        const active = p === currentPage ? 'active' : '';
        links.innerHTML += `<span class="${active}"><a href="#" class="page-link" data-page="${p}">${p}</a></span>`;
    }

    if (endPage < res.last_page) {
        if (endPage < res.last_page - 1) links.innerHTML += `<span class="disabled"><span class="page-link">…</span></span>`;
        links.innerHTML += `<span><a href="#" class="page-link" data-page="${res.last_page}">${res.last_page}</a></span>`;
    }

    // Next
    const nextDisabled = currentPage >= res.last_page ? 'disabled' : '';
    links.innerHTML += `<span class="${nextDisabled}"><a href="#" class="page-link" data-page="${currentPage + 1}">›</a></span>`;

    // Click handlers
    links.querySelectorAll('.page-link[data-page]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.dataset.page);
            if (page && page !== currentPage) {
                currentPage = page;
                loadFamilies();
                document.querySelector('.table-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}
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
