@extends('layouts.template')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<style>
    .dashboard-container {
        padding: 0;
    }

    /* Welcome Section */
    .welcome-section {
        margin-bottom: 28px;
    }

    .welcome-section h1 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .welcome-section p {
        font-size: 13px;
        color: #94a3b8;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border: 1px solid #FFF0EC;
        transition: transform .2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #FFF5F2;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .stat-info h3 {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .stat-info p {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        margin: 0;
    }

    /* Main Content Layout */
    .main-dashboard {
        display: flex;
        gap: 24px;
    }

    .left-panel {
        flex: 2;
    }

    .right-panel {
        flex: 1;
    }

    /* Live Monitoring Table */
    .monitoring-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border: 1px solid #FFF0EC;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #FFF0EC;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .live-badge {
        background: #fef2f2;
        color: #ef4444;
        font-size: 10px;
        padding: 4px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }

    .refresh-btn {
        background: #FFF5F2;
        border: none;
        border-radius: 12px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #FF6B6B;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: .2s;
    }

    .refresh-btn:hover {
        background: #FFE4D6;
    }

    .monitoring-table {
        width: 100%;
        border-collapse: collapse;
    }

    .monitoring-table th {
        text-align: left;
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        background: #FFFAF9;
        border-bottom: 1px solid #FFF0EC;
    }

    .monitoring-table td {
        padding: 14px 20px;
        font-size: 13px;
        color: #475569;
        border-bottom: 1px solid #FFF0EC;
        vertical-align: middle;
    }

    .monitoring-table tr:last-child td {
        border-bottom: none;
    }

    .monitoring-table tr:hover {
        background: #FFFAF9;
        cursor: pointer;
    }

    .child-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .child-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 14px;
        overflow: hidden;
    }

    .child-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .child-name {
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .child-class {
        font-size: 11px;
        color: #94a3b8;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-checked-in {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-checked-out {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-absent {
        background: #fffbeb;
        color: #d97706;
    }

    .fee-paid {
        color: #16a34a;
        font-weight: 700;
    }

    .fee-unpaid {
        color: #dc2626;
        font-weight: 700;
    }

    .action-icon {
        color: #FF6B6B;
        cursor: pointer;
        font-size: 18px;
    }

    /* Right Panel Cards */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        border: 1px solid #FFF0EC;
        margin-bottom: 24px;
    }

    .weather-card {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
    }

    .weather-card .info-label {
        color: rgba(255,255,255,0.8);
    }

    .weather-card .temp {
        font-size: 36px;
        font-weight: 800;
    }

    .notifications-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #FFF0EC;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #FFF5F2;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }

    .notification-time {
        font-size: 10px;
        color: #94a3b8;
    }

    .sync-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .sync-btn {
        flex: 1;
        background: #FFF5F2;
        border: none;
        border-radius: 12px;
        padding: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #FF6B6B;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: .2s;
    }

    .sync-btn:hover {
        background: #FFE4D6;
    }

    @media (max-width: 992px) {
        .main-dashboard {
            flex-direction: column;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="dashboard-container">

    {{-- Welcome Section --}}
    <div class="welcome-section">
        <h1>Admin Dashboard</h1>
        <p>{{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👶</div>
            <div class="stat-info">
                <h3>{{ \App\Models\Child::count() }}</h3>
                <p>Little Blossoms</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👨‍👩‍👧‍👦</div>
            <div class="stat-info">
                <h3>{{ \App\Models\ParentModel::count() }}</h3>
                <p>Loving Guardians</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👩‍🏫</div>
            <div class="stat-info">
                <h3>{{ \App\Models\Teacher::count() }}</h3>
                <p>Nurturing Team</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏫</div>
            <div class="stat-info">
                <h3>{{ \App\Models\Classroom::count() }}</h3>
                <p>Classrooms</p>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="main-dashboard">

        {{-- LEFT PANEL - Live Monitoring --}}
        <div class="left-panel">
            <div class="monitoring-card">
                <div class="card-header">
                    <h3>
                        <span>🔄</span> Live Monitoring · Today's Check-in / Check-out
                        <span class="live-badge">
                            <span>●</span> LIVE
                        </span>
                    </h3>
                    <button class="refresh-btn" onclick="location.reload()">
                        <span>🔄</span> Refresh
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="monitoring-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Child</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Fee</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Sample data - replace with actual attendance data
                                $attendances = [
                                    ['id' => 1, 'name' => 'Aurora Meilani', 'class' => 'Nursery 1', 'status' => 'checked-in', 'checkin' => '08:41 AM', 'checkout' => '-', 'fee' => 'paid'],
                                    ['id' => 2, 'name' => 'Elias Bramantya', 'class' => 'Toddler A', 'status' => 'checked-out', 'checkin' => '08:04 AM', 'checkout' => '03:59 PM', 'fee' => 'paid'],
                                    ['id' => 3, 'name' => 'Zahra Kirana', 'class' => 'Pre-School', 'status' => 'checked-in', 'checkin' => '08:31 AM', 'checkout' => '-', 'fee' => 'paid'],
                                    ['id' => 4, 'name' => 'Muhammad Danish', 'class' => 'Nursery 2', 'status' => 'absent', 'checkin' => '-', 'checkout' => '-', 'fee' => 'unpaid'],
                                    ['id' => 5, 'name' => 'Sofia Aleesya', 'class' => 'Toddler B', 'status' => 'checked-in', 'checkin' => '09:15 AM', 'checkout' => '-', 'fee' => 'paid'],
                                ];
                            @endphp
                            @foreach($attendances as $i => $attendance)
                            <tr onclick="window.location='{{ route('children.show', $attendance['id']) }}'">
                                <td style="color:#94a3b8; font-weight:700;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="child-info">
                                        <div class="child-avatar">
                                            {{ strtoupper(substr($attendance['name'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="child-name">{{ $attendance['name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="child-class">{{ $attendance['class'] }}</span></td>
                                <td>
                                    @if($attendance['status'] == 'checked-in')
                                        <span class="status-badge status-checked-in">✅ Checked-in</span>
                                    @elseif($attendance['status'] == 'checked-out')
                                        <span class="status-badge status-checked-out">📤 Checked-out</span>
                                    @else
                                        <span class="status-badge status-absent">❌ Absent</span>
                                    @endif
                                </td>
                                <td>{{ $attendance['checkin'] }}</td>
                                <td>{{ $attendance['checkout'] }}</td>
                                <td>
                                    @if($attendance['fee'] == 'paid')
                                        <span class="fee-paid">✓ Paid</span>
                                    @else
                                        <span class="fee-unpaid">✗ Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="action-icon">👁️</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="right-panel">

            {{-- Weather Card --}}
            <div class="info-card weather-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="temp">32°C</div>
                        <div class="info-label">Sunny</div>
                    </div>
                    <div style="font-size: 48px;">☀️</div>
                </div>
                <div class="info-label" style="margin-top: 12px;">
                    📍 Kuala Lumpur, Malaysia
                </div>
            </div>

            {{-- Notifications Card --}}
            <div class="info-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 0;">🔔 Notifications</h3>
                    <span class="live-badge" style="background:#FFF5F2; color:#FF6B6B;">3 new</span>
                </div>

                <div class="notifications-list">
                    <div class="notification-item">
                        <div class="notification-icon">👶</div>
                        <div class="notification-content">
                            <div class="notification-title">New child registered</div>
                            <div class="notification-time">5 minutes ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon">✅</div>
                        <div class="notification-content">
                            <div class="notification-title">Aurora checked in</div>
                            <div class="notification-time">15 minutes ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon">📤</div>
                        <div class="notification-content">
                            <div class="notification-title">Elias checked out</div>
                            <div class="notification-time">1 hour ago</div>
                        </div>
                    </div>
                </div>

                {{-- Sync Buttons --}}
                <div class="sync-buttons">
                    <button class="sync-btn" onclick="location.reload()">
                        <span>🔄</span> Live Sync
                    </button>
                    <button class="sync-btn" onclick="location.reload()">
                        <span>🔃</span> Refresh
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
</script>

@endsection