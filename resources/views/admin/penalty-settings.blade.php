@extends('layouts.template')

@section('title', 'Late Check-out Penalty Settings')
@section('page-title', 'Penalty Settings')

@section('content')
<style>
    .set-card{background:white;border-radius:16px;padding:24px;box-shadow:0 2px 10px rgba(0,0,0,.04);border:1px solid #f1f5f9;margin-bottom:20px}
    .set-card h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 16px}
    .set-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .set-group label{display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:4px}
    .set-group input,.set-group select{border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:13px;width:100%}
    .set-group small{color:#94a3b8;font-size:11px}
    .btn-save{background:linear-gradient(135deg,#FF6B6B,#FF9E7D);color:white;border:none;padding:12px 24px;border-radius:12px;font-weight:700;cursor:pointer;font-size:14px}
    .btn-save:hover{opacity:.9}
    .rp-table{width:100%;border-collapse:collapse;font-size:12px}
    .rp-table th{background:#FFF5F2;padding:8px 10px;font-size:10px;font-weight:800;text-transform:uppercase;color:#92400E}
    .rp-table td{padding:8px 10px;border-bottom:1px solid #f1f5f9}
    .badge{display:inline-block;padding:3px 8px;border-radius:8px;font-size:10px;font-weight:700}
    .badge.pending{background:#fef3c7;color:#d97706}
    .badge.paid{background:#dcfce7;color:#16a34a}
    @media(max-width:600px){.set-row{grid-template-columns:1fr}}
</style>

@if(session('success'))
<div style="background:#dcfce7;color:#16a34a;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

<div class="set-card">
    <h4><i class="fas fa-cog"></i> Late Check-out Penalty Configuration</h4>
    <form method="POST" action="{{ route('penalties.settings.save') }}">
        @csrf
        <div class="set-row">
            <div class="set-group">
                <label>Enable Penalty</label>
                <select name="enabled">
                    <option value="false" {{ ($settings['enabled']??'')==='false'?'selected':'' }}>Disabled</option>
                    <option value="true" {{ ($settings['enabled']??'')==='true'?'selected':'' }}>Enabled</option>
                </select>
            </div>
            <div class="set-group">
                <label>Grace Period (minutes)</label>
                <input type="number" name="grace_period" value="{{ $settings['grace_period']??10 }}" min="0">
                <small>Free minutes before penalty applies</small>
            </div>
            <div class="set-group">
                <label>Penalty Amount (RM)</label>
                <input type="number" name="penalty_amount" value="{{ $settings['penalty_amount']??1.00 }}" step="0.01" min="0">
            </div>
            <div class="set-group">
                <label>ToyyibPay Mode</label>
                <select name="toyyibpay_mode">
                    <option value="sandbox" {{ ($settings['toyyibpay_mode']??'sandbox')==='sandbox'?'selected':'' }}>Sandbox (Testing)</option>
                    <option value="live" {{ ($settings['toyyibpay_mode']??'')==='live'?'selected':'' }}>Live (Production)</option>
                </select>
            </div>
            <div class="set-group">
                <label>ToyyibPay Category Code</label>
                <input type="text" name="toyyibpay_category" value="{{ $settings['toyyibpay_category']??'' }}">
            </div>
            <div class="set-group">
                <label>ToyyibPay Secret Key</label>
                <input type="password" name="toyyibpay_secret" value="{{ $settings['toyyibpay_secret']??'' }}">
            </div>
            <div class="set-group">
                <label>Callback URL</label>
                <input type="text" name="callback_url" value="{{ $settings['callback_url']??url('/api/penalty/callback') }}">
            </div>
            <div class="set-group">
                <label>Return URL</label>
                <input type="text" name="return_url" value="{{ $settings['return_url']??url('/parent/penalties') }}">
            </div>
        </div>
        <button type="submit" class="btn-save" style="margin-top:16px"><i class="fas fa-save"></i> Save Settings</button>
    </form>
</div>

<div class="set-card">
    <h4><i class="fas fa-list"></i> Penalty Records</h4>
    <div style="overflow-x:auto">
        <table class="rp-table">
            <thead><tr>
                <th>Child</th><th>Date</th><th>Late</th><th>Amount</th><th>Status</th><th>Bill Code</th><th>Action</th>
            </tr></thead>
            <tbody>
                @forelse($penalties as $p)
                <tr>
                    <td><strong>{{ $p->child->name ?? '—' }}</strong></td>
                    <td>{{ $p->date->format('d M Y') }}</td>
                    <td>{{ $p->late_minutes }}m</td>
                    <td>RM {{ number_format($p->penalty_amount, 2) }}</td>
                    <td><span class="badge {{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                    <td>{{ $p->bill_code ?? '—' }}</td>
                    <td>
                        @if($p->payment_status === 'pending')
                            <form method="POST" action="{{ route('penalties.mark-paid', $p->id) }}" style="display:inline">
                                @csrf
                                <button style="background:#16a34a;color:white;border:none;padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer">Mark Paid</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('penalties.destroy', $p->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button style="background:#dc2626;color:white;border:none;padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">No penalties recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $penalties->links() }}</div>
</div>
@endsection
