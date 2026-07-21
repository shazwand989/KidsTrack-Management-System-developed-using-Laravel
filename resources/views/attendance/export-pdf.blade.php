<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; padding: 24px; color: #1e293b; }
        h1 { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 2px; }
        .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 14px; }
        .stats { text-align: center; margin-bottom: 14px; padding: 8px; background: #f8fafc; }
        .stats span { margin: 0 12px; font-weight: bold; }
        .stats .green { color: #16a34a; } .stats .blue { color: #2563eb; }
        .stats .orange { color: #d97706; } .stats .red { color: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #f1f5f9; font-size: 8px; text-transform: uppercase; font-weight: bold; color: #475569; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        td { font-size: 9px; border: 1px solid #e2e8f0; padding: 5px 8px; }
        tr:nth-child(even) td { background: #fafafa; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: bold; }
        .b-present { background: #dcfce7; color: #16a34a; }
        .b-checkout { background: #dbeafe; color: #2563eb; }
        .b-late { background: #fef3c7; color: #b45309; }
        .b-absent { background: #fee2e2; color: #dc2626; }
        .footer { text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <h1>Attendance Report</h1>
    <p class="subtitle">
        Generated: {{ $generated_at }} &bull; By: {{ $generated_by }} &bull; {{ $total }} records
    </p>

    <div class="stats">
        <span class="green">✓ Check-ins: {{ $totalCheckin }}</span>
        <span class="blue">↩ Check-outs: {{ $totalCheckout }}</span>
        <span class="orange">⚠ Late: {{ $totalLate }}</span>
        <span class="red">✗ Absent: {{ $totalAbsent }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Child</th><th>Class</th><th>Date</th><th>Status</th>
                <th>Check-in</th><th>Check-out</th><th>Drop Off</th><th>Pickup</th>
            </tr>
        </thead>
        <tbody>
            @php
                $badgeMap = [
                    'checkin'=>['b-present','Checked In'],'present'=>['b-present','Present'],
                    'checkout'=>['b-checkout','Checked Out'],'late'=>['b-late','Late'],
                    'late_checkout'=>['b-late','Late Out'],'absent'=>['b-absent','Absent'],
                ];
            @endphp
            @foreach($attendances as $i => $a)
            @php [$bc, $bt] = $badgeMap[$a->status] ?? ['b-absent','Unknown']; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $a->child->name ?? '-' }}</td>
                <td>{{ $a->child->classroom->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($a->date)->format('d/m/Y') }}</td>
                <td><span class="badge {{ $bc }}">{{ $bt }}</span></td>
                <td>{{ $a->checkin_time ? \Carbon\Carbon::parse($a->checkin_time)->format('H:i') : '-' }}</td>
                <td>{{ $a->checkout_time ? \Carbon\Carbon::parse($a->checkout_time)->format('H:i') : '-' }}</td>
                <td>{{ $a->drop_off_by ?? '-' }}</td>
                <td>{{ $a->pickup_by ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        KidsTrack SAFECARE &mdash; Auto-generated {{ now()->toDateTimeString() }}
    </div>

</body>
</html>
