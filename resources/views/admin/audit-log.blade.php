@extends('layouts.template')

@section('title', 'Audit Log')
@section('page-title', 'Audit Trail')

@section('content')

<style>
    .audit-wrap { width: 100%; }
    .audit-card { background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,.04); border: 1px solid #f1f5f9; padding: 20px; }
    .audit-table { width: 100%; border-collapse: collapse; }
    .audit-table thead { background: #FFF5F2; }
    .audit-table th { padding: 10px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #92400E; text-align: left; }
    .audit-table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #fff5f2; color: #334155; vertical-align: top; }
    .audit-table tr:hover td { background: #FFFAF9; }
    .audit-badge { display: inline-block; padding: 4px 12px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .badge-create { background: #dcfce7; color: #16a34a; }
    .badge-update { background: #dbeafe; color: #2563eb; }
    .badge-delete { background: #fee2e2; color: #dc2626; }
    .badge-login { background: #f3e8ff; color: #9333ea; }
    .badge-logout { background: #f1f5f9; color: #64748b; }
    .badge-registered { background: #fef3c7; color: #d97706; }
    .badge-default { background: #f1f5f9; color: #475569; }

    /* Change display */
    .change-row { display: flex; align-items: center; gap: 8px; font-size: 12px; padding: 2px 0; }
    .change-row .field { font-weight: 600; color: #475569; min-width: 80px; font-size: 11px; }
    .change-row .old { background: #fef2f2; color: #dc2626; padding: 2px 8px; border-radius: 6px; font-size: 11px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .change-row .new { background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 6px; font-size: 11px; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .change-row .arrow { color: #94a3b8; font-weight: 700; }
    .change-empty { color: #cbd5e1; font-size: 12px; }
    .change-toggle { cursor: pointer; color: #FF6B6B; font-weight: 700; font-size: 11px; }
    .change-toggle:hover { text-decoration: underline; }
    .change-detail { display: none; margin-top: 6px; }
    .change-detail.show { display: block; }

    /* Modal for full view */
    .ch-modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden}
    .ch-modal-overlay.show{opacity:1;visibility:visible}
    .ch-modal{background:white;border-radius:20px;padding:24px;max-width:500px;width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3)}
    .ch-modal h4{font-size:15px;font-weight:800;color:#1e293b;margin:0 0 16px}
    .ch-modal .field-block{margin-bottom:12px;padding:10px;border-radius:10px;background:#f8fafc}
    .ch-modal .field-name{font-size:11px;font-weight:700;color:#64748b;margin-bottom:4px;text-transform:uppercase}
    .ch-modal .field-val{font-size:13px;padding:4px 8px;border-radius:6px}
    .ch-modal .field-val.old{background:#fef2f2;color:#dc2626}
    .ch-modal .field-val.new{background:#f0fdf4;color:#16a34a}
</style>

<div class="audit-wrap">
    <div class="audit-card">
    <div style="overflow-x:auto;">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th><th>User</th><th>Action</th><th>Module</th>
                    <th>Entity</th><th>Changes</th><th>IP</th><th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                @php
                    $badgeMap = ['created'=>'badge-create','updated'=>'badge-update','deleted'=>'badge-delete',
                                 'login'=>'badge-login','logout'=>'badge-logout','registered'=>'badge-registered'];
                    $oldVals = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                    $newVals = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                    $hasChanges = ($oldVals || $newVals);
                @endphp
                <tr>
                    <td style="color:#94a3b8;font-weight:700;">{{ $logs->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $log->user_name ?? 'System' }}</strong>
                        <div style="font-size:10px;color:#94a3b8;">{{ $log->user_role }}</div>
                    </td>
                    <td><span class="audit-badge {{ $badgeMap[$log->action] ?? 'badge-default' }}">{{ strtoupper($log->action) }}</span></td>
                    <td>{{ $log->module }}</td>
                    <td style="font-size:12px;">
                        @if($log->auditable_type)
                            <strong>{{ class_basename($log->auditable_type) }}</strong>
                            <span style="color:#94a3b8;">#{{ $log->auditable_id }}</span>
                        @else
                            <span class="change-empty">—</span>
                        @endif
                    </td>
                    <td>
                        @if($hasChanges)
                            @php $cid = 'ch-'.$log->id; @endphp
                            <div>
                                @if($log->action === 'updated' && $oldVals && $newVals)
                                    @foreach(array_keys($oldVals) as $k)
                                        @php $o = $oldVals[$k] ?? ''; $n = $newVals[$k] ?? ''; @endphp
                                        @if($o != $n)
                                        <div class="change-row">
                                            <span class="field">{{ $k }}:</span>
                                            <span class="old">{{ is_scalar($o) ? (strlen($o) > 25 ? substr($o,0,25).'…' : $o) : json_encode($o) }}</span>
                                            <span class="arrow">→</span>
                                            <span class="new">{{ is_scalar($n) ? (strlen($n) > 25 ? substr($n,0,25).'…' : $n) : json_encode($n) }}</span>
                                        </div>
                                        @endif
                                    @endforeach
                                    @if(count(array_keys($oldVals)) > 2)
                                        <div class="change-toggle" onclick="document.getElementById('{{$cid}}').classList.toggle('show')">Show all ↓</div>
                                        <div class="change-detail" id="{{$cid}}">
                                            @foreach(array_keys($oldVals) as $k)
                                                @php $o = $oldVals[$k] ?? ''; $n = $newVals[$k] ?? ''; @endphp
                                                @if($o != $n)
                                                <div class="change-row">
                                                    <span class="field">{{ $k }}:</span>
                                                    <span class="old">{{ is_scalar($o) ? $o : json_encode($o) }}</span>
                                                    <span class="arrow">→</span>
                                                    <span class="new">{{ is_scalar($n) ? $n : json_encode($n) }}</span>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($log->action === 'created' && $newVals)
                                    @foreach($newVals as $k => $v)
                                        @if(!is_null($v) && $v !== '')
                                        <div class="change-row">
                                            <span class="field">{{ $k }}:</span>
                                            <span class="new">{{ is_scalar($v) ? (strlen($v) > 30 ? substr($v,0,30).'…' : $v) : json_encode($v) }}</span>
                                        </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="change-row">
                                        <span class="old">{{ json_encode($oldVals) }}</span>
                                        <span class="arrow">→</span>
                                        <span class="new">{{ json_encode($newVals) }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="change-empty">{{ $log->note ?? '—' }}</span>
                        @endif
                    </td>
                    <td style="font-size:10px;font-family:monospace;color:#94a3b8;">{{ $log->ip_address }}</td>
                    <td style="font-size:11px;color:#94a3b8;white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
