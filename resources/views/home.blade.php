@extends('layouts.template')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@php
    use App\Models\Child;
    use App\Models\Teacher;
    use App\Models\Classroom;
    use App\Models\Attendance;
    use App\Models\SimulationClock;

    $hour = (int)date('H', SimulationClock::getCurrentTime());
    $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Tengah Hari' : ($hour < 19 ? 'Selamat Petang' : 'Selamat Malam'));

    $todayStr = date('Y-m-d', SimulationClock::getCurrentTime());
    $todayAttendances = Attendance::with('child.classroom')->where('date', $todayStr)->get();
    $totalChildren = Child::where('is_active', true)->count();
    $checkedIn = $todayAttendances->whereIn('status', ['checkin','present','late'])->count();
    $checkedOut = $todayAttendances->whereIn('status', ['checkout','late_checkout'])->count();
    $absent = $totalChildren - $todayAttendances->filter(fn($a) => !in_array($a->status, ['absent']))->count();
    $totalParents = \App\Models\User::whereIn('role', ['parent1', 'parent2', 'guardian'])->count();
    $totalTeachers = Teacher::count();
    $totalClassrooms = Classroom::count();

    // Penalty stats
    $penalties = \App\Models\LateCheckoutPenalty::all();
    $totalFines = $penalties->count();
    $pendingFines = $penalties->where('payment_status', 'pending')->count();
    $paidFines = $penalties->where('payment_status', 'paid')->count();
    $totalOutstanding = $penalties->where('payment_status', 'pending')->sum('penalty_amount');
    $totalCollectedAmount = $penalties->where('payment_status', 'paid')->sum('penalty_amount');

    $classrooms = Classroom::withCount(['children'])->get();

    $recentCheckins = Attendance::with('child.classroom')
        ->whereDate('date', $todayStr)
        ->whereNotNull('checkin_time')
        ->orderBy('checkin_time', 'desc')
        ->take(8)
        ->get();

    $weekDays = ['Mon','Tue','Wed','Thu','Fri'];
    $weekData = [];
    foreach ($weekDays as $i => $label) {
        $d = date('Y-m-d', strtotime("-" . (4-$i) . " days", SimulationClock::getCurrentTime()));
        $c = Attendance::where('date', $d)->whereIn('status', ['checkin','present','late'])->count();
        $weekData[] = $c;
    }
@endphp

@section('content')
<style>
    .db-grid { display: grid; gap: 20px; }
    .db-welcome {
        background: linear-gradient(135deg, #6d28d9 0%, #9333ea 50%, #c084fc 100%);
        border-radius: 24px; padding: 28px 32px; color: white;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 16px; margin-bottom: 6px;
    }
    .db-welcome h2 { font-size: 26px; font-weight: 800; margin: 0 0 6px; }
    .db-welcome p { opacity: 0.85; margin: 0; font-size: 14px; }
    .db-welcome .db-date-btn {
        background: rgba(255,255,255,0.2); border: none; color: white;
        padding: 10px 20px; border-radius: 14px; font-weight: 700; font-size: 13px;
        backdrop-filter: blur(4px); cursor: pointer;
    }

    .stats-row { display: grid; grid-template-columns: repeat(5,1fr); gap: 16px; margin-bottom: 20px; }
    .stat-card {
        background: white; border-radius: 20px; padding: 22px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 16px; transition: .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .stat-icon-wrap {
        width: 50px; height: 50px; border-radius: 16px; display: flex;
        align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    }
    .stat-icon-wrap.pink { background: #fce4ec; color: #e91e63; }
    .stat-icon-wrap.blue { background: #e3f2fd; color: #1e88e5; }
    .stat-icon-wrap.green { background: #e8f5e9; color: #43a047; }
    .stat-icon-wrap.orange { background: #fff3e0; color: #fb8c00; }
    .stat-info h4 { font-size: 26px; font-weight: 800; color: #1e293b; margin: 0 0 2px; }
    .stat-info span { font-size: 12px; color: #94a3b8; font-weight: 600; }
    .stat-trend { font-size: 11px; font-weight: 700; margin-left: 6px; }
    .stat-trend.up { color: #16a34a; }
    .stat-trend.down { color: #dc2626; }

    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }

    .card {
        background: white; border-radius: 20px; padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;
    }
    .card-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
    }
    .card-header h3 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0; }
    .card-header .badge {
        background: #e8f5e9; color: #2e7d32; font-size: 11px; font-weight: 700;
        padding: 4px 10px; border-radius: 20px;
    }

    .att-overview { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-bottom: 16px; }
    .att-item { text-align: center; padding: 14px; border-radius: 14px; background: #f8fafc; }
    .att-item .num { font-size: 28px; font-weight: 800; }
    .att-item .lbl { font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 2px; }
    .att-item.checkin { background: #e8f5e9; } .att-item.checkin .num { color: #2e7d32; }
    .att-item.checkout { background: #e3f2fd; } .att-item.checkout .num { color: #1565c0; }
    .att-item.absent { background: #fce4ec; } .att-item.absent .num { color: #c62828; }

    .progress-wrap { margin-bottom: 14px; }
    .progress-label { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #475569; }
    .progress-bar { height: 8px; border-radius: 8px; background: #f1f5f9; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 8px; transition: width .5s; }
    .progress-fill.green { background: linear-gradient(90deg, #43a047, #66bb6a); }
    .progress-fill.blue { background: linear-gradient(90deg, #1e88e5, #42a5f5); }
    .progress-fill.orange { background: linear-gradient(90deg, #fb8c00, #ffa726); }

    .checkin-list { max-height: 380px; overflow-y: auto; }
    .checkin-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .checkin-item:last-child { border-bottom: none; }
    .ci-avatar {
        width: 38px; height: 38px; border-radius: 12px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 800; font-size: 14px; flex-shrink: 0; overflow: hidden;
    }
    .ci-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ci-info { flex: 1; } .ci-name { font-weight: 700; font-size: 13px; color: #1e293b; }
    .ci-sub { font-size: 11px; color: #94a3b8; }
    .ci-time { font-size: 12px; font-weight: 700; color: #16a34a; }
    .ci-time.late { color: #dc2626; }

    .classroom-cards { display: grid; gap: 10px; }
    .classroom-card {
        display: flex; align-items: center; gap: 12px; padding: 14px;
        border-radius: 14px; background: #f8fafc; transition: .15s; cursor: pointer;
    }
    .classroom-card:hover { background: #f1f5f9; }
    .cc-bar-wrap { flex: 1; }
    .cc-bar { height: 6px; border-radius: 6px; background: #e2e8f0; overflow: hidden; margin-top: 6px; }
    .cc-fill { height: 100%; border-radius: 6px; }
    .cc-name { font-size: 13px; font-weight: 700; color: #1e293b; }
    .cc-count { font-size: 11px; color: #94a3b8; }

    .quick-actions { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-top: 16px; }
    .qa-btn {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 16px 10px; border-radius: 16px; background: #f8fafc;
        border: 1px solid #f1f5f9; text-decoration: none; transition: .15s;
        font-size: 12px; font-weight: 700; color: #475569; text-align: center;
    }
    .qa-btn:hover { background: #e8f5e9; border-color: #c8e6c9; color: #2e7d32; }
    .qa-btn i, .qa-btn .material-symbols-rounded { font-size: 24px; }

    .chart-wrap { height: 200px; margin-top: 10px; }

    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(3,1fr); }
        .content-grid { grid-template-columns: 1fr; }
        .att-overview { grid-template-columns: repeat(3,1fr); }
    }
    @media (max-width: 576px) {
        .stats-row { grid-template-columns: 1fr; }
    }
</style>

<div class="db-grid">
    {{-- Welcome Banner --}}
    <div class="db-welcome">
        <div>
            <h2>{{ $greeting }}, {{ auth()->user()->name }}!</h2>
            <p>{{ \Carbon\Carbon::createFromTimestamp(SimulationClock::getCurrentTime())->isoFormat('dddd, D MMMM YYYY') }} · {{ \Carbon\Carbon::createFromTimestamp(SimulationClock::getCurrentTime())->format('h:i A') }}</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <span style="background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:12px;font-size:13px;font-weight:700;">
                <i class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">schedule</i>
                {{ \App\Models\SimulationClock::getFormattedTime() }}
            </span>
            <a href="{{ route('simulation.dashboard') }}" class="db-date-btn">
                <i class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;">settings</i> Simulation
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon-wrap pink"><i class="material-symbols-rounded">child_care</i></div>
            <div class="stat-info">
                <h4>{{ $totalChildren }}</h4>
                <span>Total Children</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap blue"><i class="material-symbols-rounded">family_restroom</i></div>
            <div class="stat-info">
                <h4>{{ $totalParents }}</h4>
                <span>Families</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap green"><i class="material-symbols-rounded">school</i></div>
            <div class="stat-info">
                <h4>{{ $totalTeachers }}</h4>
                <span>Teachers</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap orange"><i class="material-symbols-rounded">meeting_room</i></div>
            <div class="stat-info">
                <h4>{{ $totalClassrooms }}</h4>
                <span>Classrooms</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="material-symbols-rounded">gavel</i></div>
            <div class="stat-info">
                <h4>RM {{ number_format($totalOutstanding, 0) }}</h4>
                <span>Outstanding Fines <span style="color:#d97706;">({{ $pendingFines }})</span></span>
            </div>
        </div>
    </div>

    {{-- Today's Attendance Overview + Quick Actions --}}
    <div class="content-grid">
        <div class="card">
            <div class="card-header">
                <h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">today</i> Today's Attendance Overview</h3>
                <span class="badge">{{ $todayStr }}</span>
            </div>
            <div class="att-overview">
                <div class="att-item checkin">
                    <div class="num">{{ $checkedIn }}</div>
                    <div class="lbl">Checked In</div>
                </div>
                <div class="att-item checkout">
                    <div class="num">{{ $checkedOut }}</div>
                    <div class="lbl">Checked Out</div>
                </div>
                <div class="att-item absent">
                    <div class="num">{{ max(0, $totalChildren - $checkedIn - $checkedOut) }}</div>
                    <div class="lbl">Not Yet</div>
                </div>
            </div>
            @php $pct = $totalChildren > 0 ? round(($checkedIn / $totalChildren) * 100) : 0; @endphp
            <div class="progress-wrap">
                <div class="progress-label"><span>Check-in Progress</span><span>{{ $checkedIn }}/{{ $totalChildren }} ({{ $pct }}%)</span></div>
                <div class="progress-bar"><div class="progress-fill green" style="width:{{ $pct }}%"></div></div>
            </div>
            @php $outPct = $totalChildren > 0 ? round(($checkedOut / $totalChildren) * 100) : 0; @endphp
            <div class="progress-wrap">
                <div class="progress-label"><span>Check-out Progress</span><span>{{ $checkedOut }}/{{ $totalChildren }} ({{ $outPct }}%)</span></div>
                <div class="progress-bar"><div class="progress-fill blue" style="width:{{ $outPct }}%"></div></div>
            </div>

            {{-- Weekly Chart --}}
            <div class="card-header" style="margin-top:16px;">
                <h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">bar_chart</i> Weekly Attendance</h3>
            </div>
            <div class="chart-wrap"><canvas id="weeklyChart"></canvas></div>
        </div>

        {{-- Right Side --}}
        <div>
            {{-- Quick Actions --}}
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">bolt</i> Quick Actions</h3></div>
                <div class="quick-actions">
                    <a href="{{ route('attendance.create') }}" class="qa-btn">
                        <i class="material-symbols-rounded">edit_note</i> Take Attendance
                    </a>
                    <a href="{{ route('children.create') }}" class="qa-btn">
                        <i class="material-symbols-rounded">person_add</i> Add Child
                    </a>
                    <a href="{{ route('parents.create') }}" class="qa-btn">
                        <i class="material-symbols-rounded">group_add</i> Add Parent
                    </a>
                    <a href="{{ route('attendance.calendar') }}" class="qa-btn">
                        <i class="material-symbols-rounded">calendar_month</i> Calendar
                    </a>
                    <a href="{{ route('qr.code') }}" class="qa-btn">
                        <i class="material-symbols-rounded">qr_code_2</i> QR Code
                    </a>
                    <a href="{{ route('simulation.dashboard') }}" class="qa-btn">
                        <i class="material-symbols-rounded">timer</i> Timer
                    </a>
                    <a href="{{ route('penalties.fines') }}" class="qa-btn">
                        <i class="material-symbols-rounded">gavel</i> Fines
                    </a>
                </div>
            </div>

            {{-- Classroom Occupancy --}}
            <div class="card">
                <div class="card-header"><h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">door_open</i> Classroom Occupancy</h3></div>
                <div class="classroom-cards">
                    @foreach($classrooms as $classroom)
                    @php
                        $filled = $classroom->children_count;
                        $cap = $classroom->capacity ?? 15;
                        $fillPct = $cap > 0 ? round(($filled / $cap) * 100) : 0;
                        $barColor = $fillPct > 80 ? '#dc2626' : ($fillPct > 50 ? '#fb8c00' : '#43a047');
                    @endphp
                    <div class="classroom-card" onclick="location='{{ route('classrooms.show', $classroom->id) }}'">
                        <div class="stat-icon-wrap" style="width:36px;height:36px;border-radius:10px;font-size:16px;background:#f1f5f9;color:#64748b;">
                            <i class="material-symbols-rounded">meeting_room</i>
                        </div>
                        <div class="cc-bar-wrap">
                            <div class="cc-name">{{ $classroom->name }}</div>
                            <div class="cc-bar"><div class="cc-fill" style="width:{{ $fillPct }}%;background:{{ $barColor }};"></div></div>
                        </div>
                        <div class="cc-count">{{ $filled }}/{{ $cap }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Check-ins --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">history</i> Recent Check-ins Today</h3>
            <a href="{{ route('attendance.index') }}" style="font-size:12px;font-weight:700;color:#FF6B6B;text-decoration:none;">View All &rarr;</a>
        </div>
        <div class="checkin-list">
            @forelse($recentCheckins as $att)
            <div class="checkin-item">
                <div class="ci-avatar">
                    @if($att->child && $att->child->photo)
                        <img src="{{ asset('storage/'.$att->child->photo) }}">
                    @else
                        {{ strtoupper(substr($att->child->name ?? '?', 0, 1)) }}
                    @endif
                </div>
                <div class="ci-info">
                    <div class="ci-name">{{ $att->child->name ?? 'Unknown' }}</div>
                    <div class="ci-sub">{{ $att->child->classroom->name ?? 'N/A' }}</div>
                </div>
                <div class="ci-time {{ in_array($att->status, ['late','late_checkout']) ? 'late' : '' }}">
                    {{ $att->checkin_time ? \Carbon\Carbon::parse($att->checkin_time)->format('h:i A') : '--:--' }}
                    @if($att->status == 'late') <i class="fas fa-exclamation-triangle"></i> @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:#94a3b8;">
                <i class="material-symbols-rounded" style="font-size:40px;display:block;margin-bottom:8px;">inbox</i>
                No check-ins today yet
            </div>
            @endforelse
        </div>
    </div>

    {{-- Penalty Summary --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h3><i class="material-symbols-rounded" style="font-size:18px;vertical-align:middle;">gavel</i> Late Pickup Fines</h3>
            <a href="{{ route('penalties.fines') }}" style="font-size:12px;font-weight:700;color:#d97706;text-decoration:none;">Manage &rarr;</a>
        </div>
        <div class="att-overview">
            <div class="att-item absent">
                <div class="num">RM {{ number_format($totalOutstanding, 0) }}</div>
                <div class="lbl">Outstanding</div>
            </div>
            <div class="att-item checkout">
                <div class="num">RM {{ number_format($totalCollectedAmount, 0) }}</div>
                <div class="lbl">Collected</div>
            </div>
            <div class="att-item checkin">
                <div class="num">{{ $paidFines }}</div>
                <div class="lbl">Paid</div>
            </div>
        </div>
        <div class="progress-wrap">
            <div class="progress-label"><span>Collection Rate</span><span>{{ $totalFines > 0 ? round(($paidFines / $totalFines) * 100) : 0 }}% ({{ $paidFines }}/{{ $totalFines }})</span></div>
            <div class="progress-bar"><div class="progress-fill green" style="width:{{ $totalFines > 0 ? round(($paidFines / $totalFines) * 100) : 0 }}%"></div></div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var weekCtx = document.getElementById('weeklyChart');
    if (!weekCtx) return;
    weekCtx = weekCtx.getContext('2d');
    new Chart(weekCtx, {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri'],
            datasets: [{
                label: 'Check-ins',
                data: @json($weekData),
                borderColor: '#6d28d9',
                backgroundColor: 'rgba(109,40,217,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6d28d9',
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endsection
