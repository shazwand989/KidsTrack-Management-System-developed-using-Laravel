@extends('layouts.template')

@section('title', 'Class Attendance Report')
@section('page-title', 'Attendance Reports')

@php $queryStr = http_build_query(request()->except('page')); @endphp

@section('content')
<style>
    .rp-wrap{width:100%}
    .rp-card{background:white;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f1f5f9;padding:20px;margin-bottom:16px}
    .rp-card h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 12px}

    .summary-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:16px}
    .sum-box{background:#f8fafc;border-radius:12px;padding:14px;text-align:center;border:1px solid #e2e8f0}
    .sum-box .num{font-size:24px;font-weight:800;color:#1e293b}
    .sum-box .lbl{font-size:11px;color:#64748b;font-weight:600;margin-top:2px}
    .sum-box.green{border-left:4px solid #16a34a}.sum-box.green .num{color:#16a34a}
    .sum-box.red{border-left:4px solid #dc2626}.sum-box.red .num{color:#dc2626}
    .sum-box.amber{border-left:4px solid #d97706}.sum-box.amber .num{color:#d97706}
    .sum-box.blue{border-left:4px solid #2563eb}.sum-box.blue .num{color:#2563eb}
    .sum-box.purple{border-left:4px solid #6d28d9}.sum-box.purple .num{color:#6d28d9}

    .filter-bar{display:flex;gap:8px;flex-wrap:wrap;align-items:end;margin-bottom:14px}
    .filter-bar select,.filter-bar input,.filter-bar button{border:1px solid #e2e8f0;border-radius:10px;padding:8px 12px;font-size:13px;font-weight:600;color:#334155}
    .filter-bar button{background:#FF6B6B;color:white;border:none;cursor:pointer;font-weight:700}
    .filter-bar button:hover{opacity:.9}
    .rp-table{width:100%;border-collapse:collapse}
    .rp-table th{background:#FFF5F2;padding:10px 12px;font-size:10px;font-weight:800;text-transform:uppercase;color:#92400E;border-bottom:2px solid #FED7AA;text-align:left;white-space:nowrap}
    .rp-table td{padding:10px 12px;font-size:13px;border-bottom:1px solid #f1f5f9}
    .rp-table tr:hover{background:#FFFAF9}
    .status-badge{display:inline-block;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:700}
    .status-badge.green{background:#e8f5e9;color:#2e7d32}
    .status-badge.red{background:#fce4ec;color:#c62828}
    .status-badge.amber{background:#fff3e0;color:#e65100}
    .btn-export{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;margin-left:6px}
    .btn-export.csv{background:#f1f5f9;color:#475569}
    .btn-export:hover{opacity:.85}
</style>

<div class="rp-wrap">
    {{-- Summary Cards --}}
    <div class="summary-row">
        <div class="sum-box blue"><div class="num">{{ $summary['total_students'] ?? 0 }}</div><div class="lbl">Total Students</div></div>
        <div class="sum-box green"><div class="num">{{ $summary['total_present'] ?? 0 }}</div><div class="lbl">Total Present</div></div>
        <div class="sum-box red"><div class="num">{{ $summary['total_absent'] ?? 0 }}</div><div class="lbl">Total Absent</div></div>
        <div class="sum-box amber"><div class="num">{{ $summary['total_late'] ?? 0 }}</div><div class="lbl">Late Check-ins</div></div>
        <div class="sum-box purple"><div class="num">{{ $summary['total_early'] ?? 0 }}</div><div class="lbl">Early Check-outs</div></div>
        <div class="sum-box green"><div class="num">{{ $summary['overall_pct'] ?? 0 }}%</div><div class="lbl">Overall Attendance</div></div>
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
            <select name="class"><option value="">All Classes</option>
                @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ ($filters['class']??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" placeholder="Search student..." value="{{ $filters['search']??'' }}">
            <input type="date" name="date_from" value="{{ $filters['date_from']??'' }}" title="From">
            <input type="date" name="date_to" value="{{ $filters['date_to']??'' }}" title="To">
            <button type="submit">Apply</button>
            <a href="?" class="btn-export csv">Reset</a>
            <a href="{{ route('reports.class.export') }}?{{ $queryStr }}" class="btn-export csv"><i class="material-symbols-rounded" style="font-size:14px;">download</i> CSV</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="rp-card">
        <h4><i class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">table_chart</i> Class Attendance Report</h4>
        <div style="overflow-x:auto">
            <table class="rp-table">
                <thead><tr>
                    <th>#</th><th>Student</th><th>Class</th><th>School Days</th><th>Present</th><th>Absent</th><th>Late</th><th>Early</th><th>%</th>
                </tr></thead>
                <tbody>
                    @forelse($rows as $i=>$r)
                    <tr style="cursor:pointer" onclick="location.href='{{ route('reports.student', hash_id($r['child_id'])) }}?{{ $queryStr }}'">
                        <td>{{ ($paginator->currentPage()-1)*$paginator->perPage()+$i+1 }}</td>
                        <td style="font-weight:700">{{ $r['name'] }}</td>
                        <td>{{ $r['classroom'] }}</td>
                        <td>{{ $r['total_days'] }}</td>
                        <td><span class="status-badge green">{{ $r['present'] }}</span></td>
                        <td><span class="status-badge red">{{ $r['absent'] }}</span></td>
                        <td><span class="status-badge amber">{{ $r['late'] }}</span></td>
                        <td><span class="status-badge blue">{{ $r['early'] }}</span></td>
                        <td style="font-weight:700">{{ $r['percentage'] }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">No data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($paginator) && $paginator->hasPages())
        <div style="margin-top:16px;">
            {{ $paginator->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
