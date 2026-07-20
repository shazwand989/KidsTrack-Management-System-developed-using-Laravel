@extends('layouts.parent-template')

@section('content')
<style>
    .page-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
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
</style>

<div class="alert-box">
    <span class="icon"><i class="material-symbols-rounded" style="font-size:24px;">warning</i></span>
    <p>Late check-ins may incur fines according to nursery policy. Please ensure timely attendance.</p>
</div>

<div class="page-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">gavel</i> Fine Records</h4>
    <div style="overflow-x:auto;">
        <table class="fine-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Child</th>
                    <th>Reason</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">gavel</i>
                        No fine records. Good job!
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
