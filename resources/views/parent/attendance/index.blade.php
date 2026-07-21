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
                <div class="class-name">🏫 {{ $cls->name ?? 'No Class' }}</div>
                <div class="times">
                    <span class="in">🟢 {{ $cls->start_time ? \Carbon\Carbon::parse($cls->start_time)->format('h:i A') : '—' }}</span>
                    <span class="out">🟣 {{ $cls->end_time ? \Carbon\Carbon::parse($cls->end_time)->format('h:i A') : '—' }}</span>
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
                <tr onclick="location.href='{{ route('parent.attendance.child', \App\Helper\KioskHelper::hashId($att->child_id)) }}'">
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
@endsection
