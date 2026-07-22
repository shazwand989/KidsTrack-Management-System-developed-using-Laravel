@extends('layouts.template')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')

<style>
    .cal-wrap { width: 100%; }

    /* Header */
    .cal-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 14px; margin-bottom: 18px;
    }
    .cal-header h2 { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; }
    .cal-header h2 span { color: #FF6B6B; }

    /* View toggle */
    .view-toggle { display: flex; gap: 4px; background: #f1f5f9; border-radius: 10px; padding: 3px; }
    .view-toggle button {
        border: none; padding: 6px 16px; border-radius: 8px; font-size: 12px;
        font-weight: 700; cursor: pointer; transition: .15s; background: transparent; color: #64748b;
    }
    .view-toggle button.active { background: white; color: #FF6B6B; box-shadow: 0 1px 3px rgba(0,0,0,.08); }

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
        <h2><i class="fas fa-calendar-alt"></i> <span>Attendance</span> Calendar</h2>
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="view-toggle">
                <button id="btnWeek" class="active" onclick="switchView('week')">Week</button>
                <button id="btnMonth" onclick="switchView('month')">Month</button>
            </div>
            <div class="week-nav">
                <button onclick="nav(-1)">‹ Prev</button>
                <span class="week-label" id="weekLabel"></span>
                <button onclick="nav(1)">Next ›</button>
                <button onclick="nav(0)" style="background:#FF6B6B;color:white;">Today</button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card"><div class="stat-dot green"><i class="fas fa-check"></i></div><div><div class="stat-num" id="statPresent">--</div><div class="stat-lbl">Present</div></div></div>
        <div class="stat-card"><div class="stat-dot orange"><i class="fas fa-exclamation-triangle"></i></div><div><div class="stat-num" id="statLate">--</div><div class="stat-lbl">Late</div></div></div>
        <div class="stat-card"><div class="stat-dot red"><i class="fas fa-times"></i></div><div><div class="stat-num" id="statAbsent">--</div><div class="stat-lbl">Absent</div></div></div>
        <div class="stat-card"><div class="stat-dot blue"><i class="fas fa-child"></i></div><div><div class="stat-num" id="statKids">--</div><div class="stat-lbl">Children</div></div></div>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <input type="text" id="searchCal" placeholder="Search child..." oninput="renderCalendar()">
        <select id="classCal" onchange="renderCalendar()">
            <option value="all">All Classes</option>
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

@php
$attData = $attendances->map(function($a) {
    return [
        'id' => $a->id,
        'child_id' => $a->child_id,
        'date' => $a->date instanceof \Carbon\Carbon ? $a->date->format('Y-m-d') : $a->date,
        'status' => $a->status,
        'checkin_time' => $a->checkin_time ? \Carbon\Carbon::parse($a->checkin_time)->format('h:i A') : null,
        'checkout_time' => $a->checkout_time ? \Carbon\Carbon::parse($a->checkout_time)->format('h:i A') : null,
        'child_name' => $a->child->name ?? '',
        'classroom' => $a->child->classroom->name ?? '-',
        'class_start' => $a->child->classroom->start_time ?? null,
        'class_end' => $a->child->classroom->end_time ?? null,
    ];
});
@endphp

<script>
const children = @json($children);
const attendances = @json($attData);
const todayStr = '{{ now()->toDateString() }}';
const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

let currentView = 'week';
let currentWeekStart = getMonday(new Date());
let currentMonthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1);

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

const attLookup = {};
attendances.forEach(a => {
    if (!attLookup[a.child_id]) attLookup[a.child_id] = {};
    attLookup[a.child_id][a.date] = a;
});

function switchView(view) {
    currentView = view;
    document.getElementById('btnWeek').classList.toggle('active', view === 'week');
    document.getElementById('btnMonth').classList.toggle('active', view === 'month');
    if (view === 'week') currentWeekStart = getMonday(new Date());
    else currentMonthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
    renderCalendar();
}

function nav(dir) {
    if (currentView === 'week') {
        if (dir === 0) currentWeekStart = getMonday(new Date());
        else currentWeekStart.setDate(currentWeekStart.getDate() + (dir * 7));
    } else {
        if (dir === 0) currentMonthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
        else currentMonthStart.setMonth(currentMonthStart.getMonth() + dir);
    }
    renderCalendar();
}

function getDays() {
    if (currentView === 'week') {
        const days = [];
        for (let i = 0; i < 5; i++) {
            const d = new Date(currentWeekStart);
            d.setDate(d.getDate() + i);
            days.push(d);
        }
        return days;
    } else {
        // Month: all Mon-Fri days of the month
        const days = [];
        const year = currentMonthStart.getFullYear();
        const month = currentMonthStart.getMonth();
        const lastDay = new Date(year, month + 1, 0).getDate();
        for (let d = 1; d <= lastDay; d++) {
            const date = new Date(year, month, d);
            const dow = date.getDay();
            if (dow !== 0 && dow !== 6) days.push(date); // Mon-Fri only
        }
        return days;
    }
}

function renderCalendar() {
    const search = document.getElementById('searchCal').value.toLowerCase();
    const cls = document.getElementById('classCal').value;

    let filtered = children.filter(c => {
        if (cls !== 'all' && c.classroom_id != cls) return false;
        if (search && !c.name.toLowerCase().includes(search)) return false;
        return true;
    });

    const days = getDays();

    // Label
    if (currentView === 'week') {
        document.getElementById('weekLabel').textContent = fmtShort(days[0]) + ' – ' + fmtShort(days[4]);
    } else {
        document.getElementById('weekLabel').textContent = monthNames[currentMonthStart.getMonth()] + ' ' + currentMonthStart.getFullYear();
    }

    // Head
    let headHtml = '<tr><th>Child</th>';
    days.forEach(d => {
        const isToday = fmtDate(d) === todayStr;
        const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        headHtml += `<th class="${isToday ? 'today-col' : ''}"><div>${dayNames[d.getDay()]}</div><div style="font-size:16px;">${d.getDate()}</div><div style="font-size:9px;font-weight:500;color:#64748b;">${monthNames[d.getMonth()]}</div></th>`;
    });
    headHtml += '</tr>';
    document.getElementById('calHead').innerHTML = headHtml;

    // Body
    let bodyHtml = '';
    let stats = {present: 0, late: 0, absent: 0};

    if (filtered.length === 0) {
        document.getElementById('emptyCal').style.display = 'block';
        document.getElementById('calBody').innerHTML = '';
    } else {
        document.getElementById('emptyCal').style.display = 'none';
        filtered.forEach(child => {
            bodyHtml += '<tr>';
            bodyHtml += `<td>
                <div class="child-info">
                    <div class="child-avatar">${child.name.charAt(0).toUpperCase()}</div>
                    <div><div class="child-name">${child.name}</div><div class="child-class">${child.classroom?.name || '-'} · ${child.age}y</div></div>
                </div></td>`;

            days.forEach(d => {
                const dateStr = fmtDate(d);
                const att = (attLookup[child.id] || {})[dateStr] || null;
                let cls = 'empty', label = '—';
                if (att) {
                    const s = att.status || '';
                    if (s === 'present' || s === 'checkin') { cls = 'present'; label = '✓'; stats.present++; }
                    else if (s === 'late' || s === 'late_checkout') { cls = 'late'; label = '⚠'; stats.late++; }
                    else if (s === 'absent') { cls = 'absent'; label = '✗'; stats.absent++; }
                    else if (s === 'checkout') { cls = 'present'; label = '✓'; stats.present++; }
                }
                const onClick = att ? `onclick="showAttModal('${child.name.replace(/'/g,"\\'")}','${att.classroom||'-'}','${dateStr}','${att.status||''}','${att.checkin_time||''}','${att.checkout_time||''}','${att.class_start||''}','${att.class_end||''}')"` : '';
                bodyHtml += `<td><span class="day-dot ${cls} ${att?'clickable':''}" ${onClick} title="${att?att.status||'No record':'No record'}">${label}</span></td>`;
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

renderCalendar();

function toMinutes(t){
    if(!t) return null;
    t=t.trim();
    var isPM=/pm/i.test(t),isAM=/am/i.test(t);
    t=t.replace(/\s*[ap]m\s*/i,'').trim();
    var parts=t.split(':');
    var h=parseInt(parts[0])||0,m=parseInt(parts[1])||0;
    if(isPM && h<12) h+=12;
    if(isAM && h==12) h=0;
    return h*60+m;
}

function showAttModal(name, classroom, date, status, ciTime, coTime, classStart, classEnd){
    document.getElementById('amName').textContent=name;
    document.getElementById('amClass').textContent='<i class="fas fa-school"></i> '+classroom+' · <i class="fas fa-calendar-alt"></i> '+date;

    var s=status||'No record';
    var sColor='#FF6B6B';
    if(s==='present'||s==='checkin'||s==='checkout') sColor='#16a34a';
    else if(s==='late'||s==='late_checkout') sColor='#d97706';
    else if(s==='absent') sColor='#dc2626';
    document.getElementById('amStatus').textContent=s;
    document.getElementById('amStatus').style.background=sColor+'18';
    document.getElementById('amStatus').style.color=sColor;

    // Check-in
    var ciBadge='',ciMins='';
    var ciMin=toMinutes(ciTime),schInMin=toMinutes(classStart);
    if(ciMin!==null&&schInMin!==null&&ciTime&&classStart){
        var diff=ciMin-schInMin;
        if(diff>0){
            ciBadge='<span style="background:#fff3e0;color:#e65100;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> Late</span>';
            ciMins='+'+diff+'m late';
        }else{
            ciBadge='<span style="background:#e8f5e9;color:#2e7d32;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> On Time</span>';
        }
    }
    document.getElementById('amCiTime').textContent=ciTime||'—';
    document.getElementById('amCiSched').textContent=classStart?(classStart.length>5?classStart.substring(0,5):classStart):'—';
    document.getElementById('amCiBadge').innerHTML=ciBadge;
    document.getElementById('amCiMins').textContent=ciMins;

    // Check-out
    var coBadge='',coMins='';
    var coMin=toMinutes(coTime),schOutMin=toMinutes(classEnd);
    if(coMin!==null&&schOutMin!==null&&coTime&&classEnd){
        var coDiff=coMin-schOutMin;
        if(coDiff<0){
            coBadge='<span style="background:#fce4ec;color:#c62828;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> Early</span>';
            coMins=Math.abs(coDiff)+'m early';
        }else if(coDiff>0){
            coBadge='<span style="background:#fff3e0;color:#e65100;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> Late</span>';
            coMins='+'+coDiff+'m late';
        }else{
            coBadge='<span style="background:#e8f5e9;color:#2e7d32;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> On Time</span>';
        }
    }
    document.getElementById('amCoTime').textContent=coTime||'—';
    document.getElementById('amCoSched').textContent=classEnd?(classEnd.length>5?classEnd.substring(0,5):classEnd):'—';
    document.getElementById('amCoBadge').innerHTML=coBadge;
    document.getElementById('amCoMins').textContent=coMins;

    document.getElementById('attModal').classList.add('show');
}
function closeAttModal(){document.getElementById('attModal').classList.remove('show');}
</script>

{{-- Modal --}}
<div class="modal-overlay" id="attModal" onclick="if(event.target===this)closeAttModal()">
    <div class="modal-box" style="background:white;border-radius:20px;padding:28px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
        <button onclick="closeAttModal()" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">✕</button>
        <div style="font-size:18px;font-weight:800;color:#1e293b;margin-bottom:2px;" id="amName"></div>
        <div style="font-size:12px;color:#94a3b8;margin-bottom:12px;" id="amClass"></div>
        <div style="display:inline-block;padding:4px 14px;border-radius:10px;font-size:12px;font-weight:700;background:#FFF5F2;color:#FF6B6B;margin-bottom:16px;" id="amStatus"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:#f8fafc;border-radius:12px;padding:12px;text-align:center;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;"><i class="fas fa-download"></i> Check-in</div>
                <div style="font-size:20px;font-weight:800;color:#1e293b;margin:4px 0;" id="amCiTime">—</div>
                <div style="font-size:11px;color:#64748b;">Schedule: <span id="amCiSched" style="font-weight:700;">—</span></div>
                <div style="margin-top:6px;" id="amCiBadge"></div>
                <div style="font-size:11px;font-weight:700;color:#c62828;margin-top:2px;" id="amCiMins"></div>
            </div>
            <div style="background:#f8fafc;border-radius:12px;padding:12px;text-align:center;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;"><i class="fas fa-upload"></i> Check-out</div>
                <div style="font-size:20px;font-weight:800;color:#1e293b;margin:4px 0;" id="amCoTime">—</div>
                <div style="font-size:11px;color:#64748b;">Schedule: <span id="amCoSched" style="font-weight:700;">—</span></div>
                <div style="margin-top:6px;" id="amCoBadge"></div>
                <div style="font-size:11px;font-weight:700;color:#c62828;margin-top:2px;" id="amCoMins"></div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:.2s;}
.modal-overlay.show{opacity:1;visibility:visible;}
.modal-overlay.show .modal-box{transform:translateY(0);}
.modal-box{transform:translateY(20px);transition:.3s;}
.day-dot.clickable{cursor:pointer;}
.day-dot.clickable:hover{transform:scale(1.3);}
</style>
@endsection
