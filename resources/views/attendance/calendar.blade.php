@extends('layouts.template')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
    :root {
        --purple: #6d28d9;
        --purple-light: #ede9fe;
        --green: #16a34a;
        --green-light: #dcfce7;
        --blue: #2563eb;
        --blue-light: #dbeafe;
        --red: #dc2626;
        --red-light: #fee2e2;
        --orange: #d97706;
        --orange-light: #fef3c7;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-400: #94a3b8;
        --gray-700: #334155;
        --gray-900: #0f172a;
        --radius: 18px;
        --radius-sm: 12px;
    }

    .page-wrap { width: 100%; }

    /* Header */
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 16px; margin-bottom: 20px;
    }
    .page-header h1 { font-size: 22px; font-weight: 800; color: var(--gray-900); margin: 0; display: flex; align-items: center; gap: 10px; }
    .page-header h1 .badge { font-size: 11px; background: var(--purple-light); color: var(--purple); padding: 4px 14px; border-radius: 20px; font-weight: 700; }
    .header-date { font-size: 13px; color: var(--gray-400); font-weight: 600; }

    /* Stats Row */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
    .stat-card {
        background: white; border-radius: var(--radius); padding: 18px 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04); border: 1px solid var(--gray-100);
        display: flex; align-items: center; gap: 14px; transition: .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .stat-icon {
        width: 46px; height: 46px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.purple { background: var(--purple-light); color: var(--purple); }
    .stat-icon.green { background: var(--green-light); color: var(--green); }
    .stat-icon.blue { background: var(--blue-light); color: var(--blue); }
    .stat-icon.orange { background: var(--orange-light); color: var(--orange); }
    .stat-num { font-size: 26px; font-weight: 800; color: var(--gray-900); line-height: 1; }
    .stat-lbl { font-size: 11px; color: var(--gray-400); font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }

    /* Main Layout */
    .main-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 20px; align-items: start; }
    .card {
        background: white; border-radius: var(--radius); padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04); border: 1px solid var(--gray-100);
        min-width: 0;
    }
    .card-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; flex-wrap: wrap; gap: 10px;
    }
    .card-header h3 { font-size: 16px; font-weight: 800; color: var(--gray-900); margin: 0; display: flex; align-items: center; gap: 8px; }

    /* Filter */
    .filter-row { display: flex; gap: 10px; align-items: center; }
    .filter-row select {
        padding: 7px 14px; border: 1.5px solid var(--gray-200); border-radius: 10px;
        font-size: 13px; background: white; outline: none; color: var(--gray-700); font-weight: 600;
    }
    .filter-row select:focus { border-color: var(--purple); }
    .btn-sm {
        padding: 7px 16px; border: none; border-radius: 10px; font-weight: 700;
        font-size: 12px; cursor: pointer; transition: .2s;
    }
    .btn-sm.filter { background: var(--purple); color: white; }
    .btn-sm.filter:hover { background: #5b21b6; }
    .btn-sm.reset { background: var(--gray-100); color: var(--gray-700); }
    .btn-sm.reset:hover { background: var(--gray-200); }

    /* FullCalendar Overrides */
    .fc { font-family: 'Inter', sans-serif; }
    .fc .fc-toolbar-title { font-size: 1em !important; font-weight: 800; color: var(--gray-900); }
    .fc .fc-button { font-size: .72em !important; font-weight: 700 !important; border-radius: 10px !important; text-transform: capitalize !important; padding: 6px 14px !important; }
    .fc .fc-button-primary { background: var(--purple) !important; border-color: var(--purple) !important; }
    .fc .fc-button-primary:hover { background: #5b21b6 !important; }
    .fc .fc-button-primary.fc-button-active { background: #4c1d95 !important; }
    .fc .fc-daygrid-event { font-size: 10px; padding: 2px 6px; border-radius: 5px; font-weight: 600; cursor: pointer; border: none !important; }
    .fc .fc-day-today { background: var(--purple-light) !important; }
    .fc .fc-daygrid-day-number { font-weight: 700; font-size: 13px; color: var(--gray-700); }

    /* Legend */
    .legend-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--gray-100); }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: var(--gray-700); padding: 4px 10px; border-radius: 8px; background: var(--gray-50); }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
    .legend-dot.present { background: #43a047; } .legend-dot.checkout { background: #1e88e5; }
    .legend-dot.late { background: #e53935; } .legend-dot.absent { background: #fb8c00; }

    /* Timer Panel */
    .timer-panel { display: flex; flex-direction: column; gap: 16px; }
    .timer-panel .card { margin-bottom: 0 !important; }
    .timer-status-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;
    }
    .timer-chip {
        text-align: center; padding: 14px 10px; border-radius: var(--radius-sm);
        border: 2px solid transparent; transition: .2s;
    }
    .timer-chip.morning { background: #fef3c7; border-color: #fcd34d; }
    .timer-chip.evening { background: #e0e7ff; border-color: #c4b5fd; }
    .timer-chip .chip-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
    .timer-chip.morning .chip-label { color: #92400e; } .timer-chip.evening .chip-label { color: #4338ca; }
    .timer-chip .chip-time { font-size: 15px; font-weight: 800; color: var(--gray-900); font-family: monospace; }
    .timer-chip .chip-status {
        font-size: 9px; font-weight: 800; padding: 2px 10px; border-radius: 20px;
        display: inline-block; margin-top: 4px; text-transform: uppercase;
    }
    .chip-status.active { background: #22c55e; color: white; animation: pulse 1.5s ease-in-out infinite; }
    .chip-status.soon { background: #f59e0b; color: white; }
    .chip-status.closed { background: #e2e8f0; color: #64748b; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)}50%{opacity:.7;transform:scale(.95)} }

    /* Timer Settings Card */
    .set-card { background: var(--gray-50); border-radius: var(--radius-sm); padding: 18px; border: 1px solid var(--gray-200); margin-bottom: 14px; }
    .set-card .set-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .set-card.morning { border-left: 4px solid #f59e0b; } .set-card.morning .set-label { color: #92400e; }
    .set-card.evening { border-left: 4px solid #8b5cf6; } .set-card.evening .set-label { color: #4338ca; }
    .time-inputs { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .time-inputs input[type="time"] {
        flex: 1; min-width: 100px; padding: 8px 12px; border: 1.5px solid var(--gray-200); border-radius: 10px;
        font-size: 14px; background: white; outline: none; font-weight: 700; color: var(--gray-900); font-family: inherit;
    }
    .time-inputs input[type="time"]:focus { border-color: var(--purple); }
    .time-inputs .sep { color: var(--gray-400); font-weight: 700; font-size: 14px; flex-shrink: 0; }

    .btn-row { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
    .btn { padding: 10px 20px; border: none; border-radius: 12px; font-weight: 700; font-size: 13px; cursor: pointer; transition: .2s; white-space: nowrap; }
    .btn.save { background: #22c55e; color: white; flex: 1; min-width: 120px; }
    .btn.save:hover { background: #16a34a; box-shadow: 0 4px 12px rgba(34,197,94,0.3); }
    .btn.save:disabled { opacity: .5; cursor: not-allowed; }
    .btn.reset { background: var(--red); color: white; flex: 1; min-width: 120px; }
    .btn.reset:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,0.3); }
    .timer-msg { margin-top: 10px; padding: 10px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; display: none; }
    .timer-msg.success { display: block; background: var(--green-light); color: var(--green); }
    .timer-msg.error { display: block; background: var(--red-light); color: var(--red); }
    .timer-info-text { font-size: 11px; color: var(--gray-400); text-align: center; margin-top: 8px; font-weight: 600; }

    /* Modal */
    .att-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: .2s; }
    .att-modal-overlay.show { opacity: 1; visibility: visible; }
    .att-modal-box { background: white; border-radius: 22px; padding: 28px; max-width: 440px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; transform: translateY(20px); transition: .3s; }
    .att-modal-overlay.show .att-modal-box { transform: translateY(0); }
    .att-modal-close { position: absolute; top: 14px; right: 16px; background: none; border: none; font-size: 22px; cursor: pointer; color: var(--gray-400); padding: 4px 8px; border-radius: 8px; }
    .att-modal-close:hover { background: var(--gray-100); color: var(--gray-900); }
    .att-modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .att-modal-avatar { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 20px; flex-shrink: 0; }
    .att-modal-header h3 { font-size: 18px; font-weight: 800; color: var(--gray-900); margin: 0; }
    .att-modal-date { font-size: 12px; color: var(--gray-400); font-weight: 600; margin-bottom: 16px; }
    .att-modal-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-100); }
    .att-modal-row:last-child { border-bottom: none; }
    .att-modal-row .lbl { font-size: 12px; color: var(--gray-400); font-weight: 600; }
    .att-modal-row .val { font-size: 14px; font-weight: 700; color: var(--gray-900); }
    .att-modal-row .val.time { font-family: monospace; font-size: 15px; }
    .att-modal-badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 10px; font-size: 12px; font-weight: 700; }
    .att-modal-badge.present { background: var(--green-light); color: var(--green); } .att-modal-badge.checkout { background: var(--blue-light); color: var(--blue); }
    .att-modal-badge.late { background: var(--red-light); color: var(--red); } .att-modal-badge.absent { background: var(--orange-light); color: var(--orange); }

    @media (max-width: 1100px) {
        .main-grid { grid-template-columns: 1fr; }
        .timer-panel { flex-direction: row; flex-wrap: wrap; }
        .timer-panel .card { flex: 1; min-width: 280px; }
    }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .timer-panel { flex-direction: column; }
        .timer-panel .card { flex: none; min-width: auto; }
        .time-inputs { flex-direction: column; }
        .time-inputs .sep { display: none; }
        .btn-row { flex-direction: column; }
        .btn { flex: none !important; }
    }
    @media (max-width: 500px) {
        .stats-row { grid-template-columns: 1fr; }
        .timer-status-row { grid-template-columns: 1fr; }
    }
</style>

<div class="page-wrap">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1><i class="material-symbols-rounded" style="font-size:24px;">calendar_month</i> Attendance Calendar <span class="badge" id="eventCount">0 events</span></h1>
            <div class="header-date">{{ Carbon\Carbon::now()->format('l, d F Y') }}</div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon green"><i class="material-symbols-rounded">check_circle</i></div>
            <div><div class="stat-num" id="statPresent">--</div><div class="stat-lbl">Present</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="material-symbols-rounded">logout</i></div>
            <div><div class="stat-num" id="statCheckout">--</div><div class="stat-lbl">Checked Out</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="material-symbols-rounded">warning</i></div>
            <div><div class="stat-num" id="statLate">--</div><div class="stat-lbl">Late</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple"><i class="material-symbols-rounded">group</i></div>
            <div><div class="stat-num" id="statTotal">--</div><div class="stat-lbl">Total Records</div></div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="main-grid">

        {{-- Calendar Card --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="material-symbols-rounded" style="font-size:20px;">calendar_view_month</i> Calendar</h3>
                <div class="filter-row">
                    <select id="filterClassroom">
                        <option value="">All Classrooms</option>
                        @foreach($classrooms ?? [] as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn-sm filter" onclick="applyFilter()">Filter</button>
                    <button class="btn-sm reset" onclick="resetFilter()">Reset</button>
                </div>
            </div>
            <div id="calendar"></div>
            <div class="legend-bar">
                <div class="legend-item"><div class="legend-dot present"></div> Present</div>
                <div class="legend-item"><div class="legend-dot checkout"></div> Checked Out</div>
                <div class="legend-item"><div class="legend-dot late"></div> Late</div>
                <div class="legend-item"><div class="legend-dot absent"></div> Absent</div>
            </div>
        </div>

        {{-- Timer Panel --}}
        <div class="timer-panel">

            {{-- Live Status --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="material-symbols-rounded" style="font-size:20px;">schedule</i> Today's Schedule</h3>
                </div>
                <div class="timer-status-row" id="timerChips">
                    <div class="timer-chip morning">
                        <div class="chip-label">🌅 Morning Check-in</div>
                        <div class="chip-time">--:--</div>
                        <div class="chip-status closed">--</div>
                    </div>
                    <div class="timer-chip evening">
                        <div class="chip-label">🌙 Evening Check-out</div>
                        <div class="chip-time">--:--</div>
                        <div class="chip-status closed">--</div>
                    </div>
                </div>
            </div>

            {{-- Timer Settings --}}
            <div class="card">
                <div class="card-header">
                    <h3><i class="material-symbols-rounded" style="font-size:20px;">settings</i> Timer Settings</h3>
                </div>
                <p class="timer-info-text">Set operation hours for all days (Mon-Sun)</p>

                <div class="set-card morning">
                    <div class="set-label">🌅 Morning Session (Check-in Window)</div>
                    <div class="time-inputs">
                        <input type="time" id="morning_start" value="07:00" onchange="updateHidden()">
                        <span class="sep">to</span>
                        <input type="time" id="morning_end" value="07:30" onchange="updateHidden()">
                    </div>
                </div>

                <div class="set-card evening">
                    <div class="set-label">🌙 Evening Session (Check-out Window)</div>
                    <div class="time-inputs">
                        <input type="time" id="evening_start" value="17:00" onchange="updateHidden()">
                        <span class="sep">to</span>
                        <input type="time" id="evening_end" value="17:30" onchange="updateHidden()">
                    </div>
                </div>

                <form action="{{ route('kiosk.save.timer.settings') }}" method="POST" id="timerForm">
                    @csrf
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <input type="hidden" name="{{ $day }}[morning][start]" id="ms_{{ $day }}" value="07:00">
                        <input type="hidden" name="{{ $day }}[morning][end]" id="me_{{ $day }}" value="07:30">
                        <input type="hidden" name="{{ $day }}[evening][start]" id="es_{{ $day }}" value="17:00">
                        <input type="hidden" name="{{ $day }}[evening][end]" id="ee_{{ $day }}" value="17:30">
                    @endforeach
                </form>

                <div id="timerMsg" class="timer-msg"></div>

                <div class="btn-row">
                    <button class="btn save" id="saveBtn" onclick="saveTimers()">💾 Save All</button>
                    <button class="btn reset" onclick="resetTimers()">🔄 Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Attendance Detail Modal --}}
<div class="att-modal-overlay" id="attDetailModal" onclick="if(event.target===this)closeAttModal()">
    <div class="att-modal-box">
        <button class="att-modal-close" onclick="closeAttModal()"><i class="material-symbols-rounded" style="font-size:20px;">close</i></button>
        <div class="att-modal-header">
            <div class="att-modal-avatar" id="attModalAvatar" style="background:var(--purple);">?</div>
            <div>
                <h3 id="attModalChildName">Child Name</h3>
                <div class="att-modal-date" id="attModalDate">Date</div>
            </div>
        </div>
        <div class="att-modal-row"><span class="lbl">Classroom</span><span class="val" id="attModalClassroom">--</span></div>
        <div class="att-modal-row"><span class="lbl">Status</span><span class="att-modal-badge present" id="attModalStatus">Present</span></div>
        <div class="att-modal-row"><span class="lbl">Check-in</span><span class="val time" id="attModalCheckin">--</span></div>
        <div class="att-modal-row"><span class="lbl">Check-out</span><span class="val time" id="attModalCheckout">--</span></div>
    </div>
</div>

<script>
// ============ FULLCALENDAR ============
let cal, allEvents = [];
document.addEventListener('DOMContentLoaded', function() {
    cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek' },
        height: 'auto',
        events: function(info, successCallback) {
            var url = '/attendance-calendar-data?start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
            var cid = document.getElementById('filterClassroom').value;
            if (cid) url += '&classroom_id=' + encodeURIComponent(cid);
            fetch(url).then(function(r) { return r.json(); }).then(function(d) {
                allEvents = d;
                updateStats(d);
                successCallback(d);
            }).catch(function() { successCallback([]); });
        },
        eventDisplay: 'block',
        eventClick: function(info) {
            var p = info.event.extendedProps;
            var s = p.status || 'present';
            var labels = { 'present': 'Present / Checked In', 'checkin': 'Checked In', 'checkout': 'Checked Out', 'late': 'Late Check-in', 'late_checkout': 'Late Check-out', 'absent': 'Absent' };
            document.getElementById('attModalAvatar').style.background = p.color || '#6d28d9';
            document.getElementById('attModalAvatar').textContent = (p.child_name || '?').charAt(0).toUpperCase();
            document.getElementById('attModalChildName').textContent = p.child_name || 'Child';
            document.getElementById('attModalDate').textContent = info.event.startStr;
            document.getElementById('attModalClassroom').textContent = p.classroom || '--';
            document.getElementById('attModalStatus').textContent = labels[s] || s;
            document.getElementById('attModalStatus').className = 'att-modal-badge ' + (s === 'late_checkout' ? 'late' : s);
            document.getElementById('attModalCheckin').textContent = p.checkin_time || '--';
            document.getElementById('attModalCheckout').textContent = p.checkout_time || '--';
            document.getElementById('attDetailModal').classList.add('show');
        }
    });
    cal.render();
    loadTimerInfo();
    setInterval(loadTimerInfo, 60000);
});

function updateStats(events) {
    var present = 0, checkout = 0, late = 0;
    events.forEach(function(e) {
        var s = (e.extendedProps && e.extendedProps.status) || '';
        if (s === 'present' || s === 'checkin') present++;
        else if (s === 'checkout') checkout++;
        else if (s === 'late' || s === 'late_checkout') late++;
    });
    document.getElementById('statPresent').textContent = present;
    document.getElementById('statCheckout').textContent = checkout;
    document.getElementById('statLate').textContent = late;
    document.getElementById('statTotal').textContent = events.length;
    document.getElementById('eventCount').textContent = events.length + ' events';
}

function closeAttModal() { document.getElementById('attDetailModal').classList.remove('show'); }
function applyFilter() { cal.refetchEvents(); }
function resetFilter() { document.getElementById('filterClassroom').value = ''; cal.refetchEvents(); }
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeAttModal(); });

// ============ TIMER ============
const timerDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

function updateHidden() {
    var ms = document.getElementById('morning_start').value, me = document.getElementById('morning_end').value;
    var es = document.getElementById('evening_start').value, ee = document.getElementById('evening_end').value;
    timerDays.forEach(function(d) {
        var el = document.getElementById('ms_' + d); if (el) el.value = ms;
        el = document.getElementById('me_' + d); if (el) el.value = me;
        el = document.getElementById('es_' + d); if (el) el.value = es;
        el = document.getElementById('ee_' + d); if (el) el.value = ee;
    });
}

function loadTimerInfo() {
    fetch('/kiosk/get-timer-settings').then(function(r) { return r.json(); }).then(function(d) {
        if (!d.success) return;
        var t = d.data, now = new Date();
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var today = days[now.getDay()], timer = t[today];
        if (!timer) return;
        var ct = parseInt(String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0'));

        function chk(s, e) { var ss = parseInt(s.replace(':', '')), ee = parseInt(e.replace(':', '')); return ct >= ss && ct <= ee ? 'active' : (ct < ss ? 'soon' : 'closed'); }
        function lbl(s) { return { active: 'Active Now', soon: 'Upcoming', closed: 'Closed' }[s]; }

        // Update chips
        document.getElementById('timerChips').innerHTML =
            '<div class="timer-chip morning">' +
            '<div class="chip-label">🌅 Morning Check-in</div>' +
            '<div class="chip-time">' + timer.morning.start + ' - ' + timer.morning.end + '</div>' +
            '<div class="chip-status ' + chk(timer.morning.start, timer.morning.end) + '">' + lbl(chk(timer.morning.start, timer.morning.end)) + '</div>' +
            '</div>' +
            '<div class="timer-chip evening">' +
            '<div class="chip-label">🌙 Evening Check-out</div>' +
            '<div class="chip-time">' + timer.evening.start + ' - ' + timer.evening.end + '</div>' +
            '<div class="chip-status ' + chk(timer.evening.start, timer.evening.end) + '">' + lbl(chk(timer.evening.start, timer.evening.end)) + '</div>' +
            '</div>';

        // Update form
        document.getElementById('morning_start').value = timer.morning.start;
        document.getElementById('morning_end').value = timer.morning.end;
        document.getElementById('evening_start').value = timer.evening.start;
        document.getElementById('evening_end').value = timer.evening.end;
        updateHidden();
    }).catch(function() {});
}

function saveTimers() {
    var btn = document.getElementById('saveBtn'), msg = document.getElementById('timerMsg');
    var ms = document.getElementById('morning_start').value, me = document.getElementById('morning_end').value;
    var es = document.getElementById('evening_start').value, ee = document.getElementById('evening_end').value;
    var payload = {};
    timerDays.forEach(function(day) { payload[day] = { morning: { start: ms, end: me }, evening: { start: es, end: ee } }; });
    btn.disabled = true; btn.textContent = '⏳ Saving...'; msg.className = 'timer-msg';
    fetch('/kiosk/save-timer-settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload)
    }).then(function(r) { return r.json(); }).then(function(d) {
        msg.className = 'timer-msg ' + (d.success ? 'success' : 'error');
        msg.textContent = d.message;
        if (d.success) loadTimerInfo();
    }).catch(function(e) { msg.className = 'timer-msg error'; msg.textContent = 'Error: ' + e.message; })
    .finally(function() { btn.disabled = false; btn.textContent = '💾 Save All'; });
}

function resetTimers() {
    if (!confirm('Reset all timers to default (07:00-07:30, 17:00-17:30)?')) return;
    var msg = document.getElementById('timerMsg');
    fetch('/kiosk/reset-timer-settings', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
    .then(function(r) { return r.json(); }).then(function(d) {
        msg.className = 'timer-msg ' + (d.success ? 'success' : 'error');
        msg.textContent = d.message;
        if (d.success) { loadTimerInfo(); }
    }).catch(function(e) { msg.className = 'timer-msg error'; msg.textContent = 'Error: ' + e.message; });
}

setTimeout(updateHidden, 300);
</script>
@endsection
