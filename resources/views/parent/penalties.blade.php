@extends('layouts.parent-template')

@section('content')
<style>
    .pen-card{background:white;border-radius:16px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f1f5f9;margin-bottom:16px}
    .pen-card h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 12px}
    .summary-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:10px;margin-bottom:16px}
    .sum-box{background:#f8fafc;border-radius:12px;padding:14px;text-align:center;border:1px solid #e2e8f0}
    .sum-box .num{font-size:22px;font-weight:800}
    .sum-box.red .num{color:#dc2626}.sum-box.green .num{color:#16a34a}
    .sum-box .lbl{font-size:11px;color:#64748b;font-weight:600;margin-top:2px}
    .pen-table{width:100%;border-collapse:collapse;font-size:12px}
    .pen-table th{background:#FFF5F2;padding:8px 10px;font-size:10px;font-weight:800;text-transform:uppercase;color:#92400E;text-align:left}
    .pen-table td{padding:8px 10px;border-bottom:1px solid #f1f5f9}
    .badge{display:inline-block;padding:4px 10px;border-radius:8px;font-size:10px;font-weight:700}
    .badge.pending{background:#fef3c7;color:#d97706}
    .badge.paid{background:#dcfce7;color:#16a34a}
    .badge.failed{background:#fce4ec;color:#dc2626}
    .btn-pay{background:#16a34a;color:white;border:none;padding:6px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer}
    .btn-pay:hover{opacity:.9}
    .alert{background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#dc2626;font-weight:600}
    .alert.success{background:#f0fdf4;border-color:#bbf7d0;color:#16a34a}
</style>

@if(session('error'))
<div class="alert"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if(session('success'))
<div class="alert success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="summary-row">
    <div class="sum-box red"><div class="num">RM {{ number_format($totalPending,2) }}</div><div class="lbl">Outstanding</div></div>
    <div class="sum-box green"><div class="num">RM {{ number_format($totalPaid,2) }}</div><div class="lbl">Paid</div></div>
    <div class="sum-box"><div class="num" style="color:#d97706;">{{ $pending->count() }}</div><div class="lbl">Pending</div></div>
    <div class="sum-box"><div class="num" style="color:#16a34a;">{{ $paid->count() }}</div><div class="lbl">Completed</div></div>
</div>

<div class="pen-card">
    <h4><i class="fas fa-clock"></i> Late Pickup Penalties</h4>
    <div style="overflow-x:auto">
        <table class="pen-table">
            <thead><tr>
                <th>Child</th><th>Class</th><th>Date</th><th>Late</th><th>Amount</th><th>Status</th><th>Paid Date</th><th>Action</th>
            </tr></thead>
            <tbody>
                @forelse($penalties as $p)
                <tr>
                    <td><strong>{{ $p->child->name ?? '—' }}</strong></td>
                    <td>{{ $p->child->classroom->name ?? '—' }}</td>
                    <td>{{ $p->date->format('d M Y') }}</td>
                    <td>{{ $p->late_minutes }}m</td>
                    <td>RM {{ number_format($p->penalty_amount,2) }}</td>
                    <td><span class="badge {{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                    <td>{{ $p->paid_at?->format('d M Y h:i A') ?? '—' }}</td>
                    <td>
                        @if($p->payment_status === 'pending')
                            <form method="POST" action="{{ route('parent.penalties.pay', $p->id) }}">
                                @csrf
                                <button class="btn-pay"><i class="fas fa-credit-card"></i> Pay Now</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8">No penalties found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
