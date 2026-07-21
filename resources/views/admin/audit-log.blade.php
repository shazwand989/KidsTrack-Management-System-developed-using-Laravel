@extends('layouts.template')

@section('title', 'Audit Log')
@section('page-title', 'Audit Trail')

@section('content')

<style>
    .audit-wrap { width: 100%; }
    .audit-table { width: 100%; border-collapse: collapse; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.04); border: 1px solid #f1f5f9; }
    .audit-table thead { background: #FFF5F2; }
    .audit-table th { padding: 10px 12px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #92400E; text-align: left; }
    .audit-table td { padding: 8px 12px; font-size: 12px; border-bottom: 1px solid #FFF5F2; color: #334155; }
    .audit-table tr:hover td { background: #FFFAF9; }
    .audit-badge { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .badge-create { background: #dcfce7; color: #16a34a; }
    .badge-update { background: #dbeafe; color: #2563eb; }
    .badge-delete { background: #fee2e2; color: #dc2626; }
    .badge-login { background: #f3e8ff; color: #9333ea; }
    .badge-logout { background: #f1f5f9; color: #64748b; }
    .badge-registered { background: #fef3c7; color: #d97706; }
    .badge-default { background: #f1f5f9; color: #475569; }
    .json-pop { font-size: 10px; color: #94a3b8; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: pointer; }
    .json-pop:hover { color: #FF6B6B; }
    .pagination-wrap { margin-top: 16px; }
</style>

<div class="audit-wrap">
    <div class="table-card" style="overflow-x:auto;">
        <table class="audit-table">
            <thead>
                <tr>
                    <th>#</th><th>User</th><th>Role</th><th>Action</th><th>Module</th>
                    <th>Entity</th><th>Changes</th><th>IP</th><th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $i => $log)
                <tr>
                    <td style="color:#94a3b8;font-weight:700;">{{ $logs->firstItem() + $i }}</td>
                    <td><strong>{{ $log->user_name ?? 'System' }}</strong></td>
                    <td style="font-size:10px;color:#64748b;">{{ $log->user_role }}</td>
                    <td>
                        @php
                            $badgeMap = ['created'=>'badge-create','updated'=>'badge-update','deleted'=>'badge-delete',
                                         'login'=>'badge-login','logout'=>'badge-logout','registered'=>'badge-registered'];
                        @endphp
                        <span class="audit-badge {{ $badgeMap[$log->action] ?? 'badge-default' }}">{{ $log->action }}</span>
                    </td>
                    <td>{{ $log->module }}</td>
                    <td style="font-size:11px;">{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</td>
                    <td>
                        @if($log->old_values)
                            <span class="json-pop" title="{{ json_encode($log->old_values) }}">old: {{ json_encode($log->old_values) }}</span>
                        @endif
                        @if($log->new_values)
                            <span class="json-pop" title="{{ json_encode($log->new_values) }}">new: {{ json_encode($log->new_values) }}</span>
                        @endif
                        @if(!$log->old_values && !$log->new_values && $log->note)
                            <span style="font-size:11px;color:#64748b;">{{ $log->note }}</span>
                        @endif
                    </td>
                    <td style="font-size:10px;font-family:monospace;">{{ $log->ip_address }}</td>
                    <td style="font-size:10px;color:#94a3b8;">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</div>

@endsection
