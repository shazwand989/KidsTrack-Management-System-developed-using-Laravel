<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Parent Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f4f6f9;
        }

        .sidebar {
            position: fixed;
            width: 250px;
            height: 100vh;
            background: #F28C28;
            color: white;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }

        .sidebar a.active {
            background: rgba(255,255,255,0.3);
            font-weight: bold;
        }

        .content {
            margin-left: 270px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }

        .header h2 {
            color: #2d3748;
            margin-bottom: 5px;
        }

        .header p {
            color: #718096;
        }

        .badge-verified {
            display: inline-block;
            background: #48bb78;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .badge-emergency {
            display: inline-block;
            background: #fc8181;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .badge-second {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .badge-guardian {
            display: inline-block;
            background: #9f7aea;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }

        .card h3 {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .card h2 {
            color: #2d3748;
            font-size: 28px;
        }

        .scan-btn {
            display: inline-block;
            background: linear-gradient(135deg, #F28C28, #f59e0b);
            color: white;
            padding: 14px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(242, 140, 40, 0.3);
        }

        .scan-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(242, 140, 40, 0.4);
        }

        .scan-btn .icon {
            font-size: 20px;
            margin-right: 8px;
        }

        .section {
            background: white;
            margin-top: 20px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
        }

        .section h3 {
            color: #2d3748;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border-bottom: 1px solid #eee;
            padding: 12px;
            text-align: left;
        }

        table th {
            color: #4a5568;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #a0aec0;
        }

        .empty-state .icon {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }

        .child-avatar {
            display: inline-block;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #F28C28;
            color: white;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            margin-right: 10px;
        }

        /* ============================================
           🔥 STATUS BADGE - LENGKAP
           ============================================ */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-present {
            background: #c6f6d5;
            color: #276749;
        }

        .status-checkedin {
            background: #c6f6d5;
            color: #276749;
        }

        .status-checkout {
            background: #fef3c7;
            color: #92400e;
        }

        .status-checkedout {
            background: #fef3c7;
            color: #92400e;
        }

        .status-late_checkout {
            background: #fef3c7;
            color: #92400e;
        }

        .status-late {
            background: #feebc8;
            color: #9c6b1e;
        }

        .status-absent {
            background: #fed7d7;
            color: #9b2c2c;
        }

        .status-pending {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-container {
            text-align: center;
            margin: 15px 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
                padding: 15px;
            }
            .cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>
<body>

<div class="sidebar">
    <h2>🧸 KidsTrack</h2>
    <a href="{{ route('parent.dashboard') }}" class="active">🏠 Dashboard</a>
    <a href="{{ route('parent.children') }}">👶 My Children</a>
    <a href="{{ route('parent.attendance') }}">📅 Attendance</a>
    <a href="{{ route('kiosk.index') }}">📱 Kiosk</a>
    <a href="{{ route('parent.notifications') }}">🔔 Notifications</a>
    <a href="{{ route('parent.payment') }}">💳 Payment</a>
    <a href="{{ route('parent.fine') }}">⚠️ Fine</a>
    <a href="{{ route('parent.profile') }}">👤 Profile</a>
    <a href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
       🚪 Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
</div>

<div class="content">

    {{-- HEADER / WELCOME --}}
    <div class="header">
        {{-- PARENT --}}
        @if($parent)
            <h2>
                👋 Welcome, {{ $parent->name }}
                @if($parent->verified)
                    <span class="badge-verified">✅ Verified</span>
                @endif
                @if($parent->emergency)
                    <span class="badge-emergency">🚨 Emergency</span>
                @endif
            </h2>
            <p>📞 {{ $parent->phone }}</p>
            <p style="font-size:14px; color:#a0aec0; margin-top:5px;">
                🆔 Parent ID: #{{ str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}
            </p>

        {{-- SECOND PARENT --}}
        @elseif($secondParent)
            <h2>
                👋 Welcome, {{ $secondParent->name }}
                <span class="badge-second">👫 Second Parent</span>
            </h2>
            <p>📞 {{ $secondParent->phone }}</p>
            <p style="font-size:14px; color:#a0aec0; margin-top:5px;">
                🆔 Second Parent ID: #{{ str_pad($secondParent->id, 4, '0', STR_PAD_LEFT) }}
            </p>

        {{-- GUARDIAN --}}
        @elseif($guardian)
            <h2>
                👋 Welcome, {{ $guardian->name }}
                <span class="badge-guardian">🛡️ Guardian</span>
            </h2>
            <p>📞 {{ $guardian->phone }}</p>
            <p style="font-size:14px; color:#a0aec0; margin-top:5px;">
                🆔 Guardian ID: #{{ str_pad($guardian->id, 4, '0', STR_PAD_LEFT) }}
            </p>

        @else
            <h2>👋 Welcome, Parent</h2>
            <p>Please complete your profile.</p>
        @endif
    </div>

    {{-- STATS CARDS --}}
    <div class="cards">
        <div class="card">
            <h3>👶 Children</h3>
            <h2>{{ $children->count() ?? 0 }}</h2>
        </div>
        <div class="card">
            <h3>📅 Attendance Today</h3>
            <h2>{{ $attendanceToday ?? 0 }}</h2>
        </div>
        <div class="card">
            <h3>💳 Invoice</h3>
            <h2>RM0.00</h2>
        </div>
        <div class="card">
            <h3>🔔 Notification</h3>
            <h2>0</h2>
        </div>
    </div>

    {{-- SCAN QR CODE BUTTON --}}
    <div class="btn-container">
        <a href="{{ route('kiosk.index') }}" class="scan-btn">
            <span class="icon">📷</span> Scan QR Code
        </a>
    </div>

    {{-- MY CHILDREN SECTION --}}
    <div class="section">
        <h3>👶 My Children</h3>
        <br>

        @if($children && $children->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Status Today</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $index => $child)
                        @php
                            // 🔥🔥🔥 STATUS LOGIC - BETUL 🔥🔥🔥
                            $today = \Carbon\Carbon::now('Asia/Kuala_Lumpur')->toDateString();
                            $attendance = \App\Models\Attendance::where('child_id', $child->id)
                                ->whereDate('date', $today)
                                ->first();
                            
                            if ($attendance) {
                                if ($attendance->checkout_time || $attendance->status === 'checkout' || $attendance->status === 'late_checkout') {
                                    $statusText = 'Checked Out';
                                    $statusClass = 'status-checkedout';
                                } elseif ($attendance->checkin_time || $attendance->status === 'present' || $attendance->status === 'late') {
                                    $statusText = 'Checked In';
                                    $statusClass = 'status-checkedin';
                                } else {
                                    $statusText = 'Pending';
                                    $statusClass = 'status-pending';
                                }
                            } else {
                                $statusText = 'Pending';
                                $statusClass = 'status-pending';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</span>
                                {{ $child->name }}
                            </td>
                            <td>{{ $child->classroom->name ?? 'Not assigned' }}</td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <span class="icon">👶</span>
                <p>No Child Registered Yet</p>
                <p style="font-size:14px; color:#cbd5e0;">Add your child to get started.</p>
            </div>
        @endif

    </div>

</div>

</body>
</html>