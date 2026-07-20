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
    <div class="cal-card"><div id="calendar"></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
    var cal=new FullCalendar.Calendar(document.getElementById('calendar'),{
        initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth'},height:'auto',
        events:function(info,successCallback){
            var cids=@json($childIds);
            fetch('/attendance-calendar-data?month='+(info.start.getMonth()+1)+'&year='+info.start.getFullYear())
            .then(function(r){return r.json();}).then(function(d){
                var records=d.data||[],result=[];
                for(var i=0;i<records.length;i++){
                    var a=records[i];
                    if(cids.indexOf(a.child_id)===-1)continue;
                    var nm=(a.child&&a.child.name)?a.child.name:'Unknown';
                    var st=a.status||'',cl='#43a047';
                    if(st==='late'||st==='late_checkout')cl='#e53935';
                    else if(st==='checkout')cl='#1e88e5';
                    else if(st==='absent')cl='#fb8c00';
                    var dObj=new Date(a.date);
                    var ds=dObj.getFullYear()+'-'+String(dObj.getMonth()+1).padStart(2,'0')+'-'+String(dObj.getDate()).padStart(2,'0');
                    result.push({title:nm,start:ds,backgroundColor:cl,textColor:'#fff'});
                }
                successCallback(result);
            });
        }
    });cal.render();
});
</script>
@endsection
