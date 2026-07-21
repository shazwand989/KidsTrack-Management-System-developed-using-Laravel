<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>KidsTrack - Check In Another Child</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: #f3f4f6;
            transition: background 0.5s ease;
        }

        body.main-parent { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        body.second-parent { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body.guardian { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
        body.admin { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }

        .add-card {
            background: white;
            border-radius: 30px;
            padding: 40px 35px;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .add-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            transition: all 0.5s ease;
        }
        .main-parent .add-card::before { background: linear-gradient(90deg, #059669, #10b981); }
        .second-parent .add-card::before { background: linear-gradient(90deg, #6d28d9, #8b5cf6); }
        .guardian .add-card::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
        .admin .add-card::before { background: linear-gradient(90deg, #334155, #475569); }

        .add-card .logo { font-size: 64px; margin-bottom: 10px; }
        .add-card h1 { font-size: 24px; color: #1f2937; margin-bottom: 5px; }
        .add-card .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 20px; }

        .role-badge {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        .role-badge.main-parent { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .role-badge.second-parent { background: #ede9fe; color: #5b21b6; border: 2px solid #8b5cf6; }
        .role-badge.guardian { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
        .role-badge.admin { background: #e2e8f0; color: #1e293b; border: 2px solid #475569; }

        .role-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 20px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .role-info .role-icon { font-size: 24px; }
        .role-info .role-name { font-weight: 700; }
        .role-info .role-name.main { color: #059669; }
        .role-info .role-name.second { color: #6d28d9; }
        .role-info .role-name.guardian { color: #d97706; }
        .role-info .role-name.admin { color: #1e293b; }

        .checked-in-list-container {
            margin: 15px 0;
            padding: 15px;
            background: #ecfdf5;
            border-radius: 16px;
            border: 2px solid #6ee7b7;
            text-align: left;
        }
        .checked-in-list-container .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .checked-in-list-container .header .title { font-weight: 700; color: #065f46; font-size: 15px; }
        .checked-in-list-container .header .count-badge {
            background: #065f46;
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .checked-in-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 4px;
        }
        .checked-in-item.current {
            border: 2px solid #f59e0b;
            background: #fffbeb;
        }
        .checked-in-item .left { display: flex; align-items: center; gap: 10px; }
        .checked-in-item .avatar-small {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #059669;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .checked-in-item .avatar-small.current-avatar { background: #f59e0b; }
        .checked-in-item .name { font-weight: 600; font-size: 14px; }
        .checked-in-item .class { font-size: 12px; color: #6b7280; }
        .checked-in-item .time { font-size: 11px; color: #059669; font-weight: 600; }
        .checked-in-item .current-badge {
            font-size: 10px;
            background: #fef3c7;
            color: #92400e;
            padding: 1px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .checked-in-empty { text-align: center; color: #94a3b8; padding: 10px 0; font-size: 14px; }
        .checked-in-list-scroll { max-height: 150px; overflow-y: auto; }
        .checked-in-list-scroll::-webkit-scrollbar { width: 4px; }
        .checked-in-list-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .checked-in-list-scroll::-webkit-scrollbar-thumb { background: #6ee7b7; border-radius: 10px; }

        .select-all {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 16px;
            background: #f3f4f6;
            border-radius: 12px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .select-all:hover { background: #e5e7eb; }
        .select-all .left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        .select-all .checkbox-all {
            width: 22px;
            height: 22px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            background: white;
            flex-shrink: 0;
        }
        .select-all.selected .checkbox-all {
            background: #6d28d9;
            border-color: #6d28d9;
        }
        .select-all.selected .checkbox-all::after {
            content: '✓';
            color: white;
            font-size: 14px;
            font-weight: 700;
        }

        .child-list {
            margin: 15px 0;
            text-align: left;
            max-height: 300px;
            overflow-y: auto;
        }
        .child-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        .child-list-item:hover:not(.disabled) {
            background: #f1f5f9;
            border-color: #c4b5fd;
        }
        .child-list-item.selected {
            background: #ede9fe;
            border-color: #6d28d9;
        }
        .child-list-item.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f3f4f6;
        }
        .child-list-item .info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        .child-list-item .info .checkbox {
            width: 22px;
            height: 22px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            flex-shrink: 0;
            background: white;
        }
        .child-list-item.selected .info .checkbox {
            background: #6d28d9;
            border-color: #6d28d9;
        }
        .child-list-item.selected .info .checkbox::after {
            content: '✓';
            color: white;
            font-size: 14px;
            font-weight: 700;
        }
        .child-list-item .info .details .name { font-weight: 600; color: #1f2937; }
        .child-list-item .info .details .class { font-size: 12px; color: #6b7280; }
        .child-list-item .badge {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .badge-available { background: #dbeafe; color: #1e40af; }
        .badge-checked { background: #d1fae5; color: #065f46; }
        .badge-checkout-done { background: #fef3c7; color: #92400e; }

        .btn-checkin {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
        }
        .btn-checkin:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4);
        }
        .btn-checkin:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-next {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        .btn-next-secondary {
            width: 100%;
            padding: 14px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-next-secondary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-back {
            width: 100%;
            padding: 14px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-back:hover { background: #e5e7eb; }

        .no-children {
            color: #94a3b8;
            font-size: 14px;
            padding: 30px 0;
            text-align: center;
        }
        .selected-count {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }
        .all-checked-in-message {
            margin: 15px 0;
            padding: 16px;
            background: #d1fae5;
            border-radius: 12px;
            color: #065f46;
            font-weight: 600;
            border: 2px solid #10b981;
        }
        .all-checked-in-message .icon { font-size: 32px; display: block; margin-bottom: 8px; }

        .child-profile-box {
            background: #f3f4f6;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            border: 3px solid #e5e7eb;
        }
        .child-profile-box.main-parent-border { border-color: #10b981; background: #f0fdf4; }
        .child-profile-box.second-parent-border { border-color: #8b5cf6; background: #f5f3ff; }
        .child-profile-box.guardian-border { border-color: #f59e0b; background: #fffbeb; }
        .child-profile-box.admin-border { border-color: #475569; background: #f1f5f9; }

        .child-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }
        .child-avatar.main-parent-avatar { background: linear-gradient(135deg, #059669, #10b981); }
        .child-avatar.second-parent-avatar { background: linear-gradient(135deg, #6d28d9, #8b5cf6); }
        .child-avatar.guardian-avatar { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .child-avatar.admin-avatar { background: linear-gradient(135deg, #334155, #475569); }

        .child-name { font-size: 22px; font-weight: 700; color: #1f2937; }
        .child-class { font-size: 14px; color: #6b7280; margin-top: 4px; }

        .current-child-tag {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        .current-child-tag.main-parent-tag { background: #d1fae5; color: #065f46; }
        .current-child-tag.second-parent-tag { background: #ede9fe; color: #5b21b6; }
        .current-child-tag.guardian-tag { background: #fef3c7; color: #92400e; }
        .current-child-tag.admin-tag { background: #e2e8f0; color: #1e293b; }

        .fade-in { animation: fadeIn 0.5s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .child-list::-webkit-scrollbar { width: 4px; }
        .child-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .child-list::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

        .button-divider {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .button-divider .btn-next-secondary {
            flex: 1;
            margin-top: 0;
        }
        .button-divider .btn-back {
            flex: 1;
            margin-top: 0;
        }

        /* 🔥 CSS UNTUK POPUP BULK CHECK-IN */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }
        .popup-overlay.active { display: flex; }

        .popup-overlay div::-webkit-scrollbar { width: 4px; }
        .popup-overlay div::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .popup-overlay div::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }
    </style>
</head>

<body class="{{ $roleData['class'] ?? 'main-parent' }}">
    <div class="add-card">
        <div class="logo">👶</div>

        <!-- ============================================ -->
        <!-- TITLE - BERUBAH MENGIKUT STATUS              -->
        <!-- ============================================ -->
        @php
            $totalCheckedIn = isset($allCheckedInData) ? count($allCheckedInData) : 0;
            $totalChildren = isset($totalChildren) ? $totalChildren : 0;
            $allCheckedIn = ($totalCheckedIn > 0 && $totalCheckedIn == $totalChildren);
            $childDone = $childCheckedIn && $childCheckedOut;
        @endphp

        @if($childDone)
            <h1>👶 Attendance Completed</h1>
            <p class="subtitle">Rekod kehadiran untuk hari ini</p>
        @elseif($allCheckedIn && $totalChildren > 0)
            @if(isset($attendanceSummary) && $attendanceSummary['checkin']['status'] === 'late')
                <h1>⚠️ Checked In Late</h1>
                <p class="subtitle" style="color:#c62828;">{{ \App\Services\AttendanceSummaryService::formatDuration($attendanceSummary['checkin']['minutes_diff']) }} lewat dari jadual ({{ $attendanceSummary['schedule']['morning_end'] }})</p>
            @else
                <h1>🎉 All Checked In!</h1>
                <p class="subtitle">Semua anak telah berjaya check-in hari ini</p>
            @endif
        @elseif(isset($availableChildren) && count($availableChildren) > 0)
            <h1>👨‍👩‍👧 Check In Another Child</h1>
            <p class="subtitle">Pilih anak untuk check-in</p>
        @else
            <h1>👋 Check In</h1>
            <p class="subtitle">Imbas QR Code untuk check-in anak</p>
        @endif

        <!-- ROLE BADGE (compact) -->
        <div class="role-badge {{ $roleData['badge_class'] ?? 'main-parent' }}">
            {{ $roleData['badge_text'] ?? '👨‍👩‍👦 Main Parent' }}
        </div>

        @if($childDone)
        <!-- ============================================ -->
        <!-- COMPLETED SUMMARY (CHECK-IN + CHECK-OUT DONE) -->
        <!-- ============================================ -->
        @php
            $s = $attendanceSummary ?? null;
            $cin = $s['checkin'] ?? null;
            $cout = $s['checkout'] ?? null;
            $sch = $s['schedule'] ?? null;
        @endphp
        <div style="background:#f8fafc;border-radius:16px;padding:20px;margin:10px 0;text-align:left;">
            <!-- Child Info -->
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#059669,#10b981);color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;flex-shrink:0;">{{ strtoupper(substr($currentChild->name, 0, 1)) }}</div>
                <div>
                    <div style="font-weight:700;font-size:15px;color:#1e293b;">{{ $currentChild->name }}</div>
                    <div style="font-size:13px;color:#64748b;">🏫 {{ $currentChild->classroom->name ?? '-' }}</div>
                </div>
            </div>

            <!-- Attendance Table -->
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="text-align:left;padding:8px 6px;color:#94a3b8;font-size:10px;font-weight:700;text-transform:uppercase;"></th>
                        <th style="text-align:center;padding:8px 6px;color:#94a3b8;font-size:10px;font-weight:700;text-transform:uppercase;">Scheduled</th>
                        <th style="text-align:center;padding:8px 6px;color:#94a3b8;font-size:10px;font-weight:700;text-transform:uppercase;">Actual</th>
                        <th style="text-align:center;padding:8px 6px;color:#94a3b8;font-size:10px;font-weight:700;text-transform:uppercase;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Check-in Row -->
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 6px;font-weight:700;color:#1e293b;">Check-in</td>
                        <td style="padding:10px 6px;text-align:center;color:#64748b;">{{ $sch['morning_end'] ?? '07:30' }}</td>
                        <td style="padding:10px 6px;text-align:center;font-weight:700;color:#1e293b;">{{ $cin['time'] ?? '—' }}</td>
                        <td style="padding:10px 6px;text-align:center;">
                            <span style="display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;
                                @if($cin['status'] === 'on_time') background:#e8f5e9;color:#2e7d32;
                                @elseif($cin['status'] === 'late') background:#fff3e0;color:#e65100;
                                @else background:#fce4ec;color:#c62828; @endif">
                                @if($cin['status'] === 'on_time') 🟢 On Time
                                @elseif($cin['status'] === 'late') 🟡 Late
                                @else 🔴 {{ $cin['status_label'] }}
                                @endif
                            </span>
                        </td>
                    </tr>
                    <!-- Check-out Row -->
                    <tr>
                        <td style="padding:10px 6px;font-weight:700;color:#1e293b;">Check-out</td>
                        <td style="padding:10px 6px;text-align:center;color:#64748b;">{{ $sch['class_end'] ?? $sch['evening_end'] ?? '17:00' }}</td>
                        <td style="padding:10px 6px;text-align:center;font-weight:700;color:#1e293b;">{{ $cout['time'] ?? '—' }}</td>
                        <td style="padding:10px 6px;text-align:center;">
                            <span style="display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;
                                @if($cout['status'] === 'on_time') background:#e8f5e9;color:#2e7d32;
                                @elseif($cout['status'] === 'early') background:#fce4ec;color:#c62828;
                                @elseif($cout['status'] === 'late') background:#fff3e0;color:#e65100;
                                @else background:#f3e8ff;color:#6d28d9; @endif">
                                @if($cout['status'] === 'on_time') 🟢 On Time
                                @elseif($cout['status'] === 'early') 🔴 Early
                                @elseif($cout['status'] === 'late') 🟡 Late
                                @else 🟣 {{ $cout['status_label'] }}
                                @endif
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Class Schedule -->
            <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap;">
                <div style="flex:1;min-width:80px;background:#f0fdf4;border-radius:10px;padding:10px;text-align:center;font-size:12px;">
                    <div style="color:#059669;font-weight:700;">🟢 Masuk</div>
                    <div style="font-weight:800;color:#1e293b;">{{ $sch['class_start'] ? \Carbon\Carbon::parse($sch['class_start'])->format('h:i A') : '—' }}</div>
                </div>
                <div style="flex:1;min-width:80px;background:#f5f3ff;border-radius:10px;padding:10px;text-align:center;font-size:12px;">
                    <div style="color:#6d28d9;font-weight:700;">🟣 Balik</div>
                    <div style="font-weight:800;color:#1e293b;">{{ $sch['class_end'] ? \Carbon\Carbon::parse($sch['class_end'])->format('h:i A') : '—' }}</div>
                </div>
            </div>

            <!-- Confirmation -->
            <div style="text-align:center;margin-top:16px;padding:10px;background:#e8f5e9;border-radius:10px;color:#2e7d32;font-size:13px;font-weight:600;">
                ✅ Attendance has been recorded successfully.
            </div>
        </div>

        @else
        <!-- ============================================ -->
        <!-- ANAK YANG SUDAH CHECK IN (only if any)      -->
        <!-- ============================================ -->
        @if($totalCheckedIn > 0)
        <div class="checked-in-list-container">
            <div class="header">
                <span class="title">✅ Already Checked In</span>
                <span class="count-badge">{{ $totalCheckedIn }} anak</span>
            </div>
            <div class="checked-in-list-scroll">
                @foreach($allCheckedInData as $checkedChild)
                    <div class="checked-in-item {{ isset($checkedChild['is_current']) && $checkedChild['is_current'] ? 'current' : '' }}">
                        <div class="left">
                            <div class="avatar-small {{ isset($checkedChild['is_current']) && $checkedChild['is_current'] ? 'current-avatar' : '' }}">
                                {{ $checkedChild['initial'] ?? '?' }}
                            </div>
                            <div>
                                <div class="name">{{ $checkedChild['name'] ?? 'Unknown' }}</div>
                                <div class="class">🏫 {{ $checkedChild['classroom'] ?? '-' }}
                                    @if(!empty($checkedChild['class_start']) && !empty($checkedChild['class_end']))
                                        <span style="font-size:10px;color:#64748b;display:block;">
                                            🟢 {{ \Carbon\Carbon::parse($checkedChild['class_start'])->format('h:i A') }} — 🟣 {{ \Carbon\Carbon::parse($checkedChild['class_end'])->format('h:i A') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if(isset($checkedChild['is_current']) && $checkedChild['is_current'])
                                <span class="current-badge">⭐ Current</span>
                            @endif
                            <div style="text-align:right;">
                                <span class="time">✅ {{ $checkedChild['check_in_time'] ?? 'Checked In' }}</span>
                                @if(isset($checkedChild['is_current']) && $checkedChild['is_current'] && isset($attendanceSummary) && $attendanceSummary['checkin']['status'] === 'late')
                                    <div style="color:#c62828;font-size:11px;font-weight:700;margin-top:2px;">
                                        ⚠️ {{ \App\Services\AttendanceSummaryService::formatDuration($attendanceSummary['checkin']['minutes_diff']) }} lewat
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- ============================================ -->
        <!-- CURRENT CHILD (DARI QR SCAN)                 -->
        <!-- ============================================ -->
        @if(isset($currentChild) && $currentChild)
            <div class="child-profile-box {{ $roleData['border_class'] ?? 'main-parent-border' }}">
                <div class="child-avatar {{ $roleData['avatar_class'] ?? 'main-parent-avatar' }}">
                    {{ strtoupper(substr($currentChild->name, 0, 1)) }}
                </div>
                <div class="child-name">⭐ {{ $currentChild->name }}</div>
                <div class="child-class">🏫 {{ $currentChild->classroom->name ?? 'Tiada kelas' }}</div>
                <div class="current-child-tag {{ $roleData['tag_class'] ?? 'main-parent-tag' }}">
                    @if($childDone)
                        ✅ Selesai (Check-in & Check-out)
                    @elseif($childCheckedIn)
                        ✅ Already Checked In
                        @if(isset($attendanceSummary) && $attendanceSummary['checkin']['status'] === 'late')
                            <span style="color:#c62828;font-weight:700;display:block;margin-top:4px;">
                                ⚠️ {{ \App\Services\AttendanceSummaryService::formatDuration($attendanceSummary['checkin']['minutes_diff']) }} lewat
                            </span>
                        @endif
                    @else
                        ⏳ Belum Check-in
                    @endif
                </div>
            </div>
        @endif

        <!-- ============================================ -->
        <!-- CLASS SCHEDULE                               -->
        <!-- ============================================ -->
        @if(isset($currentChild) && $currentChild && $currentChild->classroom)
            @php $cls = $currentChild->classroom; @endphp
            <div style="display:flex;gap:12px;margin:10px 0;flex-wrap:wrap;">
                <div style="flex:1;min-width:100px;background:#f0fdf4;border-radius:12px;padding:12px;text-align:center;border:2px solid #bbf7d0;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#059669;">🟢 Masuk</div>
                    <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $cls->start_time ? \Carbon\Carbon::parse($cls->start_time)->format('h:i A') : '—' }}</div>
                </div>
                <div style="flex:1;min-width:100px;background:#f5f3ff;border-radius:12px;padding:12px;text-align:center;border:2px solid #ddd6fe;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#6d28d9;">🟣 Balik</div>
                    <div style="font-size:18px;font-weight:800;color:#1e293b;">{{ $cls->end_time ? \Carbon\Carbon::parse($cls->end_time)->format('h:i A') : '—' }}</div>
                </div>
            </div>
        @endif

        <!-- ============================================ -->
        <!-- ANAK YANG BOLEH CHECK IN                     -->
        <!-- ============================================ -->
        @if(isset($availableChildren) && count($availableChildren) > 0)
            @if(count($availableChildren) > 1)
                <div class="select-all" id="selectAll" onclick="toggleSelectAll()">
                    <div class="left">
                        <div class="checkbox-all" id="checkboxAll"></div>
                        <span>Select All</span>
                    </div>
                    <span style="font-size:13px; color:#6b7280;">{{ count($availableChildren) }} available</span>
                </div>
            @endif

            <div class="child-list" id="childList">
                @foreach($availableChildren as $availChild)
                    <div class="child-list-item" data-id="{{ $availChild['id'] }}" onclick="toggleChild(this)">
                        <div class="info">
                            <div class="checkbox"></div>
                            <div class="details">
                                <div class="name">{{ $availChild['name'] }}</div>
                                <div class="class">🏫 {{ $availChild['classroom'] }}
                                    @if(!empty($availChild['class_start']) && !empty($availChild['class_end']))
                                        <span style="font-size:10px;color:#64748b;display:block;">
                                            🟢 {{ \Carbon\Carbon::parse($availChild['class_start'])->format('h:i A') }} — 🟣 {{ \Carbon\Carbon::parse($availChild['class_end'])->format('h:i A') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="badge badge-available">⏳ Available</span>
                    </div>
                @endforeach
            </div>

            <div class="selected-count" id="selectedCount">0 anak dipilih</div>

            <!-- 🔥 BUTTON CHECK-IN (PINTAR: SINGLE ATAU BULK) -->
            <button class="btn-checkin" id="btnCheckin" onclick="checkinSelected()" disabled>
                <span id="btnText">✅ Check In Selected Child</span>
            </button>

            <!-- BUTTON PROCEED TO CHECKOUT -->
            @if($totalCheckedIn > 0 && isset($currentChild))
                <button class="btn-next-secondary" onclick="proceedToCheckinPage()" style="margin-top:15px;">
                    ➡️ Proceed to Check Out ({{ $totalCheckedIn }} checked in)
                </button>
            @endif

        @elseif($allCheckedIn && $totalChildren > 0)
            <!-- ALL CHECKED IN - Show late warning if applicable -->
            @if(isset($attendanceSummary) && $attendanceSummary['checkin']['status'] === 'late')
                <div class="all-checked-in-message" style="background:#fff3e0;border-color:#f59e0b;color:#92400e;">
                    <div class="icon">⚠️</div>
                    Checked in {{ \App\Services\AttendanceSummaryService::formatDuration($attendanceSummary['checkin']['minutes_diff']) }} late!<br>
                    <small style="font-weight:400;">Schedule: {{ $attendanceSummary['schedule']['morning_end'] }}</small>
                </div>
            @else
                <div class="all-checked-in-message">
                    <div class="icon">🎉</div>
                    All children have been checked in!<br>
                    <small style="font-weight:400;">Thank you for completing check-in.</small>
                </div>
            @endif

            <!-- BUTTON PROCEED TO CHECKOUT -->
            <button class="btn-next" onclick="proceedToCheckout()">
                ➡️ Proceed to Check Out
            </button>

        @else
            <!-- No other children - show check-in/attendance for current child -->
            @if(!$allCheckedIn)
                @if(isset($currentChild) && !$childCheckedIn)
                    <!-- Child NOT checked in yet → show check-in button -->
                    <button class="btn-checkin" onclick="checkinCurrentChild()" style="margin-top:10px;">
                        ✅ Check In {{ $currentChild->name }}
                    </button>
                @elseif(isset($currentChild) && $childCheckedIn && !$childCheckedOut)
                    <!-- Child checked in but not checked out → show status -->
                    <div class="checked-in-list-container" style="margin-top:15px;">
                        <div class="header">
                            <span class="title">✅ Checked In Today</span>
                        </div>
                        <div class="checked-in-item" style="padding:10px;">
                            <div class="left">
                                <div class="avatar-small">{{ strtoupper(substr($currentChild->name, 0, 1)) }}</div>
                                <div>
                                    <div class="name">{{ $currentChild->name }}</div>
                                    <div class="class">🏫 {{ $currentChild->classroom->name ?? '-' }}</div>
                                </div>
                            </div>
                            <span class="time" style="color:#059669;font-weight:600;">
                                ✅ {{ $childAttendance ? \Carbon\Carbon::parse($childAttendance->checkin_time)->format('h:i A') : 'Checked In' }}
                            </span>
                        </div>
                    </div>
                    <button class="btn-next-secondary" onclick="proceedToCheckout()" style="margin-top:15px;">
                        ➡️ Proceed to Check Out
                    </button>
                @endif
            @endif

            @if(!$allCheckedIn)
            <div class="no-children" style="padding:10px 0;">
                <span style="color:#6b7280;font-size:13px;">Hanya anak ini sahaja yang berdaftar.</span>
            </div>
            @endif

            @if($totalCheckedIn > 0 && isset($currentChild))
                <button class="btn-next" onclick="proceedToCheckinPage()">
                    ➡️ Proceed to Check Out ({{ $totalCheckedIn }} checked in)
                </button>
            @endif
        @endif
        @endif

        <!-- ============================================ -->
        <!-- BACK BUTTON                                  -->
        <!-- ============================================ -->
        <button class="btn-back" onclick="window.location.href='/kiosk'">🔙 Back to Kiosk</button>
    </div>

    <script>
        // ============================================
        // DATA DARI CONTROLLER
        // ============================================
        const childId = {{ $currentChildId ?? 0 }};
        const parentId = {{ $parentId ?? 0 }};
        const allCheckedInIds = @json($checkedInIds ?? []);
        const availableChildren = @json(array_column($availableChildren ?? [], 'id'));
        const currentChildId = {{ isset($currentChild) ? $currentChild->id : 0 }};
        const hashedChildId = '{{ isset($currentChild) ? \App\Helper\KioskHelper::hashId($currentChild->id) : '' }}';
        const childHashes = @json(isset($allChildren) ? $allChildren->mapWithKeys(fn($c) => [$c->id => \App\Helper\KioskHelper::hashId($c->id)]) : []);

        let selectedChildren = [];

        console.log('✅ Add Another Child - Data:');
        console.log('  - Current Child ID:', currentChildId);
        console.log('  - Parent ID:', parentId);
        console.log('  - Checked In IDs:', allCheckedInIds);
        console.log('  - Available Children:', availableChildren);
        console.log('  - Available Count:', availableChildren.length);

        // ============================================
        // CHECKIN CURRENT CHILD (when no other children)
        // ============================================
        function checkinCurrentChild() {
            window.location.href = '/kiosk/checkin-page/' + hashedChildId + '?parent_id=' + parentId;
        }

        // ============================================
        // TOGGLE SELECT ALL
        // ============================================
        function toggleSelectAll() {
            console.log('🔄 toggleSelectAll() called');
            const selectAll = document.getElementById('selectAll');
            if (!selectAll) {
                console.log('  - ⚠️ selectAll element not found');
                return;
            }

            const items = document.querySelectorAll('.child-list-item:not(.disabled)');
            console.log('  - Available items:', items.length);

            if (items.length === 0) {
                console.log('  - No available items to select');
                return;
            }

            const allSelected = selectAll.classList.contains('selected');
            console.log('  - Currently all selected?', allSelected);

            if (allSelected) {
                // UNSELECT ALL
                selectAll.classList.remove('selected');
                items.forEach(item => {
                    item.classList.remove('selected');
                    const id = parseInt(item.dataset.id);
                    selectedChildren = selectedChildren.filter(c => c !== id);
                });
                console.log('  - Unselected all');
            } else {
                // SELECT ALL
                selectAll.classList.add('selected');
                items.forEach(item => {
                    const id = parseInt(item.dataset.id);
                    if (!allCheckedInIds.includes(id)) {
                        item.classList.add('selected');
                        if (!selectedChildren.includes(id)) {
                            selectedChildren.push(id);
                        }
                        console.log('  - Selected child:', id);
                    }
                });
            }

            console.log('  - Selected Children after:', selectedChildren);
            updateSelectedCount();
        }

        // ============================================
        // TOGGLE CHILD
        // ============================================
        function toggleChild(element) {
            console.log('🔄 toggleChild() called');
            const id = parseInt(element.dataset.id);
            console.log('  - Child ID:', id);

            if (allCheckedInIds.includes(id)) {
                console.log('  - ⛔ Child already checked in, skipping');
                return;
            }

            const isSelected = element.classList.contains('selected');
            console.log('  - Currently selected?', isSelected);

            if (isSelected) {
                element.classList.remove('selected');
                selectedChildren = selectedChildren.filter(c => c !== id);
                console.log('  - Removed from selection');
            } else {
                element.classList.add('selected');
                if (!selectedChildren.includes(id)) {
                    selectedChildren.push(id);
                }
                console.log('  - Added to selection');
            }

            // Update Select All
            const items = document.querySelectorAll('.child-list-item:not(.disabled)');
            const allSelected = items.length > 0 && Array.from(items).every(item => {
                return item.classList.contains('selected');
            });

            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                if (allSelected) {
                    selectAll.classList.add('selected');
                } else {
                    selectAll.classList.remove('selected');
                }
            }

            console.log('  - Selected Children:', selectedChildren);
            updateSelectedCount();
        }

        // ============================================
        // UPDATE SELECTED COUNT - PINTAR
        // ============================================
        function updateSelectedCount() {
            const count = selectedChildren.length;
            console.log('📊 updateSelectedCount():', count);

            const countElement = document.getElementById('selectedCount');
            if (countElement) {
                countElement.textContent = count + ' anak dipilih';
            }

            const btn = document.getElementById('btnCheckin');
            const btnText = document.getElementById('btnText');

            if (btn) {
                if (count === 0) {
                    btn.disabled = true;
                    if (btnText) btnText.textContent = '✅ Check In Selected Child';
                    console.log('  - Button: DISABLED');
                } else if (count === 1) {
                    btn.disabled = false;
                    if (btnText) btnText.textContent = '✅ Check In 1 Child';
                    console.log('  - Button: ENABLED (Single)');
                } else {
                    btn.disabled = false;
                    if (btnText) btnText.textContent = '⚡ Check In All (' + count + ')';
                    console.log('  - Button: ENABLED (Bulk)');
                }
            }
        }

        // ============================================
        // CHECKIN SELECTED - PINTAR (SINGLE ATAU BULK)
        // ============================================
        function checkinSelected() {
            console.log('✅ checkinSelected() called');
            console.log('  - Selected Children:', selectedChildren);

            if (selectedChildren.length === 0) {
                console.log('  - ⚠️ No children selected');
                return;
            }

            // 🔥🔥🔥 JIKA HANYA 1 ANAK → PERGI CHECK-IN PAGE 🔥🔥🔥
            if (selectedChildren.length === 1) {
                const childId = selectedChildren[0];
                console.log('  - Single child selected, redirect to check-in page:', childId);
                window.location.href = '/kiosk/checkin-page/' + (childHashes[childId] || childId) + '?parent_id=' + parentId;
                return;
            }

            // 🔥🔥🔥 JIKA LEBIH DARI 1 ANAK → BULK CHECK-IN 🔥🔥🔥
            console.log('  - Multiple children selected, doing bulk check-in');

            const btn = document.getElementById('btnCheckin');
            const btnText = document.getElementById('btnText');
            if (btn) {
                btn.disabled = true;
                if (btnText) btnText.textContent = '⏳ Checking in...';
            }

            // CALL API BULK CHECK-IN
            fetch('/kiosk/bulk-checkin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    parent_id: parentId,
                    child_ids: selectedChildren,
                    date: '{{ $selectedDate ?? now()->format("Y-m-d") }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('  - Bulk check-in response:', data);

                if (data.success) {
                    showBulkSuccessPopup(data);
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    alert('❌ ' + (data.message || 'Gagal check-in!'));
                    if (btn) {
                        btn.disabled = false;
                        if (btnText) btnText.textContent = '⚡ Check In All (' + selectedChildren.length + ')';
                    }
                }
            })
            .catch(error => {
                console.error('  - Error:', error);
                alert('❌ Ralat: ' + error.message);
                if (btn) {
                    btn.disabled = false;
                    if (btnText) btnText.textContent = '⚡ Check In All (' + selectedChildren.length + ')';
                }
            });
        }

        // ============================================
        // SHOW BULK SUCCESS POPUP
        // ============================================
        function showBulkSuccessPopup(data) {
            const existingPopup = document.getElementById('bulkSuccessPopup');
            if (existingPopup) {
                existingPopup.remove();
            }

            const overlay = document.createElement('div');
            overlay.className = 'popup-overlay active';
            overlay.id = 'bulkSuccessPopup';
            overlay.style.cssText = `
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(8px);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
                padding: 20px;
            `;

            let resultsHtml = '';
            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    const statusIcon = item.status === 'checked_in' ? '✅' :
                                      item.status === 'late' ? '⏰' : '📌';
                    const statusText = item.status === 'checked_in' ? 'Checked In' :
                                      item.status === 'late' ? 'Late' : 'Already';
                    const borderColor = item.status === 'checked_in' ? '#22c55e' :
                                       item.status === 'late' ? '#ef4444' : '#3b82f6';
                    resultsHtml += `
                        <div style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 8px 12px;
                            margin: 4px 0;
                            background: white;
                            border-radius: 8px;
                            border-left: 4px solid ${borderColor};
                        ">
                            <span style="font-weight: 600; font-size: 14px;">${statusIcon} ${item.name}</span>
                            <span style="font-size: 12px; color: #6b7280;">${statusText}</span>
                            <span style="font-size: 11px; color: #94a3b8;">${item.time || ''}</span>
                        </div>
                    `;
                });
            }

            overlay.innerHTML = `
                <div style="
                    background: white;
                    border-radius: 32px;
                    padding: 35px 30px;
                    max-width: 440px;
                    width: 100%;
                    text-align: center;
                    animation: slideUp 0.4s ease;
                    max-height: 90vh;
                    overflow-y: auto;
                ">
                    <div style="font-size: 56px; margin-bottom: 8px;">🎉</div>
                    <h2 style="font-size: 22px; font-weight: 700; color: #16a34a; margin-bottom: 4px;">
                        Bulk Check-in Berjaya!
                    </h2>
                    <p style="color: #6b7280; font-size: 14px; margin-bottom: 12px;">
                        ${data.checked_count || 0} anak berjaya check-in
                        ${data.late_count > 0 ? `, ${data.late_count} lewat` : ''}
                        ${data.already_count > 0 ? `, ${data.already_count} sudah check-in` : ''}
                    </p>
                    <div style="
                        background: #f8fafc;
                        border-radius: 12px;
                        padding: 10px;
                        margin: 10px 0;
                        text-align: left;
                        max-height: 200px;
                        overflow-y: auto;
                    ">
                        ${resultsHtml}
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                        <button onclick="closeBulkPopup()" style="
                            padding: 14px;
                            border: none;
                            border-radius: 14px;
                            font-weight: 600;
                            font-size: 16px;
                            cursor: pointer;
                            background: linear-gradient(135deg, #16a34a, #22c55e);
                            color: white;
                            transition: all 0.3s;
                            width: 100%;
                        ">
                            👍 Selesai
                        </button>
                        <button onclick="proceedToCheckout()" style="
                            padding: 14px;
                            border: none;
                            border-radius: 14px;
                            font-weight: 600;
                            font-size: 16px;
                            cursor: pointer;
                            background: #2563eb;
                            color: white;
                            transition: all 0.3s;
                            width: 100%;
                        ">
                            ➡️ Proceed to Check Out
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);
        }

        // ============================================
        // CLOSE BULK POPUP
        // ============================================
        function closeBulkPopup() {
            const popup = document.getElementById('bulkSuccessPopup');
            if (popup) {
                popup.remove();
            }
            window.location.reload();
        }

        // ============================================
        // PROCEED TO CHECKIN PAGE (UNTUK CURRENT CHILD)
        // ============================================
        function proceedToCheckinPage() {
            console.log('✅ proceedToCheckinPage() called');
            console.log('  - Current Child ID:', currentChildId);
            console.log('  - Parent ID:', parentId);

            if (currentChildId > 0) {
                window.location.href = '/kiosk/checkin-page/' + hashedChildId + '?parent_id=' + parentId;
            } else {
                console.log('  - ⚠️ No current child, redirect to checkout');
                window.location.href = '/kiosk/checkout-landing?parent_id=' + parentId;
            }
        }

        // ============================================
        // PROCEED TO CHECKOUT (TERUS)
        // ============================================
        function proceedToCheckout() {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '⏳ Processing...';

            fetch('/kiosk/direct-checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    child_id: currentChildId,
                    parent_id: parentId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/kiosk?checkout=success&child=' + encodeURIComponent(data.child_name);
                } else {
                    alert('❌ ' + (data.message || 'Gagal checkout'));
                    btn.disabled = false;
                    btn.textContent = '➡️ Proceed to Check Out';
                }
            })
            .catch(err => {
                alert('❌ Ralat: ' + err.message);
                btn.disabled = false;
                btn.textContent = '➡️ Proceed to Check Out';
            });
        }

        // ============================================
        // INIT
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM Loaded - Add Another Child');
            console.log('  - Available Children:', availableChildren);
            console.log('  - Checked In IDs:', allCheckedInIds);
            console.log('  - Current Child ID:', currentChildId);

            // Reset selection
            selectedChildren = [];

            document.querySelectorAll('.child-list-item').forEach(function(item) {
                const id = parseInt(item.dataset.id);
                const isCheckedIn = allCheckedInIds.includes(id);
                console.log('  - Found child:', id, 'Checked In:', isCheckedIn);

                if (isCheckedIn) {
                    item.classList.add('disabled');
                    item.classList.remove('selected');
                }
            });

            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.classList.remove('selected');
            }

            updateSelectedCount();
        });

        // ============================================
        // AUTO REFRESH SETIAP 30 SAAT
        // ============================================
        setTimeout(function() {
            console.log('🔄 Auto refresh');
            location.reload();
        }, 30000);

        console.log('✅ Add Another Child loaded successfully!');
    </script>
</body>
</html>
