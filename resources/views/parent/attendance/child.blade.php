@extends('layouts.parent-template')

@section('content')
<style>
    .detail-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .detail-card h3 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .att-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .att-table tr:last-child td { border-bottom: none; }
    .status-dot { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .status-dot.green { background: #e8f5e9; color: #2e7d32; }
    .status-dot.red { background: #fce4ec; color: #c62828; }
    .status-dot.yellow { background: #fff3e0; color: #e65100; }
    .status-dot.blue { background: #e3f2fd; color: #1565c0; }
    .child-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .child-avatar { width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #6d28d9, #9333ea); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; }
    .child-header h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0; }
    .child-header p { margin: 2px 0 0; font-size: 12px; color: #94a3b8; }

    /* Summary card */
    .summary-card { background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 16px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
    .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 500px) { .summary-grid { grid-template-columns: 1fr; } }
    .summary-box { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .summary-box h5 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin: 0 0 10px; }
    .summary-box .time { font-size: 22px; font-weight: 800; color: #1e293b; }
    .summary-box .diff { font-size: 12px; margin-top: 4px; }
    .summary-box .diff.red { color: #c62828; }
    .summary-box .diff.green { color: #2e7d32; }
    .summary-box .diff.blue { color: #1565c0; }
    .schedule-info { font-size: 11px; color: #94a3b8; margin-top: 6px; }
    .schedule-info span { font-weight: 600; color: #64748b; }
</style>

<div class="child-header">
    <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
    <div>
        <h4>{{ $child->name }}</h4>
        <p>{{ $child->classroom->name ?? 'No Class' }} &middot; {{ $child->age }} years old</p>
    </div>
</div>

<!-- Class Schedule Card -->
@php $cls = $child->classroom; @endphp
@if($cls)
<div class="detail-card" style="border-left:4px solid #6d28d9;">
    <h3>📅 Class Schedule — {{ $cls->name }}</h3>
    <div style="display:flex;gap:24px;flex-wrap:wrap;">
        <div style="flex:1;min-width:120px;background:#f0fdf4;border-radius:12px;padding:14px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#059669;margin-bottom:4px;">🟢 Class Starts</div>
            <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $cls->start_time ? \Carbon\Carbon::parse($cls->start_time)->format('h:i A') : '—' }}</div>
        </div>
        <div style="flex:1;min-width:120px;background:#f5f3ff;border-radius:12px;padding:14px;text-align:center;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#6d28d9;margin-bottom:4px;">🟣 Class Ends</div>
            <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $cls->end_time ? \Carbon\Carbon::parse($cls->end_time)->format('h:i A') : '—' }}</div>
        </div>
    </div>
</div>
@endif

<div class="detail-card">
    <h3><i class="material-symbols-rounded" style="font-size:18px;">history</i> Attendance History</h3>
    @if($attendance->count() > 0)
    <div style="overflow-x:auto;">
        <table class="att-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Summary</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendance as $att)
                @php
                    $s = $summaries[$att->id] ?? null;
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                    <td>
                        @if($s)
                            {!! $s['summary'] !!}
                        @else
                            <span class="status-dot yellow">—</span>
                        @endif
                    </td>
                    <td>
                        @if($s)
                            <div>
                                <strong>{{ $s['checkin']['time'] ?? '—' }}</strong>
                                <br>
                                <span class="status-dot {{ $s['checkin']['status_class'] }}">
                                    {{ $s['checkin']['status_label'] }}
                                </span>
                                @if($s['checkin']['minutes_diff'] > 0)
                                    <div class="diff red" style="margin-top:2px;">
                                        +{{ \App\Services\AttendanceSummaryService::formatDuration($s['checkin']['minutes_diff']) }} late
                                    </div>
                                @endif
                            </div>
                        @else
                            {{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($s)
                            <div>
                                <strong>{{ $s['checkout']['time'] ?? '—' }}</strong>
                                <br>
                                <span class="status-dot {{ $s['checkout']['status_class'] }}">
                                    {{ $s['checkout']['status_label'] }}
                                </span>
                                @if($s['checkout']['minutes_diff'] > 0 && $s['checkout']['status'] === 'early')
                                    <div class="diff blue" style="margin-top:2px;">
                                        -{{ \App\Services\AttendanceSummaryService::formatDuration($s['checkout']['minutes_diff']) }} early
                                    </div>
                                @elseif($s['checkout']['minutes_diff'] > 0 && $s['checkout']['status'] === 'late')
                                    <div class="diff red" style="margin-top:2px;">
                                        +{{ \App\Services\AttendanceSummaryService::formatDuration($s['checkout']['minutes_diff']) }} late
                                    </div>
                                @endif
                            </div>
                        @else
                            {{ $att->checkout_time ? \Carbon\Carbon::parse($att->checkout_time)->format('h:i A') : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($s)
                            <div class="schedule-info">
                                <span>🕐 In:</span> {{ $s['schedule']['morning_end'] }}<br>
                                <span>🕐 Out:</span> {{ $s['schedule']['class_end'] ?? $s['schedule']['evening_end'] }}
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">
        {{ $attendance->links() }}
    </div>
    @else
    <div style="text-align:center;padding:40px;color:#94a3b8;">
        <i class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">event_busy</i>
        No attendance records found.
    </div>
    @endif
</div>

<div style="text-align:right;">
    <a href="{{ route('parent.attendance.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#f1f5f9;color:#475569;border-radius:12px;font-weight:700;font-size:13px;text-decoration:none;">
        <i class="material-symbols-rounded" style="font-size:16px;">arrow_back</i> Back to Attendance
    </a>
</div>
@endsection
