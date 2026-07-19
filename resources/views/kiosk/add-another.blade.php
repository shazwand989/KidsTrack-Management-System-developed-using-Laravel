<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidsTrack - Select Children</title>
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
        
        /* ============================================ */
        /* CHECK-IN MODE - WARNA CERAH                  */
        /* ============================================ */
        body.main-parent {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        body.second-parent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body.guardian {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }
        body.admin {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        /* ============================================ */
        /* CHECK-OUT MODE - WARNA GELAP / MERAH        */
        /* ============================================ */
        body.checkout-mode {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        body.checkout-mode.main-parent {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        body.checkout-mode.second-parent {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        body.checkout-mode.guardian {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        body.checkout-mode.admin {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

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

        /* ============================================ */
        /* CHECK-OUT CARD - GELAP                       */
        /* ============================================ */
        .add-card.checkout-card {
            background: #1e293b;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        }
        .add-card.checkout-card .logo { color: #facc15; }
        .add-card.checkout-card h1 { color: #facc15; }
        .add-card.checkout-card .subtitle { color: #94a3b8; }
        .add-card.checkout-card .role-info {
            background: #334155;
            border-color: #475569;
            color: #94a3b8;
        }
        .add-card.checkout-card .role-info .role-name.main { color: #facc15; }
        .add-card.checkout-card .role-info .role-name.second { color: #facc15; }
        .add-card.checkout-card .role-info .role-name.guardian { color: #facc15; }
        .add-card.checkout-card .role-info .role-name.admin { color: #facc15; }

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

        /* ============================================ */
        /* CHECK-OUT CARD BORDER - KUNING               */
        /* ============================================ */
        .add-card.checkout-card::before {
            background: linear-gradient(90deg, #facc15, #f59e0b) !important;
        }

        .add-card .logo { font-size: 64px; margin-bottom: 10px; transition: color 0.5s ease; }
        .add-card h1 { font-size: 24px; color: #1f2937; margin-bottom: 5px; transition: color 0.5s ease; }
        .add-card .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 20px; transition: color 0.5s ease; }

        .role-badge {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            transition: all 0.5s ease;
        }
        .role-badge.main-parent { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .role-badge.second-parent { background: #ede9fe; color: #5b21b6; border: 2px solid #8b5cf6; }
        .role-badge.guardian { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
        .role-badge.admin { background: #e2e8f0; color: #1e293b; border: 2px solid #475569; }

        /* ============================================ */
        /* CHECK-OUT ROLE BADGE - KUNING                */
        /* ============================================ */
        .role-badge.checkout-role {
            background: rgba(250, 204, 21, 0.2) !important;
            color: #facc15 !important;
            border-color: #facc15 !important;
        }

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
            transition: all 0.5s ease;
        }
        .role-info .role-icon { font-size: 24px; }
        .role-info .role-name { font-weight: 700; }
        .role-info .role-name.main { color: #059669; }
        .role-info .role-name.second { color: #6d28d9; }
        .role-info .role-name.guardian { color: #d97706; }
        .role-info .role-name.admin { color: #1e293b; }

        /* ============================================ */
        /* CHECKED-IN LIST                              */
        /* ============================================ */
        .checked-in-list-container {
            margin: 15px 0;
            padding: 15px;
            background: #ecfdf5;
            border-radius: 16px;
            border: 2px solid #6ee7b7;
            text-align: left;
            transition: all 0.5s ease;
        }

        /* ============================================ */
        /* CHECKOUT CHECKED-IN LIST - GELAP             */
        /* ============================================ */
        .add-card.checkout-card .checked-in-list-container {
            background: #1e293b;
            border-color: #facc15;
        }
        .add-card.checkout-card .checked-in-list-container .header .title {
            color: #facc15;
        }
        .add-card.checkout-card .checked-in-list-container .header .count-badge {
            background: #facc15;
            color: #1e293b;
        }
        .add-card.checkout-card .checked-in-item {
            background: #334155;
        }
        .add-card.checkout-card .checked-in-item .name {
            color: #e2e8f0;
        }
        .add-card.checkout-card .checked-in-item .class {
            color: #94a3b8;
        }
        .add-card.checkout-card .checked-in-item .time {
            color: #facc15;
        }
        .add-card.checkout-card .checked-in-item.current {
            border-color: #facc15;
            background: rgba(250, 204, 21, 0.1);
        }
        .add-card.checkout-card .checked-in-item .avatar-small {
            background: #facc15;
            color: #1e293b;
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
            transition: all 0.3s ease;
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
        .checked-in-item .avatar-small.current-avatar {
            background: #f59e0b;
        }
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

        /* ============================================ */
        /* CHILD PROFILE BOX                           */
        /* ============================================ */
        .child-profile-box {
            background: #f3f4f6;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            border: 3px solid #e5e7eb;
            transition: all 0.5s ease;
        }
        .child-profile-box.main-parent-border { border-color: #10b981; background: #f0fdf4; }
        .child-profile-box.second-parent-border { border-color: #8b5cf6; background: #f5f3ff; }
        .child-profile-box.guardian-border { border-color: #f59e0b; background: #fffbeb; }
        .child-profile-box.admin-border { border-color: #475569; background: #f1f5f9; }

        /* ============================================ */
        /* CHECKOUT CHILD PROFILE - GELAP              */
        /* ============================================ */
        .add-card.checkout-card .child-profile-box {
            background: #1e293b !important;
            border-color: #facc15 !important;
        }
        .add-card.checkout-card .child-profile-box .child-name {
            color: #facc15 !important;
        }
        .add-card.checkout-card .child-profile-box .child-class {
            color: #94a3b8 !important;
        }
        .add-card.checkout-card .child-avatar {
            background: linear-gradient(135deg, #facc15, #f59e0b) !important;
            color: #1e293b !important;
        }
        .add-card.checkout-card .current-child-tag {
            background: rgba(250, 204, 21, 0.2) !important;
            color: #facc15 !important;
        }

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
            transition: all 0.3s ease;
        }
        .child-avatar.main-parent-avatar { background: linear-gradient(135deg, #059669, #10b981); }
        .child-avatar.second-parent-avatar { background: linear-gradient(135deg, #6d28d9, #8b5cf6); }
        .child-avatar.guardian-avatar { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .child-avatar.admin-avatar { background: linear-gradient(135deg, #334155, #475569); }

        .child-name { font-size: 22px; font-weight: 700; color: #1f2937; transition: color 0.3s ease; }
        .child-class { font-size: 14px; color: #6b7280; margin-top: 4px; transition: color 0.3s ease; }

        .current-child-tag {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
            transition: all 0.3s ease;
        }
        .current-child-tag.main-parent-tag { background: #d1fae5; color: #065f46; }
        .current-child-tag.second-parent-tag { background: #ede9fe; color: #5b21b6; }
        .current-child-tag.guardian-tag { background: #fef3c7; color: #92400e; }
        .current-child-tag.admin-tag { background: #e2e8f0; color: #1e293b; }

        /* ============================================ */
        /* SELECT ALL & CHILD LIST                     */
        /* ============================================ */
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
            max-height: 400px;
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
        .child-list-item.current {
            border: 2px solid #f59e0b;
            background: #fffbeb;
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
        .badge-disabled { background: #f3f4f6; color: #9ca3af; }

        /* ============================================ */
        /* BUTTONS                                      */
        /* ============================================ */
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
        .btn-next:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-checkout-all {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
        }
        .btn-checkout-all:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }
        .btn-checkout-all:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .btn-checkout-all-disabled {
            width: 100%;
            padding: 16px;
            background: #e5e7eb;
            color: #9ca3af;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: not-allowed;
            margin-top: 15px;
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
        .checkout-info {
            background: #fef3c7;
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
            font-size: 13px;
            color: #92400e;
            transition: all 0.3s ease;
        }
        .checkout-info.active {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #93c5fd;
        }
        .selected-count {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }

        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-overlay.active { display: flex; }
        .popup-box {
            background: white;
            border-radius: 32px;
            padding: 40px 35px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.4s ease;
            max-height: 80vh;
            overflow-y: auto;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .popup-icon { font-size: 64px; margin-bottom: 10px; }
        .popup-box h2 { font-size: 24px; font-weight: 700; color: #16a34a; margin-bottom: 8px; }
        .popup-box .popup-sub { color: #6b7280; font-size: 15px; margin-bottom: 15px; }
        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 16px;
            margin: 5px 0;
            background: #f8fafc;
            border-radius: 10px;
        }
        .result-item.checked_in { border-left: 4px solid #22c55e; }
        .result-item.already_checked { border-left: 4px solid #3b82f6; }
        .result-item .name { font-weight: 600; }
        .result-item .status { font-size: 13px; }
        .result-item .time { font-size: 12px; color: #94a3b8; }
        .popup-btn-success {
            padding: 12px 40px;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            margin-top: 15px;
            width: 100%;
        }
        .popup-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
        }
        .result-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 10px 0;
            flex-wrap: wrap;
        }
        .result-stats .stat {
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 20px;
            background: #f3f4f6;
        }
        .stat.checked { background: #dcfce7; color: #16a34a; }
        .stat.already { background: #dbeafe; color: #2563eb; }

        .child-list::-webkit-scrollbar { width: 4px; }
        .child-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .child-list::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .checkin-time-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .checkin-time-info.active {
            color: #059669;
            font-weight: 600;
        }
    </style>
</head>

<!-- ============================================================ -->
<!-- BODY CLASS - AUTO DETECT CHECKOUT MODE                       -->
<!-- ============================================================ -->
<body class="{{ isset($isCheckoutMode) && $isCheckoutMode ? 'checkout-mode' : ($roleData['class'] ?? 'main-parent') }}">

    <div class="add-card {{ isset($isCheckoutMode) && $isCheckoutMode ? 'checkout-card' : ($roleData['class'] ?? 'main-parent') }}">
        <div class="logo">👶</div>
        <h1>{{ isset($isCheckoutMode) && $isCheckoutMode ? '👋 Check-out Children' : 'Select Children' }}</h1>
        <p class="subtitle">{{ isset($isCheckoutMode) && $isCheckoutMode ? 'Pilih anak untuk check-out' : 'Pilih anak lain untuk kehadiran' }}</p>

        <div class="role-badge {{ isset($isCheckoutMode) && $isCheckoutMode ? 'checkout-role' : ($roleData['badge_class'] ?? 'main-parent') }}">
            @if(isset($isCheckoutMode) && $isCheckoutMode)
                🌙 Check-out Mode • {{ $roleData['badge_text'] ?? '👨‍👩‍👦 Main Parent' }}
            @else
                {{ $roleData['badge_text'] ?? '👨‍👩‍👦 Main Parent' }}
            @endif
        </div>

        <div class="role-info">
            <span class="role-icon">{{ $roleData['icon'] ?? '👨‍👩‍👦' }}</span>
            <span>Logged in as</span>
            <span class="role-name {{ $roleData['name_class'] ?? 'main' }}">
                {{ $roleData['display_name'] ?? 'Main Parent' }}
            </span>
        </div>

        <!-- DISPLAY SEMUA ANAK YANG SUDAH CHECK-IN -->
        <div class="checked-in-list-container">
            <div class="header">
                <span class="title">✅ Telah Check-in Hari Ini</span>
                <span class="count-badge" id="checkedCountBadge">{{ isset($allCheckedInData) ? count($allCheckedInData) : 0 }} anak</span>
            </div>
            <div class="checked-in-list-scroll" id="checkedInList">
                @if(isset($allCheckedInData) && count($allCheckedInData) > 0)
                    @foreach($allCheckedInData as $checkedChild)
                        <div class="checked-in-item {{ isset($checkedChild['is_current']) && $checkedChild['is_current'] ? 'current' : '' }}">
                            <div class="left">
                                <div class="avatar-small {{ isset($checkedChild['is_current']) && $checkedChild['is_current'] ? 'current-avatar' : '' }}">
                                    {{ $checkedChild['initial'] ?? strtoupper(substr($checkedChild['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <div class="name">{{ $checkedChild['name'] }}</div>
                                    <div class="class">🏫 {{ $checkedChild['classroom'] ?? '-' }}</div>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:8px;">
                                @if(isset($checkedChild['is_current']) && $checkedChild['is_current'])
                                    <span class="current-badge">⭐ Current</span>
                                @endif
                                <span class="time">✅ {{ $checkedChild['time'] }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="checked-in-empty">Tiada anak yang check-in hari ini</div>
                @endif
            </div>
        </div>

        <!-- ================================================ -->
        <!-- CURRENT CHILD - CHECKOUT BOLEH BILA-BILA!        -->
        <!-- ================================================ -->
        <div class="child-profile-box {{ $roleData['border_class'] ?? 'main-parent-border' }}">
            <div class="child-avatar {{ $roleData['avatar_class'] ?? 'main-parent-avatar' }}">
                {{ strtoupper(substr($child->name, 0, 1)) }}
            </div>
            <div class="child-name">{{ $child->name }}</div>
            <div class="child-class">🏫 {{ $child->classroom->name ?? 'Tiada kelas' }}</div>
            <div class="current-child-tag {{ $roleData['tag_class'] ?? 'main-parent-tag' }}">
                @if(isset($childCheckedIn) && $childCheckedIn)
                    ✅ Sudah Check-in
                @else
                    ⏳ Belum Check-in
                @endif
            </div>
            
            <!-- BUTTON CHECKOUT - BOLEH BILA-BILA (LATE CHECKOUT JIKA LUAR SLOT) -->
            @if(isset($childCheckedIn) && $childCheckedIn && !$childCheckedOut)
                @php
                    $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
                    $timerSetting = \App\Models\TimerSetting::where('day_name', $now->format('l'))->first();
                    $canCheckoutLocal = false;
                    $checkoutStartTime = '--:--';
                    $checkoutEndTime = '--:--';
                    $checkoutMessage = '⏰ Checkout Belum Tersedia';
                    $isLateCheckout = false;
                    
                    if ($timerSetting) {
                        $currentTimeInt = (int) $now->format('Hi');
                        $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
                        $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);
                        $checkoutStartTime = date('H:i', strtotime($timerSetting->evening_start));
                        $checkoutEndTime = date('H:i', strtotime($timerSetting->evening_end));
                        
                        // 🔥🔥🔥 CHECKOUT BOLEH BILA-BILA! 🔥🔥🔥
                        if ($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt) {
                            $canCheckoutLocal = true;
                            $checkoutMessage = '✅ Waktu checkout: ' . $checkoutStartTime . ' - ' . $checkoutEndTime;
                        } else if ($currentTimeInt < $eveningStartInt) {
                            $canCheckoutLocal = false;
                            $checkoutMessage = '🕐 Checkout bermula pada ' . $checkoutStartTime;
                        } else {
                            // 🔥 LEPAS SLOT → LATE CHECKOUT (BOLEH!)
                            $canCheckoutLocal = true;
                            $isLateCheckout = true;
                            $checkoutMessage = '⏰ Late Checkout (Melebihi waktu operasi)';
                        }
                    }
                @endphp
                
                @if($canCheckoutLocal)
                    <button class="btn-checkout-all" onclick="checkoutCurrentChild()" style="margin-top:10px;">
                        👋 Check-out {{ $child->name }}
                    </button>
                    <div class="checkin-time-info active">
                        ✅ {{ $checkoutMessage }}
                    </div>
                @else
                    <button class="btn-checkout-all-disabled" disabled style="margin-top:10px;">
                        ⏰ {{ $checkoutMessage }}
                    </button>
                    <div class="checkin-time-info">
                        📌 Tunggu waktu checkout: {{ $checkoutStartTime }} - {{ $checkoutEndTime }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ================================================ -->
        <!-- ANAK LAIN - CHECKOUT BOLEH BILA-BILA!            -->
        <!-- ================================================ -->
        @if(isset($otherChildren) && $otherChildren->count() > 0)
            @php
                $availableChildren = $otherChildren->filter(function($c) {
                    return !$c->checked_out_today;
                });
                $checkedChildren = $otherChildren->filter(function($c) {
                    return $c->checked_in_today && !$c->checked_out_today;
                });
                $checkedOutChildren = $otherChildren->filter(function($c) {
                    return $c->checked_out_today;
                });
                $totalAvailable = $availableChildren->count();
                $allCheckedIn = $availableChildren->every(function($c) {
                    return $c->checked_in_today;
                });
                
                // ============================================
                // CHECKOUT LOGIC - BOLEH BILA-BILA!
                // ============================================
                $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
                $timerSetting = \App\Models\TimerSetting::where('day_name', $now->format('l'))->first();
                
                $canCheckoutLocal = false;
                $checkoutMessage = '⏰ Checkout Belum Tersedia';
                $checkoutInfoClass = '';
                $isLateCheckout = false;
                
                if ($timerSetting) {
                    $currentTimeInt = (int) $now->format('Hi');
                    $eveningStartInt = (int) str_replace(':', '', $timerSetting->evening_start);
                    $eveningEndInt = (int) str_replace(':', '', $timerSetting->evening_end);
                    $checkoutStartTime = date('H:i', strtotime($timerSetting->evening_start));
                    $checkoutEndTime = date('H:i', strtotime($timerSetting->evening_end));
                    
                    // 🔥🔥🔥 CHECKOUT BOLEH BILA-BILA! 🔥🔥🔥
                    if ($currentTimeInt >= $eveningStartInt && $currentTimeInt <= $eveningEndInt) {
                        $canCheckoutLocal = true;
                        $checkoutMessage = '✅ Waktu checkout: ' . $checkoutStartTime . ' - ' . $checkoutEndTime;
                        $checkoutInfoClass = 'active';
                    } else if ($currentTimeInt < $eveningStartInt) {
                        $canCheckoutLocal = false;
                        $checkoutMessage = '🕐 Checkout bermula pada ' . $checkoutStartTime;
                        $checkoutInfoClass = '';
                    } else {
                        // 🔥 LEPAS SLOT → LATE CHECKOUT (BOLEH!)
                        $canCheckoutLocal = true;
                        $isLateCheckout = true;
                        $checkoutMessage = '⏰ Late Checkout (Melebihi waktu operasi)';
                        $checkoutInfoClass = 'active late';
                    }
                }
            @endphp

            @if($allCheckedIn && $availableChildren->count() > 0)
                <div style="margin: 15px 0; padding: 12px; background: #dbeafe; border-radius: 12px; color: #1e40af; font-weight: 600;">
                    ✅ Semua anak lain sudah check-in
                </div>

                @if($canCheckoutLocal)
                    <button class="btn-checkout-all" onclick="checkoutAll()">
                        👋 Check-out Semua ({{ $availableChildren->count() + 1 }})
                    </button>
                    <div class="checkout-info {{ $checkoutInfoClass }}">
                        {{ $checkoutMessage }}
                    </div>
                @else
                    <button class="btn-checkout-all-disabled" disabled>
                        ⏰ Checkout Belum Tersedia
                    </button>
                    <div class="checkout-info {{ $checkoutInfoClass }}">
                        {{ $checkoutMessage }}
                    </div>
                @endif

            @elseif($totalAvailable > 0)
                <div class="select-all" id="selectAll" onclick="toggleSelectAll()">
                    <div class="left">
                        <div class="checkbox-all" id="checkboxAll"></div>
                        <span>Pilih Semua</span>
                    </div>
                    <span style="font-size:13px; color:#6b7280;">{{ $totalAvailable }} anak</span>
                </div>

                <div class="child-list" id="childList">
                    @foreach($otherChildren as $otherChild)
                        @php
                            $isChecked = $otherChild->checked_in_today;
                            $isCheckedOut = $otherChild->checked_out_today;
                            $isAvailable = !$isChecked && !$isCheckedOut;
                            $badgeClass = $isCheckedOut ? 'badge-checkout-done' : ($isChecked ? 'badge-checked' : 'badge-available');
                            $badgeText = $isCheckedOut ? '📤 Checkout' : ($isChecked ? '✅ Checked' : '⏳ Available');
                            $disabled = $isChecked || $isCheckedOut;
                        @endphp
                        <div class="child-list-item {{ $disabled ? 'disabled' : '' }}" 
                             data-id="{{ $otherChild->id }}"
                             onclick="{{ $disabled ? '' : "toggleChild(this)" }}">
                            <div class="info">
                                <div class="checkbox"></div>
                                <div class="details">
                                    <div class="name">{{ $otherChild->name }}</div>
                                    <div class="class">🏫 {{ $otherChild->classroom->name ?? 'Tiada kelas' }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="selected-count" id="selectedCount">0 anak dipilih</div>
                <button class="btn-checkin" id="btnCheckin" onclick="checkinSelected()" disabled>
                    ✅ Check-in Selected
                </button>

                <!-- BUTTON NEXT -->
                <button class="btn-next" id="btnNext" onclick="goToCheckinPage()">
                    ➡️ Next - Check-in Page
                </button>

            @else
                <div class="no-children">
                    <span style="font-size: 32px; display: block; margin-bottom: 10px;">🎉</span>
                    Semua anak lain sudah check-in atau check-out.
                </div>
            @endif

        @else
            <div class="no-children">
                <span style="font-size: 32px; display: block; margin-bottom: 10px;">👶</span>
                Tiada anak lain yang berdaftar.
            </div>
        @endif

        <button class="btn-back" onclick="window.location.href='/kiosk'">🔙 Kembali ke Kiosk</button>
    </div>

    <div class="popup-overlay" id="bulkCheckoutPopup">
        <div class="popup-box">
            <div class="popup-icon">👋</div>
            <h2>Check-out Berjaya!</h2>
            <p class="popup-sub" id="bulkCheckoutSub">Semua anak telah berjaya check-out.</p>
            <div id="bulkCheckoutResults"></div>
            <button class="popup-btn-success" onclick="closeBulkCheckoutPopup()">👍 Selesai</button>
        </div>
    </div>

    <div class="popup-overlay" id="bulkCheckinPopup">
        <div class="popup-box">
            <div class="popup-icon">✅</div>
            <h2>Check-in Berjaya!</h2>
            <p class="popup-sub" id="bulkCheckinSub">Anak yang dipilih telah berjaya check-in.</p>
            <div id="bulkCheckinResults"></div>
            <button class="popup-btn-success" onclick="closeBulkCheckinPopup()">👍 Selesai</button>
        </div>
    </div>

    <script>
        let childId = {{ isset($child) && $child ? $child->id : 0 }};
        let parentId = {{ isset($parentIdForView) && $parentIdForView ? $parentIdForView : 0 }};
        let canCheckout = {{ isset($canCheckout) ? ($canCheckout ? 'true' : 'false') : 'false' }};
        let selectedChildren = [];
        let isCheckoutMode = {{ isset($isCheckoutMode) ? ($isCheckoutMode ? 'true' : 'false') : 'false' }};

        console.log('Parent ID:', parentId);
        console.log('Child ID:', childId);
        console.log('Can Checkout:', canCheckout);
        console.log('Is Checkout Mode:', isCheckoutMode);

        // ============================================
        // TOGGLE SELECT ALL
        // ============================================
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const items = document.querySelectorAll('.child-list-item:not(.disabled)');
            const allSelected = selectAll.classList.contains('selected');
            
            if (allSelected) {
                selectAll.classList.remove('selected');
                items.forEach(item => {
                    item.classList.remove('selected');
                });
                selectedChildren = [];
            } else {
                selectAll.classList.add('selected');
                items.forEach(item => {
                    item.classList.add('selected');
                    const id = parseInt(item.dataset.id);
                    if (!selectedChildren.includes(id)) {
                        selectedChildren.push(id);
                    }
                });
            }
            updateSelectedCount();
        }

        // ============================================
        // TOGGLE CHILD
        // ============================================
        function toggleChild(element) {
            if (element.classList.contains('disabled')) return;
            
            const id = parseInt(element.dataset.id);
            const isSelected = element.classList.contains('selected');
            
            if (isSelected) {
                element.classList.remove('selected');
                selectedChildren = selectedChildren.filter(c => c !== id);
            } else {
                element.classList.add('selected');
                if (!selectedChildren.includes(id)) {
                    selectedChildren.push(id);
                }
            }
            
            const items = document.querySelectorAll('.child-list-item:not(.disabled)');
            const allSelected = items.length === selectedChildren.length;
            const selectAll = document.getElementById('selectAll');
            if (allSelected && items.length > 0) {
                selectAll.classList.add('selected');
            } else {
                selectAll.classList.remove('selected');
            }
            
            updateSelectedCount();
        }

        // ============================================
        // UPDATE SELECTED COUNT
        // ============================================
        function updateSelectedCount() {
            const count = selectedChildren.length;
            document.getElementById('selectedCount').textContent = count + ' anak dipilih';
            const btn = document.getElementById('btnCheckin');
            if (btn) {
                btn.disabled = count === 0;
                btn.textContent = count > 0 ? '✅ Check-in Selected (' + count + ')' : '✅ Check-in Selected';
            }
        }

        // ============================================
        // CHECKIN SELECTED
        // ============================================
        function checkinSelected() {
            if (selectedChildren.length === 0) return;
            
            const firstChildId = selectedChildren[0];
            window.location.href = '/kiosk/checkin-page/' + firstChildId + '?parent_id=' + parentId;
        }

        // ============================================
        // GO TO CHECKIN PAGE - BUTTON NEXT
        // ============================================
        function goToCheckinPage() {
            console.log('🔥 Go to Checkin Page - childId:', childId, 'parentId:', parentId);
            window.location.href = '/kiosk/checkin-page/' + childId + '?parent_id=' + parentId;
        }

        // ============================================
        // CHECKOUT CURRENT CHILD
        // ============================================
        function checkoutCurrentChild() {
            if (!canCheckout) {
                alert('⏰ Checkout hanya dibenarkan dalam waktu Evening slot!');
                return;
            }
            
            window.location.href = '/kiosk/checkin-page/' + childId + '?parent_id=' + parentId + '&action=checkout';
        }

        // ============================================
        // CHECKOUT ALL
        // ============================================
        function checkoutAll() {
            if (!canCheckout) {
                alert('⏰ Checkout hanya dibenarkan dalam waktu Evening slot!');
                return;
            }
            
            const btn = document.querySelector('.btn-checkout-all');
            if (!btn) return;
            
            btn.disabled = true;
            btn.textContent = '⏳ Memproses...';

            const childIds = @json($allChildren->pluck('id')->toArray() ?? []);
            const parentId = {{ isset($parentIdForView) && $parentIdForView ? $parentIdForView : 0 }};

            fetch('{{ route('kiosk.checkout.all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    parent_id: parentId,
                    child_ids: childIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showBulkCheckoutPopup(data);
                    btn.textContent = '✅ Selesai!';
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    alert('❌ ' + data.message);
                    btn.disabled = false;
                    btn.textContent = '👋 Check-out Semua';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Ralat: ' + error.message);
                btn.disabled = false;
                btn.textContent = '👋 Check-out Semua';
            });
        }

        // ============================================
        // POPUP BULK CHECKOUT
        // ============================================
        function showBulkCheckoutPopup(data) {
            const popup = document.getElementById('bulkCheckoutPopup');
            const resultsDiv = document.getElementById('bulkCheckoutResults');
            
            let html = `
                <div class="result-stats">
                    <span class="stat checked">👋 ${data.checkout_count || 0} Checked Out</span>
                    ${data.already_count > 0 ? `<span class="stat already">📌 ${data.already_count} Already</span>` : ''}
                </div>
            `;
            
            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    let statusText = '';
                    let statusClass = '';
                    if (item.status === 'checkout') {
                        statusText = '👋 Checked Out';
                        statusClass = 'checked_in';
                    } else if (item.status === 'already_checked') {
                        statusText = '📌 Already Checked';
                        statusClass = 'already_checked';
                    }
                    html += `
                        <div class="result-item ${statusClass}">
                            <span class="name">${item.name}</span>
                            <span class="status">${statusText}</span>
                            <span class="time">${item.time}</span>
                        </div>
                    `;
                });
            }
            
            resultsDiv.innerHTML = html;
            popup.classList.add('active');
        }

        function closeBulkCheckoutPopup() {
            document.getElementById('bulkCheckoutPopup').classList.remove('active');
            window.location.reload();
        }

        // ============================================
        // POPUP BULK CHECKIN
        // ============================================
        function showBulkCheckinPopup(data) {
            const popup = document.getElementById('bulkCheckinPopup');
            const resultsDiv = document.getElementById('bulkCheckinResults');
            
            let html = `
                <div class="result-stats">
                    <span class="stat checked">✅ ${data.checked_count || 0} Checked In</span>
                    ${data.already_count > 0 ? `<span class="stat already">📌 ${data.already_count} Already</span>` : ''}
                </div>
            `;
            
            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    let statusText = '';
                    let statusClass = '';
                    if (item.status === 'checked_in') {
                        statusText = '✅ Checked In';
                        statusClass = 'checked_in';
                    } else if (item.status === 'already_checked') {
                        statusText = '📌 Already Checked';
                        statusClass = 'already_checked';
                    }
                    html += `
                        <div class="result-item ${statusClass}">
                            <span class="name">${item.name}</span>
                            <span class="status">${statusText}</span>
                            <span class="time">${item.time}</span>
                        </div>
                    `;
                });
            }
            
            resultsDiv.innerHTML = html;
            popup.classList.add('active');
        }

        function closeBulkCheckinPopup() {
            document.getElementById('bulkCheckinPopup').classList.remove('active');
            window.location.reload();
        }

        document.getElementById('bulkCheckoutPopup').addEventListener('click', function(e) {
            if (e.target === this) { 
                document.getElementById('bulkCheckoutPopup').classList.remove('active');
                window.location.reload();
            }
        });

        document.getElementById('bulkCheckinPopup').addEventListener('click', function(e) {
            if (e.target === this) { 
                document.getElementById('bulkCheckinPopup').classList.remove('active');
                window.location.reload();
            }
        });

        // ============================================
        // UPDATE CHECKED IN COUNT
        // ============================================
        function updateCheckedInCount() {
            const count = document.querySelectorAll('.checked-in-item').length;
            const badge = document.getElementById('checkedCountBadge');
            if (badge) {
                badge.textContent = count + ' anak';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCheckedInCount();
            console.log('✅ DOM Loaded - canCheckout:', canCheckout);
            console.log('✅ DOM Loaded - isCheckoutMode:', isCheckoutMode);
            
            // Auto refresh setiap 30 saat untuk detect perubahan waktu
            setInterval(function() {
                location.reload();
            }, 30000);
        });
    </script>

</body>
</html>