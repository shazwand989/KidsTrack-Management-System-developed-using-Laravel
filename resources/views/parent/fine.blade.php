@extends('layouts.parent-template')

@section('content')
<style>
    .page-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .page-card h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 20px; display: flex; align-items: center; gap: 8px; }
    .fine-table { width: 100%; border-collapse: collapse; }
    .fine-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .fine-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .fine-table tr:last-child td { border-bottom: none; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
    .badge.paid { background: #e8f5e9; color: #2e7d32; }
    .badge.pending { background: #fff3e0; color: #e65100; }
    .alert-box { background: #fff3e0; border: 1px solid #ffcc02; border-radius: 14px; padding: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
    .alert-box .icon { color: #e65100; flex-shrink: 0; }
    .alert-box p { margin: 0; font-size: 13px; color: #e65100; font-weight: 600; }
    .summary-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
    .sum-box { background: #f8fafc; border-radius: 14px; padding: 16px; text-align: center; border: 1px solid #e2e8f0; }
    .sum-box .num { font-size: 24px; font-weight: 800; }
    .sum-box.red .num { color: #dc2626; }
    .sum-box.orange .num { color: #e65100; }
    .sum-box .lbl { font-size: 11px; color: #64748b; font-weight: 600; margin-top: 2px; }
</style>

@if($pendingCount > 0)
<div class="alert-box">
    <span class="icon"><i class="material-symbols-rounded" style="font-size:24px;">warning</i></span>
    <p>You have <strong>{{ $pendingCount }}</strong> unpaid fine(s) totaling <strong>RM {{ number_format($totalPending, 2) }}</strong>. Please settle them promptly.</p>
</div>
@endif

<div class="summary-row">
    <div class="sum-box red">
        <div class="num">RM {{ number_format($totalPending, 2) }}</div>
        <div class="lbl">Outstanding</div>
    </div>
    <div class="sum-box orange">
        <div class="num">{{ $pendingCount }}</div>
        <div class="lbl">Pending Fines</div>
    </div>
</div>

<div class="page-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">gavel</i> Fine Records</h4>
    @if($penalties->count() > 0)
    <div style="overflow-x:auto;">
        <table class="fine-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Child</th>
                    <th>Class</th>
                    <th>Scheduled</th>
                    <th>Actual</th>
                    <th>Late</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penalties as $p)
                <tr>
                    <td>{{ $p->date->format('d M Y') }}</td>
                    <td style="font-weight:700;">{{ $p->child->name ?? '—' }}</td>
                    <td>{{ $p->child->classroom->name ?? '—' }}</td>
                    <td>{{ $p->scheduled_checkout }}</td>
                    <td>{{ $p->actual_checkout }}</td>
                    <td>{{ $p->late_minutes }} min</td>
                    <td><strong>RM {{ number_format($p->penalty_amount, 2) }}</strong></td>
                    <td><span class="badge {{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;color:#94a3b8;">
        <i class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">gavel</i>
        No fine records. Good job!
    </div>
    @endif
</div>
@endsection
