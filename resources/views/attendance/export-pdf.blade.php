<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="{{ asset('css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lib/all.min.css') }}">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; padding: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
        .header p { margin: 2px 0; color: #64748b; font-size: 12px; }
        .stats-row { background: #f8fafc; border-radius: 8px; padding: 14px 20px; margin-bottom: 20px; }
        .stat-item { display: flex; align-items: center; gap: 8px; }
        .stat-icon { font-size: 16px; width: 20px; }
        .stat-icon.success { color: #16a34a; } .stat-icon.info { color: #2563eb; }
        .stat-icon.warning { color: #d97706; } .stat-icon.danger { color: #dc2626; }
        .stat-num { font-size: 20px; font-weight: 800; }
        .stat-num.success { color: #16a34a; } .stat-num.info { color: #2563eb; }
        .stat-num.warning { color: #d97706; } .stat-num.danger { color: #dc2626; }
        .stat-lbl { font-size: 13px; font-weight: 700; color: #475569; }
        .table th { background: #f1f5f9; font-size: 10px; text-transform: uppercase; font-weight: 800; color: #475569; border-bottom: 2px solid #dee2e6; }
        .table td { font-size: 12px; vertical-align: middle; }
        .badge-status { padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 700; }
        .badge-checkin { background: #dcfce7; color: #16a34a; }
        .badge-checkout { background: #dbeafe; color: #2563eb; }
        .badge-late { background: #fef3c7; color: #b45309; }
        .badge-absent { background: #fee2e2; color: #dc2626; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #dee2e6; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="header text-center">
        <h1>Attendance Report</h1>
        <p>
            <strong>Generated:</strong> {{ $generated_at }}
            &bull; <strong>By:</strong> {{ $generated_by }}
            &bull; <strong>Total:</strong> {{ $total }} records
        </p>
    </div>

    {{-- Stats Row --}}
    <div class="stats-row">
        <div class="row g-3">
            <div class="col-3">
                <div class="stat-item">
                    <i class="fas fa-check-circle stat-icon success"></i>
                    <div class="stat-num success">{{ $totalCheckin }}</div>
                    <div class="stat-lbl">Check-ins</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-item">
                    <i class="fas fa-sign-out-alt stat-icon info"></i>
                    <div class="stat-num info">{{ $totalCheckout }}</div>
                    <div class="stat-lbl">Check-outs</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-item">
                    <i class="fas fa-exclamation-triangle stat-icon warning"></i>
                    <div class="stat-num warning">{{ $totalLate }}</div>
                    <div class="stat-lbl">Late</div>
                </div>
            </div>
            <div class="col-3">
                <div class="stat-item">
                    <i class="fas fa-times-circle stat-icon danger"></i>
                    <div class="stat-num danger">{{ $totalAbsent }}</div>
                    <div class="stat-lbl">Absent</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Child</th>
                    <th>Classroom</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Drop Off By</th>
                    <th>Pickup By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $attendance)
                @php
                    $child = $attendance->child;
                    $classroom = $child ? $child->classroom : null;
                    $status = $attendance->status;

                    $badgeMap = [
                        'checkin' => ['Check-in', 'badge-checkin'],
                        'present' => ['Check-in', 'badge-checkin'],
                        'checkout' => ['Check-out', 'badge-checkout'],
                        'late' => ['Late', 'badge-late'],
                        'late_checkout' => ['Late', 'badge-late'],
                        'absent' => ['Absent', 'badge-absent'],
                    ];
                    $badge = $badgeMap[$status] ?? ['Unknown', 'badge-absent'];

                    $dropOff = $attendance->drop_off_by;
                    if ($dropOff && is_numeric($dropOff)) {
                        $dropOff = optional(\App\Models\ParentModel::find($dropOff))->name
                            ?? optional(\App\Models\User::find($dropOff))->name
                            ?? $dropOff;
                    }

                    $pickup = $attendance->pickup_by;
                    if ($pickup && is_numeric($pickup)) {
                        $pickup = optional(\App\Models\ParentModel::find($pickup))->name
                            ?? optional(\App\Models\User::find($pickup))->name
                            ?? $pickup;
                    }
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $child->name ?? 'Unknown' }}</td>
                    <td>{{ $classroom->name ?? 'No Class' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                    <td><span class="badge-status {{ $badge[1] }}">{{ $badge[0] }}</span></td>
                    <td>{{ $attendance->checkin_time ? \Carbon\Carbon::parse($attendance->checkin_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $attendance->checkout_time ? \Carbon\Carbon::parse($attendance->checkout_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $dropOff ?? '-' }}</td>
                    <td>{{ $pickup ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No attendance records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer text-center">
        <p class="mb-1">This report is auto-generated from KidsTrack System</p>
        <p class="mb-0">&copy; {{ date('Y') }} KidsTrack &mdash; All Rights Reserved</p>
    </div>
</div>

</body>
</html>
