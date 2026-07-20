@extends('layouts.parent-template')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #6d28d9, #9333ea);
        border-radius: 24px; padding: 24px 28px; color: white;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 16px; margin-bottom: 24px;
    }
    .welcome-card h2 { font-size: 22px; font-weight: 800; margin: 0; }
    .welcome-card p { opacity: 0.85; margin: 4px 0 0; font-size: 13px; }
    .stat-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: white; border-radius: 18px; padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.purple { background: #ede9fe; color: #6d28d9; }
    .stat-icon.green { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.blue { background: #e3f2fd; color: #1565c0; }
    .stat-num { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1; }
    .stat-lbl { font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
    
    .card-table { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    .card-table h4 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px; }
    
    .child-table { width: 100%; border-collapse: collapse; }
    .child-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .child-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .child-table tr:last-child td { border-bottom: none; }
    
    .child-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #6d28d9, #9333ea);
        color: white; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px; flex-shrink: 0;
    }
    .status-dot { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; }
    .status-dot.green { background: #e8f5e9; color: #2e7d32; }
    .status-dot.yellow { background: #fff3e0; color: #e65100; }
    .status-dot.red { background: #fce4ec; color: #c62828; }
    
    .qr-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: linear-gradient(135deg, #f59e0b, #fb8c00);
        color: white; border-radius: 14px; font-weight: 700; font-size: 13px;
        text-decoration: none; box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        transition: .2s;
    }
    .qr-btn:hover { color: white; transform: translateY(-1px); }
    
    @media (max-width: 768px) { .stat-row { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 480px) { .stat-row { grid-template-columns: 1fr; } }
</style>

{{-- Welcome --}}
<div class="welcome-card">
    <div>
        <h2>Welcome back, {{ $parent->name ?? Auth::user()->name }}!</h2>
        <p>{{ date('l, d F Y') }} · {{ $parent->phone_number ?? '' }}</p>
    </div>
    <a href="{{ route('kiosk.index') }}" class="qr-btn" target="_blank">
        <i class="material-symbols-rounded" style="font-size:20px;">qr_code_scanner</i> Scan QR Code
    </a>
</div>

{{-- Stats --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="material-symbols-rounded">child_care</i></div>
        <div><div class="stat-num">{{ $totalChildren }}</div><div class="stat-lbl">Children</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="material-symbols-rounded">check_circle</i></div>
        <div><div class="stat-num">{{ $attendanceToday }}</div><div class="stat-lbl">Checked In Today</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="material-symbols-rounded">receipt_long</i></div>
        <div><div class="stat-num">RM0.00</div><div class="stat-lbl">Invoice</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="material-symbols-rounded">notifications</i></div>
        <div><div class="stat-num">0</div><div class="stat-lbl">Notifications</div></div>
    </div>
</div>

{{-- Children Table --}}
<div class="card-table">
    <h4><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">child_care</i> My Children</h4>
    @if($children->count() > 0)
    <div style="overflow-x:auto;">
        <table class="child-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Age</th><th>Class</th><th>Status Today</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($children as $i => $child)
                <tr style="cursor:pointer;" onclick="location.href='{{ route('parent.children.show', $child->id) }}'">
                    <td style="color:#94a3b8;font-weight:700;">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
                            <span style="font-weight:700;color:#1e293b;">{{ $child->name }}</span>
                        </div>
                    </td>
                    <td>{{ $child->age }}</td>
                    <td>{{ $child->classroom->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $cls = $child->status_class ?? 'pending';
                            $txt = $child->status_today ?? 'Pending';
                        @endphp
                        <span class="status-dot {{ $cls == 'checkin' ? 'green' : ($cls == 'checkout' ? 'red' : 'yellow') }}">
                            {{ $txt }}
                        </span>
                    </td>
                    <td><a href="{{ route('parent.children.show', $child->id) }}" onclick="event.stopPropagation();" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:#ede9fe;color:#6d28d9;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;"><i class="material-symbols-rounded" style="font-size:14px;">visibility</i> View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;color:#94a3b8;">
        <i class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:8px;">child_care</i>
        No children registered yet.
    </div>
    @endif
</div>
@endsection
