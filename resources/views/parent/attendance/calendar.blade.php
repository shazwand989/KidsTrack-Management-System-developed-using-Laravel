@extends('layouts.parent-template')

@php
    use App\Models\Attendance;
    use App\Models\SimulationClock;
    $today = date('Y-m-d', SimulationClock::getCurrentTime());
    $childIds = $children->pluck('id')->toArray();
    $todayAttendance = Attendance::whereIn('child_id', $childIds)->where('date', $today)->get();
@endphp

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<style>
    .cal-wrapper{display:grid;grid-template-columns:260px 1fr;gap:20px;}
    .cal-card{background:white;border-radius:20px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,0.05);border:1px solid #f1f5f9;}
    .cal-card h3{font-size:14px;font-weight:800;color:#1e293b;margin:0 0 14px;display:flex;align-items:center;gap:8px;}
    .ts-item{text-align:center;padding:12px 8px;border-radius:12px;background:#f8fafc;}
    .ts-num{font-size:22px;font-weight:800;}.ts-lbl{font-size:10px;color:#94a3b8;font-weight:600;}
    .ts-item.green{background:#e8f5e9;}.ts-item.green .ts-num{color:#2e7d32;}
    .ts-item.blue{background:#e3f2fd;}.ts-item.blue .ts-num{color:#1565c0;}
    .ts-item.red{background:#fce4ec;}.ts-item.red .ts-num{color:#c62828;}
    .fc{font-family:'Inter',sans-serif;}.fc .fc-toolbar-title{font-size:1em!important;font-weight:800;}
    .fc .fc-button{font-size:.75em!important;font-weight:700!important;border-radius:10px!important;}
    .fc .fc-button-primary{background:#6d28d9!important;border-color:#6d28d9!important;}
    .fc .fc-daygrid-event{font-size:10px;padding:2px 4px;border-radius:4px;font-weight:600;}
    .fc .fc-day-today{background:#ede9fe!important;}
    .today-summary{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
    .legend-bar{display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;}
    .legend-item{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;color:#475569;padding:4px 10px;border-radius:8px;background:#f8fafc;}
    .legend-dot{width:12px;height:12px;border-radius:4px;flex-shrink:0;}
    .legend-dot.present{background:#43a047;}.legend-dot.checkout{background:#1e88e5;}
    .legend-dot.late{background:#e53935;}.legend-dot.absent{background:#fb8c00;}
    /* Modal */
    .modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:.2s;}
    .modal-overlay.show{opacity:1;visibility:visible;}
    .modal-box{background:white;border-radius:20px;padding:28px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;transform:translateY(20px);transition:.3s;}
    .modal-overlay.show .modal-box{transform:translateY(0);}
    .modal-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;padding:4px 8px;border-radius:8px;}
    .modal-close:hover{background:#f1f5f9;color:#1e293b;}
    .modal-title{font-size:18px;font-weight:800;color:#1e293b;margin:0 0 20px;display:flex;align-items:center;gap:10px;}
    .modal-avatar{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:18px;}
    .modal-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9;}
    .modal-row:last-child{border-bottom:none;}
    .modal-row .lbl{font-size:12px;color:#94a3b8;font-weight:600;}
    .modal-row .val{font-size:14px;font-weight:700;color:#1e293b;}
    .modal-row .val.time{font-family:monospace;font-size:15px;}
    .modal-status{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:10px;font-size:12px;font-weight:700;}
    .modal-status.present{background:#e8f5e9;color:#2e7d32;}.modal-status.checkout{background:#e3f2fd;color:#1565c0;}
    .modal-status.late{background:#fce4ec;color:#c62828;}.modal-status.absent{background:#fff3e0;color:#e65100;}
    @media(max-width:768px){.cal-wrapper{grid-template-columns:1fr;}}
</style>
<div class="cal-wrapper">
    <div class="cal-sidebar">
        <div class="cal-card">
            <h3><i class="material-symbols-rounded" style="font-size:16px;">today</i> Today</h3>
            <div class="today-summary">
                <div class="ts-item green"><div class="ts-num">{{ $todayAttendance->whereIn('status',['checkin','present','late'])->count() }}</div><div class="ts-lbl">Checked In</div></div>
                <div class="ts-item blue"><div class="ts-num">{{ $todayAttendance->whereIn('status',['checkout','late_checkout'])->count() }}</div><div class="ts-lbl">Checked Out</div></div>
                <div class="ts-item red"><div class="ts-num">{{ $todayAttendance->whereIn('status',['late','late_checkout'])->count() }}</div><div class="ts-lbl">Late</div></div>
            </div>
        </div>
        <div class="cal-card">
            <h3><i class="material-symbols-rounded" style="font-size:16px;">child_care</i> My Children</h3>
            @foreach($children as $child)
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:12px;border-bottom:1px solid #f1f5f9;">
                <span style="width:8px;height:8px;border-radius:50%;background:#6d28d9;"></span>
                <span style="font-weight:700;color:#1e293b;">{{ $child->name }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="cal-card">
        <div id="calendar"></div>
        <div class="legend-bar">
            <div class="legend-item"><div class="legend-dot present"></div> Present</div>
            <div class="legend-item"><div class="legend-dot checkout"></div> Checked Out</div>
            <div class="legend-item"><div class="legend-dot late"></div> Late</div>
            <div class="legend-item"><div class="legend-dot absent"></div> Absent</div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
    var cal=new FullCalendar.Calendar(document.getElementById('calendar'),{
        initialView:'dayGridMonth',
        headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth'},
        height:'auto',
        events:{
            url:'/parent/attendance/calendar-data',
            failure:function(){console.error('Failed to load calendar events');}
        },
        eventDisplay:'block',
        eventTimeFormat:{hour:'2-digit',minute:'2-digit',hour12:true},
        eventDidMount:function(info){
            info.el.style.cursor='pointer';
        },
        eventClick:function(info){
            console.log('Event clicked:', info.event.title);
            var p=info.event.extendedProps;
            var statusLabel={'present':'Present / Checked In','checkin':'Checked In','checkout':'Checked Out','late':'Late Check-in','late_checkout':'Late Check-out','absent':'Absent'};
            var s=p.status||'present';

            // Child info
            document.getElementById('modalChildName').textContent=p.child_name||'Child';
            document.getElementById('modalClassroom').textContent=p.classroom||'-';
            document.getElementById('modalAvatar').style.background=p.color||'#6d28d9';
            document.getElementById('modalAvatar').textContent=(p.child_name||'?').charAt(0).toUpperCase();

            // Status
            document.getElementById('modalStatus').textContent=statusLabel[s]||s;
            document.getElementById('modalStatus').className='modal-status '+s;

            // Date
            document.getElementById('modalDate').textContent=info.event.startStr;

            // Check-in row
            var ciStatus=p.checkin_status||'unknown';
            var ciBadge='<span style="color:#94a3b8;">—</span>';
            if(ciStatus==='on_time') ciBadge='<span style="background:#e8f5e9;color:#2e7d32;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🟢 On Time</span>';
            else if(ciStatus==='late') ciBadge='<span style="background:#fff3e0;color:#e65100;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🟡 Late</span>';
            else if(ciStatus==='absent') ciBadge='<span style="background:#fce4ec;color:#c62828;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🔴 Absent</span>';
            document.getElementById('modalCheckinStatus').innerHTML=ciBadge;
            document.getElementById('modalCheckinTime').textContent=p.checkin_time||'—';
            var ciMins=p.checkin_minutes||0;
            document.getElementById('modalCheckinMins').textContent=ciMins>0?'+'+ciMins+'m':'';
            document.getElementById('modalCheckinSched').textContent=p.schedule_in||'—';

            // Check-out row
            var coStatus=p.checkout_status||'unknown';
            var coBadge='<span style="color:#94a3b8;">—</span>';
            if(coStatus==='on_time') coBadge='<span style="background:#e8f5e9;color:#2e7d32;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🟢 On Time</span>';
            else if(coStatus==='early') coBadge='<span style="background:#fce4ec;color:#c62828;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🔴 Early</span>';
            else if(coStatus==='late') coBadge='<span style="background:#fff3e0;color:#e65100;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🟡 Late</span>';
            else if(coStatus==='not_checked_out') coBadge='<span style="background:#f3e8ff;color:#6d28d9;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;">🟣 Not Out</span>';
            document.getElementById('modalCheckoutStatus').innerHTML=coBadge;
            document.getElementById('modalCheckoutTime').textContent=p.checkout_time||'—';
            var coMins=p.checkout_minutes||0;
            document.getElementById('modalCheckoutMins').textContent=coMins>0?(coStatus==='early'?'-':'+')+coMins+'m':'';
            document.getElementById('modalCheckoutSched').textContent=p.schedule_out||'—';

            document.getElementById('attendanceModal').classList.add('show');
        }
    });
    cal.render();
});
function closeModal(){document.getElementById('attendanceModal').classList.remove('show');}
</script>

{{-- Modal --}}
<div class="modal-overlay" id="attendanceModal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()"><i class="material-symbols-rounded" style="font-size:20px;">close</i></button>
        <div class="modal-title">
            <div class="modal-avatar" id="modalAvatar" style="background:#6d28d9;">?</div>
            <div>
                <span id="modalChildName" style="display:block;">Child Name</span>
                <small id="modalClassroom" style="color:#94a3b8;font-size:11px;">—</small>
            </div>
        </div>
        <div class="modal-row">
            <span class="lbl">Date</span>
            <span class="val" id="modalDate">--</span>
        </div>
        <div class="modal-row">
            <span class="lbl">Overall Status</span>
            <span class="modal-status present" id="modalStatus">Present</span>
        </div>

        {{-- Check-in Detail --}}
        <div style="margin-top:12px;padding:12px;background:#f8fafc;border-radius:12px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">📥 Check-in</div>
            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Schedule</td>
                    <td style="padding:4px 0;text-align:right;font-weight:600;" id="modalCheckinSched">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Actual</td>
                    <td style="padding:4px 0;text-align:right;font-weight:700;" id="modalCheckinTime">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Status</td>
                    <td style="padding:4px 0;text-align:right;" id="modalCheckinStatus">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Diff</td>
                    <td style="padding:4px 0;text-align:right;font-weight:700;color:#c62828;" id="modalCheckinMins">—</td>
                </tr>
            </table>
        </div>

        {{-- Check-out Detail --}}
        <div style="margin-top:10px;padding:12px;background:#f8fafc;border-radius:12px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">📤 Check-out</div>
            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Schedule</td>
                    <td style="padding:4px 0;text-align:right;font-weight:600;" id="modalCheckoutSched">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Actual</td>
                    <td style="padding:4px 0;text-align:right;font-weight:700;" id="modalCheckoutTime">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Status</td>
                    <td style="padding:4px 0;text-align:right;" id="modalCheckoutStatus">—</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;color:#94a3b8;">Diff</td>
                    <td style="padding:4px 0;text-align:right;font-weight:700;color:#c62828;" id="modalCheckoutMins">—</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
