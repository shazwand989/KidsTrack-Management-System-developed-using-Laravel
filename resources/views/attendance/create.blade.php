@extends('layouts.template')

@section('title', 'Take Attendance')
@section('page-title', 'Take Attendance')

@section('content')

<style>
    .att-container { max-width: 1400px; margin: 0 auto; }

    /* Top bar */
    .top-bar {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        border-radius: 20px;
        padding: 18px 24px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }
    .top-bar-left h3 { color: white; margin: 0; font-size: 18px; }
    .top-bar-left p { color: rgba(255,255,255,.85); margin: 2px 0 0; font-size: 13px; }
    .clock { font-size: 22px; font-weight: 800; letter-spacing: 2px; }

    /* Quick actions */
    .quick-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .qa-btn {
        border: none;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: .15s;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .qa-present { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .qa-present:hover { background: #16a34a; color: white; }
    .qa-absent  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .qa-absent:hover  { background: #d97706; color: white; }
    .qa-clear   { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .qa-clear:hover   { background: #e2e8f0; }
    .qa-save    { background: linear-gradient(135deg, #FF6B6B, #FF9E7D); color: white; box-shadow: 0 4px 12px rgba(255,107,107,.25); }
    .qa-save:hover    { opacity: .9; transform: translateY(-1px); }

    /* Search + filter row */
    .tool-row {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
        flex-wrap: wrap;
        align-items: center;
    }
    .tool-row input {
        flex: 1; min-width: 200px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 13px;
        color: #1e293b;
    }
    .tool-row select {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }
    .counter-badge {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        white-space: nowrap;
    }

    /* Table */
    .table-wrap {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,.04);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table thead { background: #FFF5F2; }
    .att-table th {
        padding: 12px 14px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #92400E;
        text-align: left;
        white-space: nowrap;
    }
    .att-table tbody td {
        padding: 10px 14px;
        font-size: 13px;
        border-bottom: 1px solid #FFF5F2;
        vertical-align: middle;
        color: #334155;
    }
    .att-table tbody tr:hover { background: #FFFAF9; }
    .att-table tbody tr.done { opacity: .65; }
    .att-table tbody tr.done td { background: #f8fafc; }
    .att-table .child-name { font-weight: 700; color: #0f172a; font-size: 14px; }
    .att-table .child-meta { font-size: 11px; color: #64748b; font-weight: 500; }

    /* Status pills in table */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: .15s;
        border: 1px solid transparent;
    }
    .sp-present { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
    .sp-present.sel { background: #16a34a; color: white; }
    .sp-late    { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .sp-late.sel    { background: #d97706; color: white; }
    .sp-absent  { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
    .sp-absent.sel  { background: #dc2626; color: white; }
    .sp-none    { background: #f1f5f9; color: #64748b; border-color: #cbd5e1; font-weight: 600; }

    /* Time inputs */
    .time-inp {
        width: 90px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 7px 8px;
        font-size: 13px;
        font-family: monospace;
        text-align: center;
        color: #1e293b;
        font-weight: 600;
    }
    .name-inp {
        width: 140px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 13px;
        color: #1e293b;
        font-weight: 500;
    }
    .late-reason-inp {
        width: 160px;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 12px;
        background: #fffbeb;
        color: #92400E;
        font-weight: 500;
    }

    .saved-badge {
        display: inline-block;
        background: #DCFCE7;
        color: #15803D;
        padding: 3px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
    }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state h5 { font-weight: 800; color: #1e293b; }

    .toast {
        position: fixed; top: 20px; right: 20px;
        padding: 14px 22px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 13px;
        z-index: 9999;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        animation: slideIn .3s ease;
        display: none;
    }
    .toast.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .toast.error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

    @media (max-width: 768px) {
        .top-bar { flex-direction: column; align-items: flex-start; }
        .quick-actions { overflow-x: auto; flex-wrap: nowrap; }
    }
</style>

<div class="att-container">

    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="top-bar-left">
            <h3><i class="fas fa-clipboard-list"></i> Take Attendance</h3>
            <p id="dateDisplay"></p>
        </div>
        <div class="clock" id="liveClock">--:--:--</div>
        <div>
            <button class="qa-btn qa-save" onclick="saveAll()" style="padding:12px 24px;font-size:14px;">
                <i class="fas fa-save"></i> Save All Records
            </button>
        </div>
    </div>

    {{-- Toast --}}
    <div class="toast" id="toast"></div>

    {{-- Quick Actions --}}
    <div class="quick-actions">
        <button class="qa-btn qa-present" onclick="setAll('present')"><i class="fas fa-check-circle"></i> Mark All Present</button>
        <button class="qa-btn qa-absent" onclick="setAll('absent')"><i class="fas fa-times-circle"></i> Mark All Absent</button>
        <button class="qa-btn qa-clear" onclick="setAll(null)">🔄 Clear All</button>
        <span style="flex:1;"></span>
        <span class="counter-badge" id="counterBadge">0 / {{ $children->count() }} marked</span>
    </div>

    {{-- Search + Filter --}}
    <div class="tool-row">
        <input type="text" id="searchInput" placeholder="<i class="fas fa-search"></i> Search child name..." oninput="filterTable()">
        <select id="classFilter" onchange="filterTable()">
            <option value="all"><i class="fas fa-school"></i> All Classes</option>
            @foreach($classrooms as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <span class="counter-badge" id="visibleCount">{{ $children->count() }} shown</span>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="att-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Child</th>
                    <th>Class</th>
                    <th style="width:220px;">Status</th>
                    <th>Check-in</th>
                    <th>Drop Off By</th>
                    <th>Late Reason</th>
                    <th style="width:60px;">✓</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @foreach($children as $i => $child)
                @php
                    $today = $todayAttendance[$child->id] ?? null;
                    $guardianInfo = $child->guardianships
                        ->where('relationship', 'main_parent')
                        ->first()?->user?->name ?? '';
                @endphp
                <tr data-child="{{ $child->id }}"
                    data-class="{{ $child->classroom_id }}"
                    data-status="{{ $today->status ?? 'none' }}"
                    data-name="{{ strtolower($child->name) }}"
                    data-search="{{ strtolower($child->name) }} {{ strtolower($child->classroom->name ?? '') }}"
                    class="{{ $today ? 'done' : '' }}">
                    <td class="row-num" style="color:#64748b;font-weight:700;">{{ $i + 1 }}</td>
                    <td>
                        <span class="child-name">{{ $child->name }}</span>
                        <div class="child-meta"><i class="fas fa-child"></i> {{ $child->age }}y</div>
                    </td>
                    <td><span class="child-meta">{{ $child->classroom->name ?? '-' }}
                        @if($child->classroom?->start_time)
                            · ⏰ {{ substr($child->classroom->start_time, 0, 5) }}
                        @endif
                    </span></td>
                    <td>
                        <span class="status-pill sp-none" id="pill-{{ $child->id }}"
                              onclick="cycleStatus({{ $child->id }})">— Tap to set —</span>
                    </td>
                    <td>
                        <input type="time" class="time-inp" id="timeIn-{{ $child->id }}"
                               value="{{ $today?->checkin_time ? substr($today->checkin_time,0,5) : '07:15' }}"
                               onchange="markDirty({{ $child->id }})">
                    </td>
                    <td>
                        <input type="text" class="name-inp" id="parent-{{ $child->id }}"
                               value="{{ $today?->drop_off_by ?? $guardianInfo }}"
                               placeholder="Parent name"
                               onchange="markDirty({{ $child->id }})">
                    </td>
                    <td>
                        <input type="text" class="late-reason-inp" id="reason-{{ $child->id }}"
                               value="{{ $today?->late_reason ?? '' }}"
                               placeholder="e.g. Traffic jam"
                               onchange="markDirty({{ $child->id }})">
                    </td>
                    <td>
                        @if($today)
                            <span class="saved-badge">Saved</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($children->isEmpty())
    <div class="empty-state">
        <h5>No children registered</h5>
        <p>Register children first.</p>
    </div>
    @endif

</div>

<script>
const statusMap = [null, 'present', 'late', 'absent'];
const statusLabels = ['— Tap to set —', '<i class="fas fa-check-circle"></i> Present', '<i class="fas fa-exclamation-triangle"></i> Late', '<i class="fas fa-times-circle"></i> Absent'];
const statusClasses = ['sp-none', 'sp-present', 'sp-late', 'sp-absent'];

let attendanceState = {};
const totalChildren = {{ $children->count() }};

// Init from server data
@foreach($children as $child)
    @php $today = $todayAttendance[$child->id] ?? null; @endphp
    @if($today)
        attendanceState[{{ $child->id }}] = {
            status: '{{ $today->status }}',
            checkin_time: '{{ $today->checkin_time ? substr($today->checkin_time,0,5) : '07:15' }}',
            drop_off_by: '{{ addslashes($today->drop_off_by ?? '') }}',
            late_reason: '{{ addslashes($today->late_reason ?? '') }}'
        };
        updatePill({{ $child->id }}, '{{ $today->status }}');
    @endif
@endforeach

// Live clock
function tick() {
    const now = new Date();
    document.getElementById('liveClock').textContent =
        now.toLocaleTimeString('en-MY', { hour12: false });
    document.getElementById('dateDisplay').textContent =
        now.toLocaleDateString('en-MY', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
}
tick(); setInterval(tick, 1000);

// Cycle status: none → present → late → absent → none
function cycleStatus(childId) {
    const pill = document.getElementById('pill-' + childId);
    let current = Object.values(statusMap).indexOf(getState(childId).status);
    if (current === -1) current = 0; // no status → start from null, next → present
    current = (current + 1) % statusMap.length;
    const newStatus = statusMap[current];

    const row = document.querySelector(`tr[data-child="${childId}"]`);
    row.classList.remove('done');

    if (!newStatus) {
        delete attendanceState[childId];
    } else {
        attendanceState[childId] = attendanceState[childId] || {};
        attendanceState[childId].status = newStatus;
    }
    updatePill(childId, newStatus);
    updateCounter();
}

function updatePill(childId, status) {
    const pill = document.getElementById('pill-' + childId);
    const idx = statusMap.indexOf(status);
    pill.textContent = statusLabels[idx >= 0 ? idx : 0];
    pill.className = 'status-pill ' + statusClasses[idx >= 0 ? idx : 0] + (status ? ' sel' : '');
}

function getState(childId) {
    return attendanceState[childId] || {};
}

function markDirty(childId) {
    const row = document.querySelector(`tr[data-child="${childId}"]`);
    if (row) row.classList.remove('done');
}

// Set all visible to one status
function setAll(status) {
    document.querySelectorAll('#tableBody tr').forEach(row => {
        if (row.style.display === 'none') return;
        const childId = parseInt(row.dataset.child);
        if (status) {
            attendanceState[childId] = attendanceState[childId] || {};
            attendanceState[childId].status = status;
            row.classList.remove('done');
            updatePill(childId, status);
        } else {
            delete attendanceState[childId];
            row.classList.remove('done');
            updatePill(childId, null);
        }
    });
    updateCounter();
    renumberRows();
}

function updateCounter() {
    const count = Object.keys(attendanceState).length;
    document.getElementById('counterBadge').textContent = count + ' / ' + totalChildren + ' marked';
}

// Renumber visible rows
function renumberRows() {
    let n = 0;
    document.querySelectorAll('#tableBody tr').forEach(row => {
        if (row.style.display !== 'none') {
            n++;
            const numCell = row.querySelector('.row-num');
            if (numCell) numCell.textContent = n;
        }
    });
}

// Filter table with dynamic renumbering
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const cls = document.getElementById('classFilter').value;
    let visible = 0;
    document.querySelectorAll('#tableBody tr').forEach(row => {
        const matchSearch = !search || (row.dataset.search || '').includes(search);
        const matchClass = cls === 'all' || row.dataset.class === cls;
        row.style.display = (matchSearch && matchClass) ? '' : 'none';
        if (matchSearch && matchClass) {
            visible++;
            const numCell = row.querySelector('.row-num');
            if (numCell) numCell.textContent = visible;
        }
    });
    document.getElementById('visibleCount').textContent = visible + ' shown';
}

// Save all
async function saveAll() {
    // Collect data from inputs
    document.querySelectorAll('#tableBody tr').forEach(row => {
        const childId = parseInt(row.dataset.child);
        if (!attendanceState[childId]) return;
        const tinp = document.getElementById('timeIn-' + childId);
        const pinp = document.getElementById('parent-' + childId);
        const rinp = document.getElementById('reason-' + childId);
        if (tinp) attendanceState[childId].checkin_time = tinp.value;
        if (pinp) attendanceState[childId].drop_off_by = pinp.value;
        if (rinp) attendanceState[childId].late_reason = rinp.value;
    });

    if (Object.keys(attendanceState).length === 0) {
        showToast('No attendance marked. Click a status pill first.', 'error');
        return;
    }

    try {
        const resp = await fetch('{{ route("attendance.batch-store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                date: new Date().toISOString().split('T')[0],
                attendances: attendanceState
            })
        });
        const result = await resp.json();
        if (result.success) {
            showToast('<i class="fas fa-check-circle"></i> ' + result.saved + ' records saved!', 'success');
            // Mark saved rows
            document.querySelectorAll('#tableBody tr').forEach(row => {
                const childId = parseInt(row.dataset.child);
                if (attendanceState[childId]) row.classList.add('done');
            });
            // Refresh saved badges
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('Error: ' + (result.errors || []).join(', '), 'error');
        }
    } catch (e) {
        showToast('Network error. Please try again.', 'error');
    }
}

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + type;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

updateCounter();
</script>

@endsection