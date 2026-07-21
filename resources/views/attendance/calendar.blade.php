@extends('layouts.template')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')

<style>
    .cal-wrap { max-width: 1400px; }

    /* Header */
    .cal-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 14px; margin-bottom: 18px;
    }
    .cal-header h2 { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; }
    .cal-header h2 span { color: #FF6B6B; }

    /* Week nav */
    .week-nav {
        display: flex; align-items: center; gap: 10px;
        background: white; border-radius: 14px; padding: 8px 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04); border: 1px solid #f1f5f9;
    }
    .week-nav button {
        border: none; background: #f1f5f9; border-radius: 8px;
        padding: 6px 12px; cursor: pointer; font-weight: 700; font-size: 13px;
        color: #475569; transition: .15s;
    }
    .week-nav button:hover { background: #FF6B6B; color: white; }
    .week-nav .week-label { font-weight: 800; font-size: 14px; color: #1e293b; min-width: 180px; text-align: center; }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px; }
    .stat-card {
        background: white; border-radius: 14px; padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 12px;
    }
    .stat-dot { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .stat-dot.green { background: #dcfce7; color: #16a34a; }
    .stat-dot.red { background: #fee2e2; color: #dc2626; }
    .stat-dot.orange { background: #fef3c7; color: #d97706; }
    .stat-dot.blue { background: #dbeafe; color: #2563eb; }
    .stat-num { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-lbl { font-size: 11px; color: #64748b; font-weight: 600; }

    /* Filter bar */
    .filter-bar {
        display: flex; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; align-items: center;
    }
    .filter-bar select, .filter-bar input {
        border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 14px;
        font-size: 13px; font-weight: 600; color: #334155;
    }
    .filter-bar input { min-width: 200px; }

    /* Table */
    .table-card {
        background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04);
        border: 1px solid #f1f5f9; overflow-x: auto;
    }
    .cal-table { width: 100%; border-collapse: collapse; min-width: 700px; }
    .cal-table thead th {
        background: #FFF5F2; padding: 12px 10px; font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: .04em; color: #92400E;
        border-bottom: 2px solid #FED7AA; text-align: center; white-space: nowrap;
    }
    .cal-table thead th:first-child { text-align: left; padding-left: 16px; min-width: 160px; }
    .cal-table thead th.today-col { background: #FFE4D6; }
    .cal-table tbody td {
        padding: 10px; text-align: center; border-bottom: 1px solid #FFF5F2;
        font-size: 13px; vertical-align: middle;
    }
    .cal-table tbody td:first-child { text-align: left; padding-left: 16px; }
    .cal-table tbody tr:hover { background: #FFFAF9; }
    .cal-table .child-info { display: flex; align-items: center; gap: 10px; }
    .cal-table .child-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px; flex-shrink: 0;
    }
    .cal-table .child-name { font-weight: 700; color: #0f172a; font-size: 13px; }
    .cal-table .child-class { font-size: 10px; color: #64748b; }

    /* Day status dots */
    .day-dot {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 50%; font-size: 10px; font-weight: 800;
        cursor: default; transition: .15s;
    }
    .day-dot.present { background: #dcfce7; color: #16a34a; }
    .day-dot.late { background: #fef3c7; color: #d97706; }
    .day-dot.absent { background: #fee2e2; color: #dc2626; }
    .day-dot.empty { background: #f8fafc; color: #cbd5e1; }
    .day-dot.weekend { background: #f1f5f9; color: #94a3b8; }
    .day-dot:hover { transform: scale(1.15); }

    /* Legend */
    .legend { display: flex; gap: 14px; margin-top: 14px; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #475569; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; }
    .legend-dot.present { background: #dcfce7; border: 2px solid #16a34a; }
    .legend-dot.late { background: #fef3c7; border: 2px solid #d97706; }
    .legend-dot.absent { background: #fee2e2; border: 2px solid #dc2626; }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state h5 { font-weight: 800; color: #1e293b; }

    @media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
</style>

<div class="cal-wrap">

    {{-- Header --}}
    <div class="cal-header">
        <h2>📅 <span>Attendance</span> Calendar</h2>
        <div class="week-nav">
            <button onclick="navWeek(-1)">‹ Prev</button>
            <span class="week-label" id="weekLabel"></span>
            <button onclick="navWeek(1)">Next ›</button>
            <button onclick="navWeek(0)" style="background:#FF6B6B;color:white;">Today</button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card"><div class="stat-dot green">✓</div><div><div class="stat-num" id="statPresent">--</div><div class="stat-lbl">Present</div></div></div>
        <div class="stat-card"><div class="stat-dot orange">⚠</div><div><div class="stat-num" id="statLate">--</div><div class="stat-lbl">Late</div></div></div>
        <div class="stat-card"><div class="stat-dot red">✗</div><div><div class="stat-num" id="statAbsent">--</div><div class="stat-lbl">Absent</div></div></div>
        <div class="stat-card"><div class="stat-dot blue">👶</div><div><div class="stat-num" id="statKids">--</div><div class="stat-lbl">Children</div></div></div>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <input type="text" id="searchCal" placeholder="🔍 Search child..." oninput="renderWeek()">
        <select id="classCal" onchange="renderWeek()">
            <option value="all">🏫 All Classes</option>
            @foreach($classrooms as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <table class="cal-table">
            <thead id="calHead"></thead>
            <tbody id="calBody"></tbody>
        </table>
    </div>

    {{-- Legend --}}
    <div class="legend">
        <div class="legend-item"><div class="legend-dot present"></div> Present</div>
        <div class="legend-item"><div class="legend-dot late"></div> Late</div>
        <div class="legend-item"><div class="legend-dot absent"></div> Absent</div>
    </div>

    <div class="empty-state" id="emptyCal" style="display:none;">
        <h5>No children found</h5>
        <p>Try adjusting your filter.</p>
    </div>

</div>

<script>
// Data from server
const children = @json($children);
const attendances = @json($attendances);
const todayStr = '{{ now()->toDateString() }}';

let currentWeekStart = getMonday(new Date());

function getMonday(d) {
    const date = new Date(d);
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(date.setDate(diff));
}

function fmtDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

function fmtShort(d) {
    return ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][d.getDay()] + ' ' + d.getDate();
}

// Build attendance lookup: {childId: {dateStr: status}}
const attLookup = {};
attendances.forEach(a => {
    if (!attLookup[a.child_id]) attLookup[a.child_id] = {};
    attLookup[a.child_id][a.date] = a.status;
});

function navWeek(dir) {
    if (dir === 0) currentWeekStart = getMonday(new Date());
    else currentWeekStart.setDate(currentWeekStart.getDate() + (dir * 7));
    renderWeek();
}

function renderWeek() {
    const search = document.getElementById('searchCal').value.toLowerCase();
    const cls = document.getElementById('classCal').value;

    // Filter children
    let filtered = children.filter(c => {
        if (cls !== 'all' && c.classroom_id != cls) return false;
        if (search && !c.name.toLowerCase().includes(search)) return false;
        return true;
    });

    // Build days array (Mon-Fri only)
    const days = [];
    for (let i = 0; i < 5; i++) {
        const d = new Date(currentWeekStart);
        d.setDate(d.getDate() + i);
        days.push(d);
    }

    // Week label
    document.getElementById('weekLabel').textContent =
        fmtShort(days[0]) + ' – ' + fmtShort(days[4]);

    // Table head
    let headHtml = '<tr><th>Child</th>';
    days.forEach(d => {
        const isToday = fmtDate(d) === todayStr;
        headHtml += `<th class="${isToday ? 'today-col' : ''}"><div>${['Mon','Tue','Wed','Thu','Fri'][d.getDay()-1]}</div><div style="font-size:16px;">${d.getDate()}</div><div style="font-size:9px;font-weight:500;color:#64748b;">${d.toLocaleDateString('en-MY',{month:'short'})}</div></th>`;
    });
    headHtml += '</tr>';
    document.getElementById('calHead').innerHTML = headHtml;

    // Table body
    let bodyHtml = '';
    let stats = {present: 0, late: 0, absent: 0};

    if (filtered.length === 0) {
        document.getElementById('emptyCal').style.display = 'block';
        document.getElementById('calBody').innerHTML = '';
    } else {
        document.getElementById('emptyCal').style.display = 'none';
        filtered.forEach((child, i) => {
            bodyHtml += '<tr>';
            bodyHtml += `<td>
                <div class="child-info">
                    <div class="child-avatar">${child.name.charAt(0).toUpperCase()}</div>
                    <div>
                        <div class="child-name">${child.name}</div>
                        <div class="child-class">${child.classroom?.name || '-'} · ${child.age}y</div>
                    </div>
                </div>
            </td>`;

            days.forEach(d => {
                const dateStr = fmtDate(d);
                const status = (attLookup[child.id] || {})[dateStr] || null;
                let cls = 'empty', label = '—';

                if (status) {
                    if (status === 'present' || status === 'checkin') { cls = 'present'; label = '✓'; stats.present++; }
                    else if (status === 'late' || status === 'late_checkout') { cls = 'late'; label = '⚠'; stats.late++; }
                    else if (status === 'absent') { cls = 'absent'; label = '✗'; stats.absent++; }
                    else if (status === 'checkout') { cls = 'present'; label = '✓'; stats.present++; }
                }

                bodyHtml += `<td><span class="day-dot ${cls}" title="${status || 'No record'}">${label}</span></td>`;
            });

            bodyHtml += '</tr>';
        });
        document.getElementById('calBody').innerHTML = bodyHtml;
    }

    document.getElementById('statPresent').textContent = stats.present;
    document.getElementById('statLate').textContent = stats.late;
    document.getElementById('statAbsent').textContent = stats.absent;
    document.getElementById('statKids').textContent = filtered.length;
}

renderWeek();
</script>

@endsection
