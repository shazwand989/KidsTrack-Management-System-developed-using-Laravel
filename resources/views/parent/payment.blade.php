@extends('layouts.parent-template')

@section('content')
<style>
    .page-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .page-card h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 20px; display: flex; align-items: center; gap: 8px; }
    .payment-table { width: 100%; border-collapse: collapse; }
    .payment-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .payment-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; }
    .payment-table tr:last-child td { border-bottom: none; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
    .badge.paid { background: #e8f5e9; color: #2e7d32; }
    .badge.pending { background: #fff3e0; color: #e65100; }
    .badge.overdue { background: #fce4ec; color: #c62828; }
    .summary-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 20px; }
    .summary-item { background: white; border-radius: 16px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; }
    .summary-item .amount { font-size: 24px; font-weight: 800; color: #1e293b; }
    .summary-item .lbl { font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
</style>

<div class="summary-row">
    <div class="summary-item">
        <div class="amount" style="color:#2e7d32;">RM0.00</div>
        <div class="lbl">Paid</div>
    </div>
    <div class="summary-item">
        <div class="amount" style="color:#e65100;">RM0.00</div>
        <div class="lbl">Pending</div>
    </div>
    <div class="summary-item">
        <div class="amount" style="color:#c62828;">RM0.00</div>
        <div class="lbl">Overdue</div>
    </div>
</div>

<div class="page-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">payments</i> Payment History</h4>
    <div style="overflow-x:auto;">
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">receipt_long</i>
                        No payment records yet.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
