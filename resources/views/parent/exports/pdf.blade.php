<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loving Guardians</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 20px; color: #FF6B6B; margin: 0 0 4px; }
        .header p { font-size: 11px; color: #94a3b8; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background: #FFF5F2; }
        th { padding: 8px 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; color: #94a3b8; border-bottom: 2px solid #FFD4C8; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #f1f5f9; font-size: 9px; }
        tr:nth-child(even) { background: #FFFAF9; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 8px; font-size: 7px; font-weight: 700; }
        .badge-yes { background: #f0fdf4; color: #16a34a; }
        .badge-no { background: #fef2f2; color: #dc2626; }
        .footer { text-align: center; margin-top: 16px; font-size: 8px; color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛡️ Loving Guardians — SAFECARE</h1>
        <p>Exported on {{ now()->format('d M Y, h:i A') }} · {{ $families->count() }} families</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Main Parent</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Second Parent</th>
                <th>Guardian</th>
                <th>Children</th>
                <th>Kids</th>
                <th>Verified</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($families as $family)
            @php
                $main   = $family['main'];
                $second = $family['second'];
                $guard  = $family['guardian'];
            @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td><strong>{{ $main->name }}</strong></td>
                <td>{{ $main->phone_number ?? '-' }}</td>
                <td>{{ $main->email ?? '-' }}</td>
                <td>{{ $second ? $second->name : '-' }}</td>
                <td>{{ $guard ? $guard->name : '-' }}</td>
                <td>{{ $family['childCount'] }}</td>
                <td>{{ $family['children']->pluck('name')->implode(', ') ?: '-' }}</td>
                <td>
                    @if($main->verified)
                        <span class="badge badge-yes">✓ Yes</span>
                    @else
                        <span class="badge badge-no">✗ No</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SAFECARE KidsTrack · Generated {{ now()->toDateTimeString() }}
    </div>
</body>
</html>
