@extends('layouts.parent-template')

@section('content')
<style>
    .card-table { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    .card-table h4 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px; }
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .att-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .att-table tbody tr { cursor: pointer; transition: .15s; }
    .att-table tbody tr:hover { background: #f8fafc; }
    .status-dot { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; }
    .status-dot.green { background: #e8f5e9; color: #2e7d32; }
    .status-dot.red { background: #fce4ec; color: #c62828; }
    .status-dot.yellow { background: #fff3e0; color: #e65100; }
    .status-dot.blue { background: #e3f2fd; color: #1565c0; }
</style>

<div class="card-table">
    <h4><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">calendar_month</i> Attendance</h4>
    @if(isset($attendance) && $attendance->count() > 0)
    <div style="overflow-x:auto;">
        <table class="att-table">
            <thead><tr><th>Child</th><th>Date</th><th>Status</th><th>Check-in</th><th>Check-out</th></tr></thead>
            <tbody>
                @foreach($attendance as $att)
                @php
                    $sc = in_array($att->status, ['checkin','present']) ? 'green' : ($att->status == 'checkout' ? 'blue' : ($att->status == 'late' || $att->status == 'late_checkout' ? 'red' : 'yellow'));
                @endphp
                <tr onclick="location.href='{{ route('parent.attendance.child', $att->child_id) }}'">
                    <td style="font-weight:700;">{{ $att->child->name ?? 'N/A' }}</td>
                    <td>{{ $att->date }}</td>
                    <td><span class="status-dot {{ $sc }}">{{ $att->status }}</span></td>
                    <td>{{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '--' }}</td>
                    <td>{{ $att->checkout_time ? \Carbon\Carbon::parse($att->checkout_time)->format('h:i A') : '--' }}</td>
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
