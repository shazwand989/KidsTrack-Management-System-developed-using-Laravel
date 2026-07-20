@extends('layouts.parent-template')

@section('content')
<style>
    .detail-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .detail-card h3 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .att-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .att-table tr:last-child td { border-bottom: none; }
    .status-dot { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; }
    .status-dot.green { background: #e8f5e9; color: #2e7d32; }
    .status-dot.red { background: #fce4ec; color: #c62828; }
    .status-dot.yellow { background: #fff3e0; color: #e65100; }
    .status-dot.blue { background: #e3f2fd; color: #1565c0; }
    .child-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .child-avatar { width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #6d28d9, #9333ea); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; }
    .child-header h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0; }
    .child-header p { margin: 2px 0 0; font-size: 12px; color: #94a3b8; }
</style>

<div class="child-header">
    <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
    <div>
        <h4>{{ $child->name }}</h4>
        <p>{{ $child->classroom->name ?? 'No Class' }} &middot; {{ $child->age }} years old</p>
    </div>
</div>

<div class="detail-card">
    <h3><i class="material-symbols-rounded" style="font-size:18px;">history</i> Attendance History</h3>
    @if($attendance->count() > 0)
    <div style="overflow-x:auto;">
        <table class="att-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Late</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendance as $att)
                @php
                    $statusClass = match($att->status) {
                        'present', 'checkin' => 'green',
                        'checkout' => 'blue',
                        'late' => 'red',
                        'late_checkout' => 'red',
                        'absent' => 'yellow',
                        default => 'yellow'
                    };
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $att->date }}</td>
                    <td>
                        <span class="status-dot {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $att->status)) }}
                        </span>
                    </td>
                    <td>{{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '--' }}</td>
                    <td>{{ $att->checkout_time ? \Carbon\Carbon::parse($att->checkout_time)->format('h:i A') : '--' }}</td>
                    <td>
                        @if(in_array($att->status, ['late', 'late_checkout']))
                            <span style="color:#c62828;font-weight:700;"><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">warning</i> Yes</span>
                        @else
                            <span style="color:#94a3b8;">No</span>
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
