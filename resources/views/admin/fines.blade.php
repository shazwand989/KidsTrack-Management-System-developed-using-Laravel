@extends('layouts.template')

@section('title', 'Fine Dashboard')
@section('page-title', 'Fine Dashboard')

@section('content')
<style>
    .fin-card{background:white;border-radius:16px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f1f5f9;margin-bottom:20px}
    .fin-card h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 16px}
    .sum-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
    .sum-box{background:#f8fafc;border-radius:12px;padding:16px;text-align:center;border:1px solid #e2e8f0}
    .sum-box .num{font-size:24px;font-weight:800;line-height:1}
    .sum-box.red .num{color:#dc2626}.sum-box.green .num{color:#16a34a}
    .sum-box.amber .num{color:#d97706}.sum-box.blue .num{color:#3b82f6}
    .sum-box .lbl{font-size:11px;color:#64748b;font-weight:600;margin-top:4px}
    .fin-table{width:100%;border-collapse:collapse;font-size:12px}
    .fin-table th{background:#FFF5F2;padding:8px 10px;font-size:10px;font-weight:800;text-transform:uppercase;color:#92400E;text-align:left}
    .fin-table td{padding:8px 10px;border-bottom:1px solid #f1f5f9}
    .badge{display:inline-block;padding:3px 8px;border-radius:8px;font-size:10px;font-weight:700}
    .badge.pending{background:#fef3c7;color:#d97706}
    .badge.paid{background:#dcfce7;color:#16a34a}
    .badge.failed{background:#fce4ec;color:#dc2626}
    .parent-row{cursor:pointer;transition:background .15s}
    .parent-row:hover{background:#FFFAF9}
    @media(max-width:768px){.sum-row{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="sum-row">
    <div class="sum-box red"><div class="num">RM {{ number_format($totalPending,2) }}</div><div class="lbl">Outstanding Fines</div></div>
    <div class="sum-box green"><div class="num">RM {{ number_format($totalPaid,2) }}</div><div class="lbl">Total Collected</div></div>
    <div class="sum-box amber"><div class="num">{{ $pending->count() }}</div><div class="lbl">Pending Payments</div></div>
    <div class="sum-box blue"><div class="num">{{ $totalCollected }}</div><div class="lbl">Completed Payments</div></div>
</div>

<div class="fin-card">
    <h4><i class="fas fa-users"></i> Outstanding by Parent</h4>
    <div style="overflow-x:auto">
        <table class="fin-table">
            <thead><tr>
                <th>Parent</th><th>Pending</th><th>Total Amount</th><th>Children</th><th>Action</th>
            </tr></thead>
            <tbody>
                @forelse($byParent as $p)
                <tr class="parent-row">
                    <td><strong>{{ $p['parent_name'] }}</strong></td>
                    <td><span class="badge pending">{{ $p['count'] }} fines</span></td>
                    <td><strong style="color:#dc2626">RM {{ number_format($p['total'],2) }}</strong></td>
                    <td>
                        @foreach($p['penalties'] as $pen)
                            <span style="display:inline-block;margin:2px 4px 2px 0;font-size:11px;">
                                {{ $pen->child->name ?? '—' }}
                                <span style="color:#94a3b8">({{ $pen->date->format('d M') }})</span>
                            </span>
                        @endforeach
                    </td>
                    <td>
                        @foreach($p['penalties'] as $pen)
                            <form method="POST" action="{{ route('penalties.mark-paid', $pen->id) }}" style="display:inline">
                                @csrf
                                <button style="background:#16a34a;color:white;border:none;padding:3px 8px;border-radius:6px;font-size:10px;cursor:pointer;margin:2px" title="Mark {{ $pen->child->name ?? '—' }} as paid">✓ Mark Paid</button>
                            </form>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:30px;color:#94a3b8">No outstanding fines. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="fin-card">
    <h4><i class="fas fa-history"></i> Recent Fines</h4>
    <div style="overflow-x:auto">
        <table class="fin-table">
            <thead><tr>
                <th>Child</th><th>Class</th><th>Date</th><th>Late</th><th>Amount</th><th>Status</th><th>Parent</th>
            </tr></thead>
            <tbody>
                @forelse($allPenalties->take(20) as $p)
                <tr>
                    <td><strong>{{ $p->child->name ?? '—' }}</strong></td>
                    <td>{{ $p->child->classroom->name ?? '—' }}</td>
                    <td>{{ $p->date->format('d M Y') }}</td>
                    <td>{{ $p->late_minutes }}m</td>
                    <td>RM {{ number_format($p->penalty_amount,2) }}</td>
                    <td><span class="badge {{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                    <td>{{ $p->parent->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">No fines recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:12px;text-align:right">
        <a href="{{ route('penalties.settings') }}" style="color:#FF6B6B;font-weight:700;font-size:12px;text-decoration:none">Manage Settings →</a>
    </div>
</div>
@endsection
