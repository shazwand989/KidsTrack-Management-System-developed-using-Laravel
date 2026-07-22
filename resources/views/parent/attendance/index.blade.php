@extends('layouts.parent-template')

@section('content')
<style>
    .card-table { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .card-table h4 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px; }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .att-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .att-table tbody tr { cursor: pointer; transition: .15s; }
    .att-table tbody tr:hover { background: #f8fafc; }
    .status-dot { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .status-dot.green { background: #e8f5e9; color: #2e7d32; }
    .status-dot.red { background: #fce4ec; color: #c62828; }
    .status-dot.yellow { background: #fff3e0; color: #e65100; }
    .status-dot.blue { background: #e3f2fd; color: #1565c0; }

    .schedule-summary { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; margin-bottom: 20px; }
    .schedule-card { background: white; border-radius: 16px; padding: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
    .schedule-avatar { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 18px; flex-shrink: 0; }
    .schedule-info { flex: 1; min-width: 0; }
    .schedule-info .child-name { font-weight: 700; font-size: 14px; color: #1e293b; }
    .schedule-info .class-name { font-size: 12px; color: #64748b; }
    .schedule-info .times { font-size: 12px; color: #475569; margin-top: 4px; display: flex; gap: 12px; }
    .schedule-info .times span { display: inline-flex; align-items: center; gap: 3px; }
    .schedule-info .times .in { color: #059669; }
    .schedule-info .times .out { color: #6d28d9; }
</style>

<!-- ============================================ -->
<!-- CLASS SCHEDULE SUMMARY CARD                    -->
<!-- ============================================ -->
@if(isset($children) && $children->count() > 0)
<div class="schedule-summary">
    @foreach($children as $child)
        @php $cls = $child->classroom; @endphp
        <div class="schedule-card">
            <div class="schedule-avatar" style="background:linear-gradient(135deg, #6d28d9, #9333ea);">
                {{ strtoupper(substr($child->name, 0, 1)) }}
            </div>
            <div class="schedule-info">
                <div class="child-name">{{ $child->name }}</div>
                <div class="class-name"><i class="fas fa-school"></i> {{ $cls->name ?? 'No Class' }}</div>
                <div class="times">
                    <span class="in"> {{ $cls->start_time ? \Carbon\Carbon::parse($cls->start_time)->format('h:i A') : '—' }}</span>
                    <span class="out"> {{ $cls->end_time ? \Carbon\Carbon::parse($cls->end_time)->format('h:i A') : '—' }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<div class="card-table">
    <h4><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">calendar_month</i> Attendance History</h4>
    @if(isset($attendance) && $attendance->count() > 0)
    <div style="overflow-x:auto;">
        <table class="att-table">
            <thead><tr><th>Child</th><th>Class</th><th>Date</th><th>Summary</th><th>Check-in</th><th>Check-out</th></tr></thead>
            <tbody>
                @foreach($attendance as $att)
                @php $s = $summaries[$att->id] ?? null; @endphp
                <tr class="att-row" style="cursor:pointer;"
                    data-name="{{ $att->child->name ?? 'N/A' }}"
                    data-classroom="{{ $att->child->classroom->name ?? '-' }}"
                    data-date="{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}"
                    data-summary="{{ $s ? strip_tags($s['summary']) : '—' }}"
                    data-ci-time="{{ $s['checkin']['time'] ?? '—' }}"
                    data-ci-status="{{ $s['checkin']['status'] ?? '—' }}"
                    data-ci-label="{{ $s['checkin']['status_label'] ?? '—' }}"
                    data-ci-mins="{{ $s['checkin']['minutes_diff'] ?? 0 }}"
                    data-ci-sched="{{ $s['schedule']['morning_end'] ?? '—' }}"
                    data-co-time="{{ $s['checkout']['time'] ?? '—' }}"
                    data-co-status="{{ $s['checkout']['status'] ?? '—' }}"
                    data-co-label="{{ $s['checkout']['status_label'] ?? '—' }}"
                    data-co-mins="{{ $s['checkout']['minutes_diff'] ?? 0 }}"
                    data-co-sched="{{ $s['schedule']['class_end'] ?? $s['schedule']['evening_end'] ?? '—' }}"
                    data-link="{{ route('parent.attendance.child', hash_id($att->child_id)) }}"
                    onclick="showDetailModal(this)">
                    <td style="font-weight:700;">{{ $att->child->name ?? 'N/A' }}</td>
                    <td><small style="color:#64748b;">{{ $att->child->classroom->name ?? '—' }}</small></td>
                    <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                    <td>
                        @if($s)
                            <small>{!! $s['summary'] !!}</small>
                        @else
                            <span class="status-dot yellow">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s)
                            <span class="status-dot {{ $s['checkin']['status_class'] }}">{{ $s['checkin']['status_label'] }}</span>
                            @if($s['checkin']['time'])
                                <small style="display:block;color:#94a3b8;margin-top:2px;">{{ $s['checkin']['time'] }}</small>
                            @endif
                        @else
                            {{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($s)
                            <span class="status-dot {{ $s['checkout']['status_class'] }}">{{ $s['checkout']['status_label'] }}</span>
                            @if($s['checkout']['time'])
                                <small style="display:block;color:#94a3b8;margin-top:2px;">{{ $s['checkout']['time'] }}</small>
                            @endif
                        @else
                            {{ $att->checkout_time ? \Carbon\Carbon::parse($att->checkout_time)->format('h:i A') : '—' }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;color:#94a3b8;">No attendance records.</div>
    @endif
</div>

{{-- Detail Modal --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)closeDetailModal()">
    <div class="modal-box" style="background:white;border-radius:20px;padding:28px;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;">
        <button class="modal-close" onclick="closeDetailModal()" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">✕</button>
        <div style="font-size:18px;font-weight:800;color:#1e293b;margin-bottom:4px;" id="mdlName"></div>
        <div style="font-size:12px;color:#94a3b8;margin-bottom:16px;" id="mdlClassDate"></div>

        {{-- Check-in --}}
        <div style="padding:12px;background:#f8fafc;border-radius:12px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;"><i class="fas fa-download"></i> Check-in</div>
            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr><td style="padding:3px 0;color:#94a3b8;">Schedule</td><td style="padding:3px 0;text-align:right;font-weight:600;" id="mdlCiSched">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Actual</td><td style="padding:3px 0;text-align:right;font-weight:700;" id="mdlCiTime">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Status</td><td style="padding:3px 0;text-align:right;" id="mdlCiStatus">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Diff</td><td style="padding:3px 0;text-align:right;font-weight:700;" id="mdlCiMins">—</td></tr>
            </table>
        </div>

        {{-- Check-out --}}
        <div style="padding:12px;background:#f8fafc;border-radius:12px;margin-bottom:12px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;"><i class="fas fa-upload"></i> Check-out</div>
            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr><td style="padding:3px 0;color:#94a3b8;">Schedule</td><td style="padding:3px 0;text-align:right;font-weight:600;" id="mdlCoSched">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Actual</td><td style="padding:3px 0;text-align:right;font-weight:700;" id="mdlCoTime">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Status</td><td style="padding:3px 0;text-align:right;" id="mdlCoStatus">—</td></tr>
                <tr><td style="padding:3px 0;color:#94a3b8;">Diff</td><td style="padding:3px 0;text-align:right;font-weight:700;" id="mdlCoMins">—</td></tr>
            </table>
        </div>

        <a id="mdlLink" href="#" style="display:block;text-align:center;padding:8px;background:#ede9fe;color:#6d28d9;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;"><i class="fas fa-clipboard-list"></i> View Full History</a>
    </div>
</div>

<style>
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:.2s;}
.modal-overlay.show{opacity:1;visibility:visible;}
.modal-overlay.show .modal-box{transform:translateY(0);}
.modal-box{transform:translateY(20px);transition:.3s;}
</style>

<script>
function showDetailModal(row){
    var d=row.dataset;
    document.getElementById('mdlName').textContent=d.name;
    document.getElementById('mdlClassDate').textContent='<i class="fas fa-school"></i> '+d.classroom+' · <i class="fas fa-calendar-alt"></i> '+d.date;
    document.getElementById('mdlLink').href=d.link;

    // Check-in
    document.getElementById('mdlCiTime').textContent=d.ciTime;
    document.getElementById('mdlCiSched').textContent=d.ciSched;
    var ciBadge=statusBadge(d.ciStatus,d.ciLabel);
    document.getElementById('mdlCiStatus').innerHTML=ciBadge;
    document.getElementById('mdlCiMins').textContent=+d.ciMins>0?'+'+d.ciMins+'m':'';
    document.getElementById('mdlCiMins').style.color='#c62828';

    // Check-out
    document.getElementById('mdlCoTime').textContent=d.coTime;
    document.getElementById('mdlCoSched').textContent=d.coSched;
    var coBadge=statusBadge(d.coStatus,d.coLabel);
    document.getElementById('mdlCoStatus').innerHTML=coBadge;
    var coM=+d.coMins;
    document.getElementById('mdlCoMins').textContent=coM>0?(d.coStatus==='early'?'-':'+')+coM+'m':'';
    document.getElementById('mdlCoMins').style.color=d.coStatus==='early'?'#1565c0':'#c62828';

    document.getElementById('detailModal').classList.add('show');
}

function statusBadge(status,label){
    if(status==='on_time') return '<span style="background:#e8f5e9;color:#2e7d32;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> '+label+'</span>';
    if(status==='late') return '<span style="background:#fff3e0;color:#e65100;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> '+label+'</span>';
    if(status==='early') return '<span style="background:#fce4ec;color:#c62828;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> '+label+'</span>';
    if(status==='absent') return '<span style="background:#fce4ec;color:#c62828;padding:3px 8px;border-radius:8px;font-size:11px;font-weight:700;"> '+label+'</span>';
    return '<span style="color:#94a3b8;">'+label+'</span>';
}

function closeDetailModal(){document.getElementById('detailModal').classList.remove('show');}
</script>
@endsection
