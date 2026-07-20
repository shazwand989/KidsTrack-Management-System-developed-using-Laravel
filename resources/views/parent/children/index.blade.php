@extends('layouts.parent-template')

@section('content')
<style>
    .card-table { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    .card-table h4 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0 0 16px; }
    .child-table { width: 100%; border-collapse: collapse; }
    .child-table th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .child-table td { padding: 12px 14px; font-size: 13px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .child-table tbody tr { cursor: pointer; transition: .15s; }
    .child-table tbody tr:hover { background: #f8fafc; }
    .child-table tr:last-child td { border-bottom: none; }
    .child-avatar { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6d28d9, #9333ea); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0; }
    .btn-view { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; background: #ede9fe; color: #6d28d9; border-radius: 10px; font-size: 11px; font-weight: 700; text-decoration: none; }
    .btn-view:hover { background: #ddd6fe; }
</style>

<div class="card-table">
    <h4><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">child_care</i> My Children</h4>
    @if($children->count() > 0)
    <div style="overflow-x:auto;">
        <table class="child-table">
            <thead><tr><th>#</th><th>Name</th><th>Age</th><th>Class</th><th>Dietary</th><th></th></tr></thead>
            <tbody>
                @foreach($children as $i => $child)
                <tr onclick="location.href='{{ route('parent.children.show', $child->id) }}'">
                    <td style="color:#94a3b8;font-weight:700;">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
                            <span style="font-weight:700;color:#1e293b;">{{ $child->name }}</span>
                        </div>
                    </td>
                    <td>{{ $child->age }}</td>
                    <td>{{ $child->classroom->name ?? 'N/A' }}</td>
                    <td>{{ $child->dietary ?? 'None' }}</td>
                    <td><a href="{{ route('parent.children.show', $child->id) }}" class="btn-view"><i class="material-symbols-rounded" style="font-size:14px;">visibility</i> View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;color:#94a3b8;">No children registered.</div>
    @endif
</div>
@endsection
