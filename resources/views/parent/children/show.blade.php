@extends('layouts.parent-template')

@section('content')
<style>
    .detail-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .detail-card h3 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .info-item label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #94a3b8; display: block; margin-bottom: 2px; }
    .info-item span { font-size: 14px; font-weight: 600; color: #1e293b; }
    .stat-row-sm { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 20px; }
    .stat-card-sm { background: white; border-radius: 16px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; }
    .stat-card-sm .num { font-size: 28px; font-weight: 800; }
    .stat-card-sm .lbl { font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
    .stat-card-sm.green .num { color: #2e7d32; }
    .stat-card-sm.red .num { color: #c62828; }
    .stat-card-sm.orange .num { color: #e65100; }
    @media(max-width:768px){ .info-grid{grid-template-columns:1fr;} }
</style>

<div class="stat-row-sm">
    <div class="stat-card-sm green">
        <div class="num">{{ $totalPresent }}</div>
        <div class="lbl">Days Present</div>
    </div>
    <div class="stat-card-sm red">
        <div class="num">{{ $totalAbsent }}</div>
        <div class="lbl">Days Absent</div>
    </div>
    <div class="stat-card-sm orange">
        <div class="num">{{ $totalLate }}</div>
        <div class="lbl">Times Late</div>
    </div>
</div>

<div class="detail-card">
    <h3><i class="material-symbols-rounded" style="font-size:18px;">child_care</i> {{ $child->name }}</h3>
    <div class="info-grid">
        <div class="info-item"><label>Full Name</label><span>{{ $child->name }}</span></div>
        <div class="info-item"><label>Age</label><span>{{ $child->age }}</span></div>
        <div class="info-item"><label>Date of Birth</label><span>{{ $child->dob ? \Carbon\Carbon::parse($child->dob)->format('d M Y') : 'N/A' }}</span></div>
        <div class="info-item"><label>Classroom</label><span>{{ $child->classroom->name ?? 'N/A' }}</span></div>
        <div class="info-item"><label>Dietary</label><span>{{ $child->dietary ?? 'None' }}</span></div>
        <div class="info-item"><label>Nursery Type</label><span>{{ $child->nursery_type ?? 'N/A' }}</span></div>
    </div>
</div>

<div class="detail-card">
    <h3><i class="material-symbols-rounded" style="font-size:18px;">history</i> Recent Attendance</h3>
    @if($child->attendances && $child->attendances->count() > 0)
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px 12px;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Date</th>
                    <th style="text-align:left;padding:8px 12px;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Status</th>
                    <th style="text-align:left;padding:8px 12px;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Check-in</th>
                    <th style="text-align:left;padding:8px 12px;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #f1f5f9;">Check-out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($child->attendances->take(10) as $att)
                @php
                    $sc = in_array($att->status, ['checkin','present']) ? '#2e7d32' : ($att->status == 'checkout' ? '#1565c0' : ($att->status == 'late' || $att->status == 'late_checkout' ? '#c62828' : '#e65100'));
                    $bg = in_array($att->status, ['checkin','present']) ? '#e8f5e9' : ($att->status == 'checkout' ? '#e3f2fd' : ($att->status == 'late' || $att->status == 'late_checkout' ? '#fce4ec' : '#fff3e0'));
                @endphp
                <tr>
                    <td style="padding:10px 12px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;font-weight:600;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                    <td style="padding:10px 12px;font-size:13px;border-bottom:1px solid #f1f5f9;">
                        <span style="display:inline-block;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700;background:{{ $bg }};color:{{ $sc }};">
                            {{ ucfirst(str_replace('_', ' ', $att->status)) }}
                        </span>
                    </td>
                    <td style="padding:10px 12px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;">{{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '--' }}</td>
                    <td style="padding:10px 12px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;">{{ $att->checkout_time ? \Carbon\Carbon::parse($att->checkout_time)->format('h:i A') : '--' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:30px;color:#94a3b8;">No attendance records yet.</div>
    @endif
</div>

<div style="text-align:right;">
    <a href="{{ route('parent.children.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#f1f5f9;color:#475569;border-radius:12px;font-weight:700;font-size:13px;text-decoration:none;">
        <i class="material-symbols-rounded" style="font-size:16px;">arrow_back</i> Back to Children
    </a>
</div>
@endsection
