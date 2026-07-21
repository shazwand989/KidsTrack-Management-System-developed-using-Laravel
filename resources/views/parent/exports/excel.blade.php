<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="utf-8">
    @verbatim<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
    <x:Name>Loving Guardians</x:Name><x:WorksheetOptions>
    <x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>
    </x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->@endverbatim
    <style>
        @page { margin: 0.4in; }
        body { font-family: Calibri, 'Segoe UI', sans-serif; }

        .header-table { width: 100%; margin-bottom: 16px; }
        .header-table td { padding: 4px 0; }
        .title { font-size: 18pt; font-weight: bold; color: #FF6B6B; }
        .subtitle { font-size: 10pt; color: #888888; }

        .stat-row { margin-bottom: 14px; }
        .stat-card {
            display: inline-block;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 18px;
            margin-right: 10px;
            margin-bottom: 6px;
            text-align: center;
        }
        .stat-num { font-size: 16pt; font-weight: bold; color: #1e293b; }
        .stat-label { font-size: 8pt; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.data thead th {
            background: #FFF5F2;
            color: #B45309;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 10px 8px;
            border: 1px solid #FED7AA;
            text-align: left;
        }
        table.data tbody td {
            padding: 8px;
            border: 1px solid #f1f5f9;
            font-size: 9pt;
            color: #334155;
            vertical-align: top;
        }
        table.data tbody tr:nth-child(even) td { background: #FFFCFA; }
        table.data .num { text-align: center; color: #94a3b8; font-weight: bold; width: 36px; }
        table.data .parent-name { font-weight: bold; color: #1e293b; font-size: 10pt; }
        table.data .muted { color: #94a3b8; font-size: 8pt; }
        table.data .badge-yes { background: #DCFCE7; color: #16A34A; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        table.data .badge-no  { background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        table.data .child-tag { background: #f1f5f9; padding: 1px 6px; border-radius: 4px; font-size: 8pt; margin: 1px; display: inline-block; }

        .footer { margin-top: 18px; font-size: 8pt; color: #cbd5e1; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td><span class="title">🛡️ Loving Guardians — SAFECARE</span></td>
            <td align="right" class="subtitle">Exported: {{ now()->format('d M Y, h:i A') }}</td>
        </tr>
        <tr><td colspan="2" class="subtitle">{{ $families->count() }} families · {{ $families->sum('childCount') }} children</td></tr>
    </table>

    {{-- Stat Cards --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-num">{{ $families->count() }}</div>
            <div class="stat-label">Families</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $families->sum('childCount') }}</div>
            <div class="stat-label">Children</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $families->filter(fn($f) => $f['main']->verified)->count() }}</div>
            <div class="stat-label">Verified</div>
        </div>
        <div class="stat-card">
            <div class="stat-num">{{ $families->filter(fn($f) => $f['guardian'])->count() }}</div>
            <div class="stat-label">With Guardian</div>
        </div>
    </div>

    {{-- Data Table --}}
    <table class="data">
        <thead>
            <tr>
                <th>#</th>
                <th>Main Parent</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Second Parent</th>
                <th>Guardian</th>
                <th>Children</th>
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
                $kids   = $family['children'];
            @endphp
            <tr>
                <td class="num">{{ $i++ }}</td>
                <td>
                    <span class="parent-name">{{ $main->name }}</span><br>
                    <span class="muted">{{ ucfirst(str_replace('parent', 'Parent ', $main->role)) }}</span>
                </td>
                <td>{{ $main->phone_number ?? '-' }}</td>
                <td>{{ $main->email ?? '-' }}</td>
                <td>
                    @if($second)
                        <span class="parent-name">{{ $second->name }}</span><br>
                        <span class="muted">{{ $second->phone_number ?? '-' }}</span>
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td>
                    @if($guard)
                        <span class="parent-name">{{ $guard->name }}</span><br>
                        <span class="muted">{{ $guard->phone_number ?? '-' }}</span>
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td>
                    @if($kids->count())
                        @foreach($kids as $child)
                            <span class="child-tag">{{ $child->name }}</span>
                        @endforeach
                    @else
                        <span class="muted">—</span>
                    @endif
                </td>
                <td>
                    @if($main->verified)
                        <span class="badge-yes">✓ Yes</span>
                    @else
                        <span class="badge-no">✗ No</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">SAFECARE KidsTrack Management · Generated {{ now()->toDateTimeString() }}</div>

</body>
</html>
