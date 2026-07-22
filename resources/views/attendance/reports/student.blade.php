@extends('layouts.template')

@section('title', 'Student Attendance Report')
@section('page-title', 'Attendance Reports')

@php $queryStr = http_build_query(request()->except('page')); @endphp

@section('content')
<style>
    .rp-wrap{width:100%}
    .rp-card{background:white;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f1f5f9;padding:20px;margin-bottom:16px}
    .rp-card h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 12px}
    .summary-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:16px}
    .sum-box{background:#f8fafc;border-radius:12px;padding:14px;text-align:center;border:1px solid #e2e8f0}
    .sum-box .num{font-size:22px;font-weight:800;color:#1e293b}
    .sum-box .lbl{font-size:11px;color:#64748b;font-weight:600;margin-top:2px}
    .sum-box.green{border-left:4px solid #16a34a}.sum-box.green .num{color:#16a34a}
    .sum-box.red{border-left:4px solid #dc2626}.sum-box.red .num{color:#dc2626}
    .sum-box.amber{border-left:4px solid #d97706}.sum-box.amber .num{color:#d97706}
    .sum-box.blue{border-left:4px solid #2563eb}.sum-box.blue .num{color:#2563eb}

    .filter-bar{display:flex;gap:8px;flex-wrap:wrap;align-items:end;margin-bottom:14px}
    .filter-bar select,.filter-bar input,.filter-bar button{border:1px solid #e2e8f0;border-radius:10px;padding:8px 12px;font-size:13px;font-weight:600;color:#334155}
    .filter-bar button{background:#FF6B6B;color:white;border:none;cursor:pointer;font-weight:700}
    .rp-table{width:100%;border-collapse:collapse;font-size:12px}
    .rp-table th{background:#FFF5F2;padding:10px 8px;font-size:10px;font-weight:800;text-transform:uppercase;color:#92400E;border-bottom:2px solid #FED7AA;text-align:left;white-space:nowrap}
    .rp-table td{padding:8px;border-bottom:1px solid #f1f5f9}
    .rp-table tr:hover{background:#FFFAF9}
    .badge{display:inline-block;padding:3px 8px;border-radius:8px;font-size:10px;font-weight:700}
    .badge.green{background:#e8f5e9;color:#2e7d32}
    .badge.red{background:#fce4ec;color:#c62828}
    .badge.amber{background:#fff3e0;color:#e65100}
    .badge.blue{background:#e3f2fd;color:#1565c0}
    .btn-export{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;margin-left:6px}
    .btn-export.csv{background:#f1f5f9;color:#475569}
</style>

<div class="rp-wrap">
    {{-- Student Info --}}
    <div class="rp-card">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:space-between">
            <div>
                <h4 style="margin:0"><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">person</i> {{ $child->name }}</h4>
                <small style="color:#64748b">🏫 {{ $child->classroom->name ?? '-' }} · {{ $child->age }}y</small>
            </div>
            <a href="{{ route('reports.class') }}?{{ $queryStr }}" style="font-size:13px;color:#6d28d9;font-weight:700;text-decoration:none;">← Back to Class Report</a>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary-row">
        <div class="sum-box blue"><div class="num">{{ $summary['total_days'] }}</div><div class="lbl">School Days</div></div>
        <div class="sum-box green"><div class="num">{{ $summary['present'] }}</div><div class="lbl">Present</div></div>
        <div class="sum-box red"><div class="num">{{ $summary['absent'] }}</div><div class="lbl">Absent</div></div>
        <div class="sum-box amber"><div class="num">{{ $summary['late'] }}</div><div class="lbl">Late Check-in</div></div>
        <div class="sum-box blue"><div class="num">{{ $summary['early'] }}</div><div class="lbl">Early Check-out</div></div>
        <div class="sum-box green"><div class="num">{{ $summary['percentage'] }}%</div><div class="lbl">Attendance</div></div>
    </div>

    {{-- Filters --}}
    <div class="rp-card">
        <h4><i class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">filter_alt</i> Filters</h4>
        <form method="GET" class="filter-bar">
            <select name="month"><option value="">Month</option>
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ ($filters['month']??'')==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
            <select name="year"><option value="">Year</option>
                @for($y=now()->year;$y>=now()->year-3;$y--)
                    <option value="{{ $y }}" {{ ($filters['year']??'')==$y?'selected':'' }}>{{ $y }}</option>
                @endfor
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from']??'' }}" title="From">
            <input type="date" name="date_to" value="{{ $filters['date_to']??'' }}" title="To">
            <button type="submit">Apply</button>
            <a href="?" class="btn-export csv">Reset</a>
            <a href="{{ route('reports.student.export', hash_id($child->id)) }}?{{ $queryStr }}" class="btn-export csv"><i class="material-symbols-rounded" style="font-size:14px;">download</i> CSV</a>
        </form>
    </div>

    {{-- Detail Table --}}
    <div class="rp-card">
        <h4><i class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">list_alt</i> Detailed Records</h4>
        <div style="overflow-x:auto">
            <table class="rp-table">
                <thead><tr>
                    <th>Date</th><th>Check-in</th><th>CI Status</th><th>Late</th><th>Check-out</th><th>CO Status</th><th>Early</th><th>Schedule In</th><th>Schedule Out</th><th>Note</th>
                </tr></thead>
                <tbody>
                    @forelse($rows as $r)
                    <tr>
                        <td style="font-weight:700">{{ $r['date'] }}</td>
                        <td>{{ $r['checkin_time'] }}</td>
                        <td><span class="badge {{ $r['ci_status_class'] }}">{{ $r['ci_status'] }}</span></td>
                        <td>{{ $r['ci_mins'] > 0 ? '+'.$r['ci_mins'].'m' : '—' }}</td>
                        <td>{{ $r['checkout_time'] }}</td>
                        <td><span class="badge {{ $r['co_status_class'] }}">{{ $r['co_status'] }}</span></td>
                        <td>{{ $r['co_status'] === 'Early' && $r['co_mins'] > 0 ? $r['co_mins'].'m' : '—' }}</td>
                        <td>{{ $r['schedule_in'] }}</td>
                        <td>{{ $r['schedule_out'] }}</td>
                        <td><small>{{ $r['note'] }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:40px;color:#94a3b8">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
