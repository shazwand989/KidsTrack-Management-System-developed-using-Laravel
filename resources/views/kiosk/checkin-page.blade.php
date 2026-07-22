<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>KidsTrack - Check-in</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            transition: background 0.5s ease;
        }

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

        .checkin-card {
            background: white;
            border-radius: 30px;
            padding: 40px 35px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .checkin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
        }
        .main-parent .checkin-card::before { background: linear-gradient(90deg, #059669, #10b981); }
        .second-parent .checkin-card::before { background: linear-gradient(90deg, #6d28d9, #8b5cf6); }
        .guardian .checkin-card::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
        .admin .checkin-card::before { background: linear-gradient(90deg, #334155, #475569); }

        .checkin-card .logo { font-size: 64px; margin-bottom: 10px; }
        .checkin-card h1 { font-size: 24px; color: #1f2937; margin-bottom: 5px; }

        .greeting {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 12px;
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
            padding: 8px 16px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .role-info .role-icon { font-size: 20px; }
        .role-info .role-name { font-weight: 700; }
        .role-info .role-name.main { color: #059669; }
        .role-info .role-name.second { color: #6d28d9; }
        .role-info .role-name.guardian { color: #d97706; }
        .role-info .role-name.admin { color: #1e293b; }

        /* ============================================
           🔥 TIMER INFO BOX - DARI DATABASE
           ============================================ */
        .timer-info-box {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 15px;
            text-align: left;
        }
        .timer-info-box .timer-title {
            font-weight: 700;
            font-size: 14px;
            color: #065f46;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .timer-info-box .timer-title .date-display {
            font-weight: 600;
            font-size: 13px;
            background: #d1fae5;
            padding: 2px 12px;
            border-radius: 20px;
            color: #065f46;
        }
        .timer-info-box .timer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 13px;
            border-bottom: 1px solid #d1fae5;
        }
        .timer-info-box .timer-row:last-child {
            border-bottom: none;
        }
        .timer-info-box .timer-row .slot-label {
            font-weight: 600;
            color: #1f2937;
        }
        .timer-info-box .timer-row .slot-time {
            font-weight: 700;
            color: #065f46;
        }
        .timer-info-box .timer-row .slot-status {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 10px;
            font-weight: 600;
        }
        .timer-info-box .timer-row .slot-status.open {
            background: #d1fae5;
            color: #065f46;
        }
        .timer-info-box .timer-row .slot-status.closed {
            background: #fee2e2;
            color: #991b1b;
        }
        .timer-info-box .timer-row .slot-status.soon {
            background: #fef3c7;
            color: #92400e;
        }
        .timer-info-box .timer-row .slot-status.active {
            background: #dbeafe;
            color: #1e40af;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .timer-info-box .no-timer {
            color: #6b7280;
            font-size: 13px;
            text-align: center;
            padding: 8px 0;
        }

        /* ============================================
           🔥 CALENDAR NAVIGATION
           ============================================ */
        .calendar-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border-radius: 12px;
            padding: 8px 12px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            flex-wrap: wrap;
            gap: 8px;
        }
        .calendar-nav .nav-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            color: #6b7280;
        }
        .calendar-nav .nav-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
        }
        .calendar-nav .date-label {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            flex: 1;
            text-align: center;
        }
        .calendar-nav .today-btn {
            font-size: 11px;
            background: #dbeafe;
            color: #1e40af;
            border: none;
            padding: 4px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
        }
        .calendar-nav .today-btn:hover {
            background: #bfdbfe;
        }

        /* 🔥 CURRENT SLOT DISPLAY */
        .current-slot-box {
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
        }
        .current-slot-box.morning {
            background: #dbeafe;
            color: #1e40af;
        }
        .current-slot-box.evening {
            background: #fce7f3;
            color: #9d174d;
        }
        .current-slot-box.outside {
            background: #fef3c7;
            color: #92400e;
        }

        /* 🔥 CHECKIN STATUS BOX */
        .checkin-status-box {
            border-radius: 16px;
            padding: 15px 20px;
            margin: 15px 0;
            text-align: center;
            border: 3px solid;
        }
        .checkin-status-box.on-time {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .checkin-status-box.late {
            background: #fef2f2;
            border-color: #ef4444;
        }
        .checkin-status-box.closed {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        .checkin-status-box .status-icon { font-size: 32px; display: block; }
        .checkin-status-box .status-text { font-size: 18px; font-weight: 700; margin-top: 4px; }
        .checkin-status-box .status-text.on-time { color: #16a34a; }
        .checkin-status-box .status-text.late { color: #dc2626; }
        .checkin-status-box .status-text.closed { color: #6b7280; }
        .checkin-status-box .status-sub { font-size: 13px; color: #6b7280; margin-top: 2px; }

        /* 🔥 WARNING BOX */
        .warning-box {
            background: #fef2f2;
            border: 2px solid #fca5a5;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 10px 0;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
            display: none;
        }
        .warning-box.show {
            display: block;
        }
        .warning-box .warning-icon { font-size: 20px; margin-right: 8px; }

        .child-box {
            border-radius: 16px;
            padding: 20px;
            margin: 15px 0;
            border: 3px solid #e5e7eb;
            transition: border-color 0.3s ease;
        }
        .child-box.main-parent-border { border-color: #10b981; background: #f0fdf4; }
        .child-box.second-parent-border { border-color: #8b5cf6; background: #f5f3ff; }
        .child-box.guardian-border { border-color: #f59e0b; background: #fffbeb; }
        .child-box.admin-border { border-color: #475569; background: #f1f5f9; }

        .child-box .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 700;
            transition: background 0.3s ease;
        }
        .child-box .avatar.main-parent-avatar { background: linear-gradient(135deg, #059669, #10b981); }
        .child-box .avatar.second-parent-avatar { background: linear-gradient(135deg, #6d28d9, #8b5cf6); }
        .child-box .avatar.guardian-avatar { background: linear-gradient(135deg, #d97706, #f59e0b); }
        .child-box .avatar.admin-avatar { background: linear-gradient(135deg, #334155, #475569); }

        .child-box .name { font-size: 22px; font-weight: 700; color: #1f2937; }
        .child-box .class { font-size: 14px; color: #6b7280; margin-top: 4px; }

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
        .checked-in-item .name { font-weight: 600; font-size: 14px; }
        .checked-in-item .class { font-size: 12px; color: #6b7280; }
        .checked-in-item .time { font-size: 11px; color: #059669; font-weight: 600; }
        .checked-in-empty { text-align: center; color: #94a3b8; padding: 10px 0; font-size: 14px; }
        .checked-in-list-scroll { max-height: 150px; overflow-y: auto; }
        .checked-in-list-scroll::-webkit-scrollbar { width: 4px; }
        .checked-in-list-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .checked-in-list-scroll::-webkit-scrollbar-thumb { background: #6ee7b7; border-radius: 10px; }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { font-weight: 500; color: #6b7280; font-size: 14px; }
        .info-row .value { font-weight: 600; color: #1f2937; font-size: 14px; }

        .status-badge {
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            margin: 15px 0;
        }
        .status-badge.on-time { background: #d1fae5; color: #065f46; }
        .status-badge.checked { background: #dbeafe; color: #1e40af; }
        .status-badge.checkout-done { background: #fef3c7; color: #92400e; }
        .status-badge.late { background: #fee2e2; color: #991b1b; }

        .late-reason-section {
            display: none;
            background: #fef2f2;
            border-radius: 12px;
            padding: 15px;
            margin: 10px 0;
            text-align: left;
        }
        .late-reason-section.show { display: block; }
        .late-reason-section label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #991b1b;
            margin-bottom: 8px;
        }
        .late-reason-section select,
        .late-reason-section textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
            margin-bottom: 8px;
        }
        .late-reason-section select:focus,
        .late-reason-section textarea:focus {
            outline: none;
            border-color: #6d28d9;
        }
        .late-reason-section textarea { resize: vertical; min-height: 60px; }

        .btn-confirm {
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
            margin-top: 10px;
        }
        .btn-confirm:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4);
        }
        .btn-confirm:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .btn-checkout {
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
        .btn-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-checkout-disabled {
            width: 100%;
            padding: 16px;
            background: #e5e7eb;
            color: #9ca3af;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: not-allowed;
            margin-top: 10px;
        }

        .btn-checkin-all {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-checkin-all:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
        }
        .btn-checkin-all:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-done {
            width: 100%;
            padding: 16px;
            background: #9ca3af;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: not-allowed;
            margin-top: 10px;
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

        .checkout-info {
            background: #fef3c7;
            border-radius: 10px;
            padding: 10px;
            margin: 10px 0;
            font-size: 13px;
            color: #92400e;
            transition: all 0.3s;
        }
        .checkout-info.active {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #93c5fd;
        }
        .checkout-info.active.late {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
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

        .popup-box {
            background: white;
            border-radius: 32px;
            padding: 40px 35px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: slideUp 0.4s ease;
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .popup-icon { font-size: 64px; margin-bottom: 10px; }
        .popup-box h2 { font-size: 24px; font-weight: 700; color: #16a34a; margin-bottom: 8px; }
        .popup-box h2.auto-checkout { color: #7c3aed; }
        .popup-box .popup-sub { color: #6b7280; font-size: 15px; margin-bottom: 15px; }
        .popup-child-detail {
            background: #f3f4f6;
            border-radius: 14px;
            padding: 15px;
            margin: 10px 0;
        }
        .popup-child-detail.auto-checkout { background: #ede9fe; }
        .popup-child-detail .child-name { font-size: 20px; font-weight: 700; color: #1f2937; }
        .popup-child-detail .child-class { font-size: 14px; color: #6b7280; margin-top: 4px; }
        .popup-checked-list {
            background: #ecfdf5;
            border-radius: 12px;
            padding: 12px;
            margin: 10px 0;
            text-align: left;
            max-height: 150px;
            overflow-y: auto;
        }
        .popup-checked-list .list-title {
            font-weight: 600;
            color: #065f46;
            font-size: 13px;
            margin-bottom: 8px;
            text-align: center;
        }
        .popup-checked-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 8px;
            background: white;
            border-radius: 6px;
            margin-bottom: 3px;
            font-size: 13px;
        }
        .popup-checked-item .name { font-weight: 600; color: #1f2937; }
        .popup-checked-item .time { font-size: 11px; color: #059669; }
        .popup-checked-list::-webkit-scrollbar { width: 3px; }
        .popup-checked-list::-webkit-scrollbar-thumb { background: #6ee7b7; border-radius: 10px; }

        .popup-status {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin: 10px 0;
        }
        .popup-status.on-time { background: #d1fae5; color: #065f46; }
        .popup-status.late { background: #fee2e2; color: #991b1b; }
        .popup-status.auto-checkout-status { background: #ede9fe; color: #5b21b6; }
        .popup-time { font-size: 14px; color: #6b7280; margin-bottom: 15px; }

        .popup-btn-group { display: flex; flex-direction: column; gap: 10px; }
        .popup-btn-success {
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
        }
        .popup-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
        }
        .popup-btn-success.auto-checkout-btn {
            background: linear-gradient(135deg, #7c3aed, #9333ea);
        }
        .popup-btn-success.auto-checkout-btn:hover {
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }
        .popup-btn-secondary {
            padding: 14px;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            background: #f3f4f6;
            color: #374151;
            transition: all 0.3s;
            width: 100%;
        }
        .popup-btn-secondary:hover { background: #e5e7eb; }

        .result-stats {
            display: flex;
            justify-content: center;
            gap: 10px;
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
        .stat.late { background: #fee2e2; color: #991b1b; }
        .stat.already { background: #dbeafe; color: #2563eb; }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 16px;
            margin: 5px 0;
            background: #f8fafc;
            border-radius: 10px;
        }
        .result-item.checked_in { border-left: 4px solid #22c55e; }
        .result-item.late { border-left: 4px solid #ef4444; }
        .result-item.already_checked { border-left: 4px solid #3b82f6; }
        .result-item .name { font-weight: 600; font-size: 14px; }
        .result-item .status { font-size: 13px; }
        .result-item .time { font-size: 12px; color: #94a3b8; }

        .popup-box::-webkit-scrollbar { width: 4px; }
        .popup-box::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .popup-box::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 10px; }

        @media (max-width: 480px) {
            .checkin-card { padding: 25px 20px; }
            .checkin-card h1 { font-size: 20px; }
            .child-box .name { font-size: 18px; }
            .popup-box { padding: 30px 20px; }
            .popup-icon { font-size: 48px; }
            .popup-box h2 { font-size: 20px; }
            .result-stats { gap: 6px; }
            .result-stats .stat { font-size: 11px; padding: 3px 10px; }
            .result-item { flex-wrap: wrap; gap: 4px; }
            .result-item .time { width: 100%; text-align: right; }
            .checked-in-item { flex-wrap: wrap; }
            .checked-in-item .time { width: 100%; text-align: right; padding-left: 40px; }
            .calendar-nav { flex-wrap: wrap; gap: 8px; justify-content: center; }
        }
    </style>
</head>

<body class="{{ $roleData['class'] ?? 'main-parent' }}">

    <div class="checkin-card {{ $roleData['class'] ?? 'main-parent' }}">
        <div class="logo">🧸</div>
        <h1>Welcome to KidsTrack</h1>

        <div class="role-badge {{ $roleData['badge_class'] ?? 'main-parent' }}">
            {{ $roleData['badge_text'] ?? '👨‍👩‍👦 Main Parent' }}
        </div>

        <div class="role-info">
            <span class="role-icon">{{ $roleData['icon'] ?? '👨‍👩‍👦' }}</span>
            <span>Logged in as</span>
            <span class="role-name {{ $roleData['name_class'] ?? 'main' }}">
                {{ $roleData['display_name'] ?? 'Main Parent' }}
            </span>
        </div>

        <div class="greeting">Hello, {{ $parentName ?? $parent->name ?? 'Parent' }}!</div>

        <!-- ============================================
        🔥 CALENDAR NAVIGATION
        ============================================ -->
        <div class="calendar-nav">
            <button class="nav-btn" onclick="changeDate(-1)">◀</button>
            <span class="date-label" id="dateLabel">{{ \Carbon\Carbon::parse($selectedDate ?? now())->format('d M Y (l)') }}</span>
            <button class="nav-btn" onclick="changeDate(1)">▶</button>
            <button class="today-btn" onclick="goToToday()"><i class="fas fa-calendar-alt"></i> Hari Ini</button>
        </div>

        <!-- ============================================
        🔥 TIMER INFO BOX - DARI DATABASE
        ============================================ -->
        <div class="timer-info-box" id="timerInfoBox">
            <div class="timer-title">
                <span>⏱️ Waktu Operasi</span>
                <span class="date-display" id="dateDisplay">{{ \Carbon\Carbon::parse($selectedDate ?? now())->format('d/m/Y') }}</span>
            </div>
            <div id="timerInfoContent">
                @if($timerSetting)
                <div style="text-align:center; font-weight:600; color:#065f46; margin-bottom:8px; font-size:14px; background:#d1fae5; padding:4px; border-radius:8px;">
                    <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($selectedDate ?? now())->format('d/m/Y') }} ({{ $now->format('l') }})
                </div>
                <div class="timer-row">
                    <span class="slot-label"> Morning (Check-in)</span>
                    <span class="slot-time">{{ $timerSetting->morning_start }} - {{ $timerSetting->morning_end }}</span>
                    <span class="slot-status active"> Aktif</span>
                </div>
                <div class="timer-row">
                    <span class="slot-label">🌙 Evening (Check-out)</span>
                    <span class="slot-time">{{ $timerSetting->evening_start }} - {{ $timerSetting->evening_end }}</span>
                    <span class="slot-status closed">🔒 Tutup</span>
                </div>
                @else
                <div class="no-timer">⏳ Memuatkan waktu operasi...</div>
                @endif
            </div>
        </div>

        <!-- ============================================
        🔥 CURRENT SLOT DISPLAY
        ============================================ -->
        <div id="currentSlotDisplay"></div>

        <!-- ============================================
        🔥 CHECKIN STATUS BOX
        ============================================ -->
        <div class="checkin-status-box" id="checkinStatusBox">
            <span class="status-icon" id="statusIcon">⏳</span>
            <div class="status-text" id="statusText">Memeriksa status...</div>
            <div class="status-sub" id="statusSub">Sila tunggu sebentar</div>
        </div>

        <!-- ============================================
        🔥 WARNING BOX
        ============================================ -->
        <div class="warning-box" id="warningBox">
            <span class="warning-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span id="warningMessage">Di luar waktu operasi!</span>
        </div>

        @php
            $allCheckedIn = collect();
            if(isset($child) && $child->checked_in_today && !$child->checked_out_today) {
                $allCheckedIn->push($child);
            }
            if(isset($checkedChildren) && $checkedChildren->count() > 0) {
                $allCheckedIn = $allCheckedIn->merge($checkedChildren);
            }
        @endphp

        <div class="checked-in-list-container">
            <div class="header">
                <span class="title"><i class="fas fa-check-circle"></i> Telah Check-in Hari Ini</span>
                <span class="count-badge">{{ $allCheckedIn->count() }} anak</span>
            </div>
            <div class="checked-in-list-scroll">
                @if($allCheckedIn->count() > 0)
                    @foreach($allCheckedIn as $checkedChild)
                        <div class="checked-in-item">
                            <div class="left">
                                <div class="avatar-small">{{ strtoupper(substr($checkedChild->name, 0, 1)) }}</div>
                                <div>
                                    <div class="name">{{ $checkedChild->name }}</div>
                                    <div class="class"><i class="fas fa-school"></i> {{ $checkedChild->classroom->name ?? '-' }}</div>
                                </div>
                            </div>
                            <span class="time"><i class="fas fa-check-circle"></i> {{ \Carbon\Carbon::parse($checkedChild->checked_in_time)->format('h:i A') }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="checked-in-empty">Tiada anak yang check-in hari ini</div>
                @endif
            </div>
        </div>

        <div class="info-row">
            <span class="label">🕐 Masa</span>
            <span class="value">{{ $currentTime }}</span>
        </div>

        <!-- ============================================
        🔥🔥🔥 BAHAGIAN CHECKOUT - IKUT DATABASE! 🔥🔥🔥
        ============================================ -->
        @php
            // Use classroom schedule for checkout timing
            $classEnd = $child->classroom->end_time ?? '17:00';
            $classEndInt = (int) str_replace(':', '', substr($classEnd, 0, 5));
            
            $canCheckout = $hasCheckin && !$hasCheckout;
            $checkoutMessage = '<i class="fas fa-check-circle"></i> Sedia untuk check-out';
            $checkoutInfoClass = 'active';
            $isLateCheckout = false;

            if ($hasCheckin) {
                $currentTimeInt = (int) $now->format('Hi');

                if ($currentTimeInt > $classEndInt) {
                    $isLateCheckout = true;
                    $checkoutMessage = ' Late Checkout (Selepas ' . substr($classEnd, 0, 5) . ')';
                    $checkoutInfoClass = 'active late';
                } else {
                    $checkoutMessage = '<i class="fas fa-check-circle"></i> Waktu checkout sehingga: ' . substr($classEnd, 0, 5);
                    $checkoutInfoClass = 'active';
                }
            }
        @endphp

        @if($hasCheckout)
        <div class="status-badge checkout-done"><i class="fas fa-hand-wave"></i> Sudah Check-out</div>
        <button class="btn-done" disabled><i class="fas fa-check-circle"></i> Selesai</button>

        @elseif($hasCheckin)
        <div class="status-badge checked"><i class="fas fa-check-circle"></i> Sudah Check-in</div>

        <button class="btn-checkout" id="btnCheckout" onclick="submitAttendance('checkout')"
                style="display: none;">
            <i class="fas fa-hand-wave"></i> Confirm Check-out
        </button>

        <button class="btn-checkout-disabled" id="btnCheckoutDisabled" style="display: none;" disabled>
            <i class="fas fa-hand-wave"></i> Confirm Check-out
        </button>

        <div class="checkout-info active" id="checkoutInfo">
            @if($isLateCheckout)
             Late Checkout (Melebihi waktu operasi)
            @else
            <i class="fas fa-check-circle"></i> Sedia untuk check-out
            @endif
        </div>

        @else

        @if($isLate)
        <div class="status-badge late"> Late Check-in</div>
        <div class="late-reason-section show" id="lateReasonSection">
            <label><i class="fas fa-edit"></i> Sila pilih sebab anda lewat:</label>
            <select id="lateReasonSelect">
                <option value="">-- Pilih Sebab --</option>
                <option value="Kesesakan lalu lintas">🚗 Kesesakan lalu lintas</option>
                <option value="Bangun lewat">😴 Bangun lewat</option>
                <option value="Kecemasan">🚨 Kecemasan</option>
                <option value="Cuaca buruk">🌧️ Cuaca buruk</option>
                <option value="Kenderaan rosak">🔧 Kenderaan rosak</option>
                <option value="Lain-lain"><i class="fas fa-edit"></i> Lain-lain</option>
            </select>
            <textarea id="lateReasonDetail" placeholder="Sila nyatakan sebab lain (jika ada)..."></textarea>
        </div>
        @else
        <div class="status-badge on-time"><i class="fas fa-check-circle"></i> On Time</div>
        @endif

        @if(isset($allChildren) && $allChildren->count() > 1)
        <button class="btn-checkin-all" onclick="checkinAll()" id="btnCheckinAll">
            ⚡ Check-in Semua ({{ $allChildren->count() }})
        </button>
        @endif

        <button class="btn-confirm" onclick="submitAttendance('checkin')" id="btnCheckin">
            <i class="fas fa-check-circle"></i> Confirm Check-in
        </button>
        @endif

        <button class="btn-back" onclick="window.location.href='/kiosk'"><i class="fas fa-arrow-left"></i> Kembali ke Kiosk</button>
    </div>

    <!-- POPUP CHECKIN SUCCESS -->
    <div class="popup-overlay" id="successPopup">
        <div class="popup-box" id="popupBox">
            <div class="popup-icon" id="popupIcon"><i class="fas fa-check-circle"></i></div>
            <h2 id="popupTitle">Check-in Berjaya!</h2>
            <p class="popup-sub" id="popupSub">Anak anda telah berjaya check-in.</p>

            <div class="popup-child-detail" id="popupDetail">
                <div class="child-name" id="popupChildName">🧸 Nama Anak</div>
                <div class="child-class" id="popupChildClass"><i class="fas fa-school"></i> Kelas</div>
            </div>

            <div class="popup-checked-list">
                <div class="list-title"><i class="fas fa-clipboard-list"></i> Anak yang telah check-in hari ini</div>
                <div id="popupCheckedItems"></div>
            </div>

            <div class="popup-status on-time" id="popupStatus"><i class="fas fa-check-circle"></i> On Time</div>
            <div class="popup-time" id="popupTime">🕐 Masa: --:--</div>

            <div class="popup-btn-group">
                <button class="popup-btn-success" id="popupBtnSuccess" onclick="closeSuccessPopupManual()">
                    👍 Terima Kasih
                </button>
                <button class="popup-btn-secondary" onclick="goToKiosk()">
                    <i class="fas fa-home"></i> Kembali ke Kiosk
                </button>
            </div>
        </div>
    </div>

    <!-- POPUP BULK CHECKIN -->
    <div class="popup-overlay" id="bulkSuccessPopup">
        <div class="popup-box">
            <div class="popup-icon"><i class="fas fa-check-circle"></i></div>
            <h2>Check-in Berjaya!</h2>
            <p class="popup-sub" id="bulkPopupSub">Semua anak telah berjaya check-in.</p>
            <div id="bulkResults"></div>
            <button class="popup-btn-success" onclick="closeBulkPopup()">👍 Selesai</button>
        </div>
    </div>

    <script>
        // 🔥 GET DATE FROM URL PARAMETER
        const urlParams = new URLSearchParams(window.location.search);
        let selectedDate = urlParams.get('date') || '{{ now()->format("Y-m-d") }}';
        let selectedDay = new Date(selectedDate + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long' });

        let childId = {{ $child->id }};
        let isLate = {{ $isLate ? 'true' : 'false' }};
        let parentId = {{ $parent->id ?? 0 }};
        let hasCheckin = {{ $hasCheckin ? 'true' : 'false' }};
        let hasCheckout = {{ $hasCheckout ? 'true' : 'false' }};

        // 🔥🔥🔥 VARIABLE UNTUK CHECKOUT - AKAN DIUPDATE OLEH JAVASCRIPT 🔥🔥🔥
        let canCheckout = {{ $canCheckout ? 'true' : 'false' }};
        let isLateCheckout = false;
        let eveningStart = '{{ $timerSetting->evening_start ?? '16:30' }}';
        let eveningEnd = '{{ $timerSetting->evening_end ?? '18:30' }}';
        let morningStartTime = '{{ $timerSetting->morning_start ?? '07:00' }}';
        let morningEndTime = '{{ $timerSetting->morning_end ?? '12:00' }}';

        let checkedInChildren = @json($checkedInData ?? []);
        let timerSettings = {};
        let currentSlot = null;
        let isSubmitting = false; // 🔥 Prevent double submission

        console.log('<i class="fas fa-search"></i> Initial canCheckout from PHP:', canCheckout);
        console.log('<i class="fas fa-search"></i> hasCheckin:', hasCheckin);
        console.log('<i class="fas fa-search"></i> hasCheckout:', hasCheckout);

        // 🔥 IMMEDIATE: If already checked in/out, handle properly
        if (hasCheckout) {
            updateCheckinStatus('closed', '<i class="fas fa-hand-wave"></i> Sudah Check-out', 'Anak ini sudah check-out hari ini');
            document.getElementById('btnCheckin')?.setAttribute('disabled', 'disabled');
            document.getElementById('btnCheckinAll')?.setAttribute('disabled', 'disabled');
            document.getElementById('btnCheckin') && (document.getElementById('btnCheckin').style.display = 'none');
            document.getElementById('btnCheckinAll') && (document.getElementById('btnCheckinAll').style.display = 'none');
            const btn = document.getElementById('btnCheckout');
            const btnDisabled = document.getElementById('btnCheckoutDisabled');
            if (btn) btn.style.display = 'none';
            if (btnDisabled) btnDisabled.style.display = 'none';
        } else if (hasCheckin && !hasCheckout) {
            updateCheckinStatus('closed', '<i class="fas fa-check-circle"></i> Sudah Check-in', 'Sila checkout untuk balik');
            document.getElementById('btnCheckin')?.setAttribute('disabled', 'disabled');
            document.getElementById('btnCheckinAll')?.setAttribute('disabled', 'disabled');
            canCheckout = true;
            const btn = document.getElementById('btnCheckout');
            const btnDisabled = document.getElementById('btnCheckoutDisabled');
            if (btn) { btn.style.display = 'block'; btn.disabled = false; }
            if (btnDisabled) btnDisabled.style.display = 'none';
        }

        // Update checkout info with actual timer values
        const checkoutInfoEl = document.getElementById('checkoutInfo');
        if (checkoutInfoEl && !hasCheckin) {
            checkoutInfoEl.textContent = ' Check-in: ' + morningStartTime + ' - ' + morningEndTime + ' | 🌙 Check-out: ' + eveningStart + ' - ' + eveningEnd;
        }

        // ============================================
        // 🔥 CHECK CHECKOUT STATUS - GUNA JAVASCRIPT!
        // ============================================
        function checkCheckoutStatus() {
            const now = new Date();
            const currentTime = parseInt(now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0'));

            // 🔥 AMBIL DARI TIMER SETTINGS
            const eveningStartInt = parseInt(eveningStart.replace(':', ''));
            const eveningEndInt = parseInt(eveningEnd.replace(':', ''));

            console.log('<i class="fas fa-search"></i> Checking checkout - currentTime:', currentTime, 'eveningStart:', eveningStartInt, 'eveningEnd:', eveningEndInt);
            console.log('<i class="fas fa-search"></i> hasCheckin:', hasCheckin, 'hasCheckout:', hasCheckout);

            // 🔥🔥🔥 TETAPKAN canCheckout — BOLEH CHECKOUT BILA-BILA SELEPAS CHECKIN 🔥🔥🔥
            if (hasCheckout) {
                canCheckout = false;
                console.log('<i class="fas fa-times-circle"></i> canCheckout = FALSE (Sudah checkout)');
            } else if (hasCheckin) {
                const isValidEvening = !Number.isNaN(eveningStartInt) && !Number.isNaN(eveningEndInt);
                if (isValidEvening) {
                    canCheckout = currentTime >= eveningStartInt;
                } else {
                    // If no configured evening slot is available, keep checkout available after check-in
                    canCheckout = true;
                }

                if (!canCheckout) {
                    isLateCheckout = false;
                    console.log('<i class="fas fa-times-circle"></i> canCheckout = FALSE (Sebelum waktu checkout bermula)');
                } else if (currentTime <= eveningEndInt) {
                    isLateCheckout = false;
                    console.log('<i class="fas fa-check-circle"></i> canCheckout = TRUE (On-time checkout)');
                } else {
                    isLateCheckout = true;
                    console.log('<i class="fas fa-check-circle"></i> canCheckout = TRUE (Late Checkout)');
                }
            } else {
                canCheckout = false;
                console.log('<i class="fas fa-times-circle"></i> canCheckout = FALSE (Belum check-in)');
            }

            // 🔥 UPDATE UI BUTTON CHECKOUT
            updateCheckoutButton();
        }

        // ============================================
        // 🔥 UPDATE CHECKOUT BUTTON UI
        // ============================================
        function updateCheckoutButton() {
            const checkoutBtn = document.getElementById('btnCheckout');
            const checkoutDisabled = document.getElementById('btnCheckoutDisabled');
            const checkoutInfo = document.getElementById('checkoutInfo');

            console.log('<i class="fas fa-search"></i> updateCheckoutButton - canCheckout:', canCheckout);
            console.log('<i class="fas fa-search"></i> checkoutBtn exists:', !!checkoutBtn);
            console.log('<i class="fas fa-search"></i> checkoutDisabled exists:', !!checkoutDisabled);

            if (!checkoutBtn && !checkoutDisabled) {
                console.log('<i class="fas fa-exclamation-triangle"></i> No checkout button found');
                return;
            }

            if (canCheckout) {
                // 🔥 Tunjuk button aktif
                if (checkoutBtn) {
                    checkoutBtn.style.display = 'block';
                    checkoutBtn.disabled = false;
                    console.log('<i class="fas fa-check-circle"></i> Checkout button ENABLED');
                }
                if (checkoutDisabled) {
                    checkoutDisabled.style.display = 'none';
                }
                if (checkoutInfo) {
                    if (isLateCheckout) {
                        checkoutInfo.textContent = ' Late Checkout (Melebihi waktu operasi)';
                        checkoutInfo.className = 'checkout-info active late';
                    } else {
                        checkoutInfo.innerHTML = '<i class="fas fa-check-circle"></i> Waktu checkout: ' + eveningStart + ' - ' + eveningEnd;
                        checkoutInfo.className = 'checkout-info active';
                    }
                }
            } else {
                // 🔥 Tunjuk button disabled
                if (checkoutBtn) {
                    checkoutBtn.style.display = 'none';
                    console.log('<i class="fas fa-times-circle"></i> Checkout button HIDDEN');
                }
                if (checkoutDisabled) {
                    checkoutDisabled.style.display = 'block';
                    checkoutDisabled.disabled = true;
                    console.log('<i class="fas fa-times-circle"></i> Checkout disabled button SHOWN');
                }
                if (checkoutInfo) {
                    checkoutInfo.textContent = '🕐 Checkout bermula pada ' + eveningStart;
                    checkoutInfo.className = 'checkout-info';
                }
            }
        }

        // ============================================
        // 🔥 CHANGE DATE FUNCTION
        // ============================================
        function changeDate(days) {
            const date = new Date(selectedDate);
            date.setDate(date.getDate() + days);
            const newDate = date.toISOString().split('T')[0];
            window.location.href = window.location.pathname + '?date=' + newDate;
        }

        function goToToday() {
            const today = new Date().toISOString().split('T')[0];
            window.location.href = window.location.pathname + '?date=' + today;
        }

        // ============================================
        // 🔥 LOAD TIMER INFO FROM DATABASE BY DATE
        // ============================================
        function loadTimerInfo() {
            fetch('/get-timer-settings?date=' + selectedDate)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        timerSettings = data.data;
                        renderTimerInfo(data.data);
                        checkCurrentSlot(data.data);
                        // 🔥🔥🔥 PANGGIL CHECK CHECKOUT STATUS SELEPAS RENDER 🔥🔥🔥
                        checkCheckoutStatus();
                    } else {
                        document.getElementById('timerInfoContent').innerHTML =
                            '<div class="no-timer"><i class="fas fa-exclamation-triangle"></i> Gagal memuat waktu operasi</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading timer info:', error);
                    document.getElementById('timerInfoContent').innerHTML =
                        '<div class="no-timer"><i class="fas fa-exclamation-triangle"></i> Ralat memuat waktu operasi</div>';
                });
        }

        function renderTimerInfo(timerSettings) {
            const now = new Date();
            const currentTime = parseInt(now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0'));

            let timer = null;
            if (Array.isArray(timerSettings)) {
                timer = timerSettings.find(t => t.day_name && t.day_name.toLowerCase().includes(selectedDay.toLowerCase()));
            } else {
                timer = timerSettings[selectedDay];
            }

            if (!timer) {
                document.getElementById('timerInfoContent').innerHTML =
                    '<div class="no-timer"><i class="fas fa-calendar-alt"></i> Tiada waktu operasi untuk ' + selectedDay + '</div>';
                return;
            }

            let morningStart, morningEnd, eveningStartLocal, eveningEndLocal;

            if (timer.morning && typeof timer.morning === 'object') {
                morningStart = timer.morning.start;
                morningEnd = timer.morning.end;
                eveningStartLocal = timer.evening.start;
                eveningEndLocal = timer.evening.end;
            } else {
                morningStart = timer.morning_start || timer.morning?.start || '--:--';
                morningEnd = timer.morning_end || timer.morning?.end || '--:--';
                eveningStartLocal = timer.evening_start || timer.evening?.start || '--:--';
                eveningEndLocal = timer.evening_end || timer.evening?.end || '--:--';
            }

            // 🔥🔥🔥 UPDATE EVENING START & END 🔥🔥🔥
            if (eveningStartLocal !== '--:--' && eveningEndLocal !== '--:--') {
                eveningStart = eveningStartLocal;
                eveningEnd = eveningEndLocal;
                console.log('<i class="fas fa-search"></i> Evening slot updated:', eveningStart, '-', eveningEnd);

                // 🔥🔥🔥 CHECK CHECKOUT STATUS SELEPAS DAPAT TIMER 🔥🔥🔥
                checkCheckoutStatus();
            }
            // Update morning times too
            if (morningStart !== '--:--') morningStartTime = morningStart;
            if (morningEnd !== '--:--') morningEndTime = morningEnd;

            function checkSlotStatus(start, end) {
                if (start === '--:--' || end === '--:--') return 'closed';
                const startTime = parseInt(start.replace(':', ''));
                const endTime = parseInt(end.replace(':', ''));

                if (currentTime >= startTime && currentTime <= endTime) {
                    return 'active';
                } else if (currentTime < startTime) {
                    return 'soon';
                } else {
                    return 'closed';
                }
            }

            const morningStatus = checkSlotStatus(morningStart, morningEnd);
            const eveningStatus = checkSlotStatus(eveningStartLocal, eveningEndLocal);

            function getStatusLabel(status) {
                const map = {
                    'active': ' Aktif',
                    'soon': '⏳ Akan Datang',
                    'closed': '🔒 Tutup'
                };
                return map[status] || '❓';
            }

            function getStatusClass(status) {
                const map = {
                    'active': 'active',
                    'soon': 'soon',
                    'closed': 'closed'
                };
                return map[status] || 'closed';
            }

            let html = `
                <div style="text-align:center; font-weight:600; color:#065f46; margin-bottom:8px; font-size:14px; background:#d1fae5; padding:4px; border-radius:8px;">
                    <i class="fas fa-calendar-alt"></i> ${selectedDate} (${selectedDay})
                </div>
                <div class="timer-row">
                    <span class="slot-label"> Morning (Check-in)</span>
                    <span class="slot-time">${morningStart} - ${morningEnd}</span>
                    <span class="slot-status ${getStatusClass(morningStatus)}">${getStatusLabel(morningStatus)}</span>
                </div>
                <div class="timer-row">
                    <span class="slot-label">🌙 Evening (Check-out)</span>
                    <span class="slot-time">${eveningStartLocal} - ${eveningEndLocal}</span>
                    <span class="slot-status ${getStatusClass(eveningStatus)}">${getStatusLabel(eveningStatus)}</span>
                </div>
            `;

            document.getElementById('timerInfoContent').innerHTML = html;

            if (morningStatus === 'active') {
                currentSlot = { type: 'checkin', label: 'Morning (Check-in)', status: 'active' };
            } else if (eveningStatus === 'active') {
                currentSlot = { type: 'checkout', label: 'Evening (Check-out)', status: 'active' };
            } else {
                currentSlot = null;
            }

            updateCurrentSlotDisplay();

            // 🔥🔥🔥 PANGGIL SEKALI LAGI UNTUK PASTIKAN 🔥🔥🔥
            checkCheckoutStatus();
        }

        // ============================================
        // 🔥 UPDATE CURRENT SLOT DISPLAY
        // ============================================
        function updateCurrentSlotDisplay() {
            const container = document.getElementById('currentSlotDisplay');

            if (!container) return;

            if (!currentSlot) {
                container.innerHTML = `
                    <div class="current-slot-box outside">
                         Late Check-in Available
                    </div>
                `;
                return;
            }

            const slotClass = currentSlot.type === 'checkin' ? 'morning' : 'evening';
            const icon = currentSlot.type === 'checkin' ? '☀️' : '🌙';

            container.innerHTML = `
                <div class="current-slot-box ${slotClass}">
                    ${icon} Current: <strong>${currentSlot.label}</strong>
                </div>
            `;
        }

        // ============================================
        // 🔥 CHECK CURRENT SLOT & UPDATE STATUS
        // ============================================
        function checkCurrentSlot(timerSettings) {
            const now = new Date();
            const currentTime = parseInt(now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0'));

            let timer = null;
            if (Array.isArray(timerSettings)) {
                timer = timerSettings.find(t => t.day_name && t.day_name.toLowerCase().includes(selectedDay.toLowerCase()));
            } else {
                timer = timerSettings[selectedDay];
            }

            if (!timer) {
                updateCheckinStatus('closed', '<i class="fas fa-calendar-alt"></i> Tiada waktu operasi', 'Sila hubungi admin');
                return;
            }

            let morningStart, morningEnd, eveningStartLocal, eveningEndLocal;

            if (timer.morning && typeof timer.morning === 'object') {
                morningStart = timer.morning.start;
                morningEnd = timer.morning.end;
                eveningStartLocal = timer.evening.start;
                eveningEndLocal = timer.evening.end;
            } else {
                morningStart = timer.morning_start || timer.morning?.start || '--:--';
                morningEnd = timer.morning_end || timer.morning?.end || '--:--';
                eveningStartLocal = timer.evening_start || timer.evening?.start || '--:--';
                eveningEndLocal = timer.evening_end || timer.evening?.end || '--:--';
            }

            function isInSlot(start, end) {
                if (start === '--:--' || end === '--:--') return false;
                const startTime = parseInt(start.replace(':', ''));
                const endTime = parseInt(end.replace(':', ''));
                return currentTime >= startTime && currentTime <= endTime;
            }

            const isMorning = isInSlot(morningStart, morningEnd);
            const isEvening = isInSlot(eveningStartLocal, eveningEndLocal);

            if (hasCheckout) {
                updateCheckinStatus('closed', '<i class="fas fa-hand-wave"></i> Sudah Check-out', 'Anak ini sudah check-out hari ini');
                document.getElementById('btnCheckin')?.setAttribute('disabled', 'disabled');
                document.getElementById('btnCheckinAll')?.setAttribute('disabled', 'disabled');
                return;
            }

            if (hasCheckin) {
                updateCheckinStatus('closed', '<i class="fas fa-check-circle"></i> Sudah Check-in', 'Sila checkout untuk balik');
                document.getElementById('btnCheckin')?.setAttribute('disabled', 'disabled');
                document.getElementById('btnCheckinAll')?.setAttribute('disabled', 'disabled');

                const eveningStartInt = !Number.isNaN(parseInt(eveningStart.replace(':', '')))
                    ? parseInt(eveningStart.replace(':', ''))
                    : NaN;
                const eveningEndInt = !Number.isNaN(parseInt(eveningEnd.replace(':', '')))
                    ? parseInt(eveningEnd.replace(':', ''))
                    : NaN;
                const currentTimeInt = parseInt(now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0'));

                if (!Number.isNaN(eveningStartInt) && currentTimeInt < eveningStartInt) {
                    canCheckout = false;
                    isLateCheckout = false;
                    const btn = document.getElementById('btnCheckout');
                    const btnDisabled = document.getElementById('btnCheckoutDisabled');
                    if (btn) { btn.style.display = 'none'; }
                    if (btnDisabled) { btnDisabled.style.display = 'block'; }
                    const checkoutInfo = document.getElementById('checkoutInfo');
                    if (checkoutInfo) {
                        checkoutInfo.textContent = '🕐 Checkout bermula pada ' + eveningStart;
                        checkoutInfo.className = 'checkout-info';
                    }
                    return;
                }

                canCheckout = true;
                const btn = document.getElementById('btnCheckout');
                const btnDisabled = document.getElementById('btnCheckoutDisabled');
                if (btn) { btn.style.display = 'block'; btn.disabled = false; }
                if (btnDisabled) { btnDisabled.style.display = 'none'; }
                return;
            }

            if (isMorning) {
                const endTime = parseInt(morningEnd.replace(':', ''));
                const isLateCheck = currentTime > endTime;

                if (isLateCheck) {
                    updateCheckinStatus('late', ' Check-in Late!', 'Melebihi waktu yang ditetapkan');
                    showWarning('<i class="fas fa-exclamation-triangle"></i> Anda check-in lewat! Sila beri alasan.');
                    document.getElementById('lateReasonSection')?.classList.add('show');
                    isLate = true;
                } else {
                    updateCheckinStatus('on-time', '<i class="fas fa-check-circle"></i> Check-in On Time', 'Dalam waktu yang ditetapkan');
                    hideWarning();
                    document.getElementById('lateReasonSection')?.classList.remove('show');
                    isLate = false;
                }
                document.getElementById('btnCheckin')?.removeAttribute('disabled');
                document.getElementById('btnCheckinAll')?.removeAttribute('disabled');

            } else if (isEvening) {
                updateCheckinStatus('closed', ' Waktu Check-in Tamat', 'Sila scan untuk check-out');
                showWarning('Waktu check-in telah tamat. Sila check-out.');
                document.getElementById('btnCheckin')?.setAttribute('disabled', 'disabled');
                document.getElementById('btnCheckinAll')?.setAttribute('disabled', 'disabled');

            } else {
                // 🔥 OUTSIDE ANY SLOT → ALLOW LATE CHECK-IN
                updateCheckinStatus('late', ' Check-in Late!', 'Di luar waktu operasi — late check-in dibenarkan');
                showWarning('<i class="fas fa-exclamation-triangle"></i> Anda check-in lewat! Di luar waktu operasi.');
                document.getElementById('lateReasonSection')?.classList.add('show');
                isLate = true;
                document.getElementById('btnCheckin')?.removeAttribute('disabled');
                document.getElementById('btnCheckinAll')?.removeAttribute('disabled');
            }
        }

        // ============================================
        // 🔥 UPDATE CHECKIN STATUS UI
        // ============================================
        function updateCheckinStatus(type, text, sub) {
            const box = document.getElementById('checkinStatusBox');
            const icon = document.getElementById('statusIcon');
            const statusText = document.getElementById('statusText');
            const statusSub = document.getElementById('statusSub');

            box.className = 'checkin-status-box';

            if (type === 'on-time') {
                box.classList.add('on-time');
                icon.innerHTML = '<i class="fas fa-check-circle"></i>';
                statusText.className = 'status-text on-time';
                statusText.innerHTML = text;
            } else if (type === 'late') {
                box.classList.add('late');
                icon.textContent = '';
                statusText.className = 'status-text late';
                statusText.innerHTML = text;
            } else {
                box.classList.add('closed');
                icon.textContent = '🚫';
                statusText.className = 'status-text closed';
                statusText.innerHTML = text;
            }

            statusSub.innerHTML = sub || '';
        }

        // ============================================
        // 🔥 WARNING BOX
        // ============================================
        function showWarning(message) {
            const box = document.getElementById('warningBox');
            document.getElementById('warningMessage').textContent = message;
            box.classList.add('show');
        }

        function hideWarning() {
            document.getElementById('warningBox').classList.remove('show');
        }

        // ============================================
        // 🔥 SUBMIT ATTENDANCE
        // ============================================
        function submitAttendance(action) {
            if (isSubmitting) { console.log('<i class="fas fa-exclamation-triangle"></i> Already submitting, ignored'); return; }
            isSubmitting = true;

            // 🔥 Immediately disable ALL buttons
            document.querySelectorAll('button').forEach(b => { b.disabled = true; b.style.opacity = '0.6'; });

            console.log('<i class="fas fa-search"></i> submitAttendance called - action:', action);
            console.log('<i class="fas fa-search"></i> canCheckout:', canCheckout);
            console.log('<i class="fas fa-search"></i> hasCheckin:', hasCheckin);

            if (action == 'checkout') {
                if (!hasCheckin) {
                    alert('<i class="fas fa-exclamation-triangle"></i> Anak ini belum check-in hari ini! Sila check-in dahulu.');
                    return;
                }
                if (!canCheckout) {
                    alert(' Checkout belum boleh! Tunggu waktu evening slot.');
                    return;
                }
            }

            let lateReason = '';

            if (action == 'checkin' && isLate) {
                const select = document.getElementById('lateReasonSelect');
                const detail = document.getElementById('lateReasonDetail');
                if (!select.value) {
                    alert('<i class="fas fa-exclamation-triangle"></i> Sila pilih sebab anda lewat!');
                    select.focus();
                    return;
                }
                lateReason = select.value;
                if (detail.value.trim()) {
                    lateReason += ' - ' + detail.value.trim();
                }
            }

            const btn = document.querySelector('.btn-confirm, .btn-checkout');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳ Memproses...';
            }

            let payload = {
                child_id: childId,
                parent_id: parentId,
                is_late: isLate ? 1 : 0,
                late_reason: lateReason,
                action: action,
                date: selectedDate
            };

            fetch('{{ route('kiosk.submit.attendance') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action == 'checkin') {
                            checkedInChildren.push({
                                name: data.child_name,
                                classroom: data.child_classroom || 'Tiada kelas',
                                time: data.checkin_time,
                                initial: data.child_name.charAt(0).toUpperCase()
                            });
                        }

                        showSuccessPopup({
                            child_name: data.child_name,
                            child_class: data.child_classroom || 'Tiada kelas',
                            time: data.checkin_time || data.checkout_time,
                            is_late: data.is_late || false,
                            action: action,
                            is_auto: data.is_auto || false
                        });
                        if (btn) btn.innerHTML = '<i class="fas fa-check-circle"></i> Berjaya!';

                        setTimeout(() => { window.location.reload(); }, 2500);
                    } else {
                        alert('<i class="fas fa-times-circle"></i> ' + data.message);
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = action == 'checkin' ? '<i class="fas fa-check-circle"></i> Confirm Check-in' : '<i class="fas fa-hand-wave"></i> Confirm Check-out';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<i class="fas fa-times-circle"></i> Ralat: ' + error.message);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = action == 'checkin' ? '<i class="fas fa-check-circle"></i> Confirm Check-in' : '<i class="fas fa-hand-wave"></i> Confirm Check-out';
                    }
                });
        }

        // ============================================
        // CHECKIN ALL
        // ============================================
        function checkinAll() {
            if (isSubmitting) return;
            isSubmitting = true;

            const btn = document.querySelector('.btn-checkin-all');
            if (!btn) return;
            btn.disabled = true;
            btn.textContent = '⏳ Memproses...';

            const childIds = @json($allChildren->pluck('id')->toArray() ?? []);
            const parentId = {{ $parent->id ?? 0 }};

            fetch('{{ route('kiosk.checkin.all') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        parent_id: parentId,
                        child_ids: childIds,
                        date: selectedDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showBulkPopup(data);
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Selesai!';
                    } else {
                        alert('<i class="fas fa-times-circle"></i> ' + data.message);
                        btn.disabled = false;
                        btn.textContent = '⚡ Check-in Semua ({{ $allChildren->count() ?? 0 }})';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('<i class="fas fa-times-circle"></i> Ralat: ' + error.message);
                    btn.disabled = false;
                    btn.textContent = '⚡ Check-in Semua ({{ $allChildren->count() ?? 0 }})';
                });
        }

        // ============================================
        // POPUP FUNCTIONS
        // ============================================
        function showBulkPopup(data) {
            const popup = document.getElementById('bulkSuccessPopup');
            const resultsDiv = document.getElementById('bulkResults');

            let html = `
                <div class="result-stats">
                    <span class="stat checked"><i class="fas fa-check-circle"></i> ${data.checked_count || 0} Checked In</span>
                    ${data.late_count > 0 ? `<span class="stat late"> ${data.late_count} Late</span>` : ''}
                    ${data.already_count > 0 ? `<span class="stat already">📌 ${data.already_count} Already</span>` : ''}
                </div>
            `;

            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    let statusText = '';
                    let statusClass = '';
                    if (item.status === 'checked_in') {
                        statusText = '<i class="fas fa-check-circle"></i> Checked In';
                        statusClass = 'checked_in';
                    } else if (item.status === 'late') {
                        statusText = ' Late';
                        statusClass = 'late';
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

        function closeBulkPopup() {
            document.getElementById('bulkSuccessPopup').classList.remove('active');
            window.location.reload();
        }

        function showSuccessPopup(data) {
            const popupBox = document.getElementById('popupBox');
            const popupIcon = document.getElementById('popupIcon');
            const popupTitle = document.getElementById('popupTitle');
            const popupSub = document.getElementById('popupSub');
            const popupDetail = document.getElementById('popupDetail');
            const popupStatus = document.getElementById('popupStatus');
            const popupTime = document.getElementById('popupTime');
            const popupBtn = document.getElementById('popupBtnSuccess');

            popupBox.classList.remove('auto-checkout');
            popupDetail.classList.remove('auto-checkout');
            popupBtn.classList.remove('auto-checkout-btn');
            popupStatus.className = 'popup-status on-time';
            popupTitle.className = '';

            if (data.action == 'checkin') {
                popupTitle.innerHTML = '<i class="fas fa-check-circle"></i> Check-in Berjaya!';
                popupSub.textContent = 'Anak anda telah berjaya check-in.';
                popupIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            } else {
                if (data.is_auto) {
                    popupTitle.textContent = '🤖 Auto Check-out Berjaya!';
                    popupSub.textContent = 'Anak anda telah berjaya check-out secara automatik.';
                    popupIcon.textContent = '🤖';
                    popupBox.classList.add('auto-checkout');
                    popupDetail.classList.add('auto-checkout');
                    popupBtn.classList.add('auto-checkout-btn');
                    popupTitle.className = 'auto-checkout';
                } else {
                    popupTitle.innerHTML = '<i class="fas fa-hand-wave"></i> Check-out Berjaya!';
                    popupSub.textContent = 'Anak anda telah berjaya check-out.';
                    popupIcon.innerHTML = '<i class="fas fa-hand-wave"></i>';
                }
            }

            document.getElementById('popupChildName').textContent = '🧸 ' + data.child_name;
            document.getElementById('popupChildClass').innerHTML = '<i class="fas fa-school"></i> ' + data.child_class;

            renderCheckedInList();

            if (data.is_late) {
                popupStatus.textContent = ' Late';
                popupStatus.className = 'popup-status late';
            } else {
                if (data.is_auto) {
                    popupStatus.textContent = '🤖 Auto Checkout';
                    popupStatus.className = 'popup-status auto-checkout-status';
                } else {
                    popupStatus.innerHTML = '<i class="fas fa-check-circle"></i> On Time';
                    popupStatus.className = 'popup-status on-time';
                }
            }

            document.getElementById('popupTime').textContent = '🕐 Masa: ' + data.time;
            document.getElementById('successPopup').classList.add('active');

            setTimeout(() => { closeSuccessPopup(); }, 5000);
        }

        function renderCheckedInList() {
            const container = document.getElementById('popupCheckedItems');
            if (!container) return;

            if (checkedInChildren.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; color:#94a3b8; font-size:13px; padding:8px 0;">
                        Tiada anak yang check-in hari ini
                    </div>
                `;
                return;
            }

            let html = '';
            checkedInChildren.forEach(child => {
                html += `
                    <div class="popup-checked-item">
                        <span class="name"><i class="fas fa-child"></i> ${child.name}</span>
                        <span class="time"><i class="fas fa-check-circle"></i> ${child.time}</span>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function closeSuccessPopup() {
            document.getElementById('successPopup').classList.remove('active');
            window.location.reload();
        }

        function closeSuccessPopupManual() {
            document.getElementById('successPopup').classList.remove('active');
            window.location.reload();
        }

        function goToKiosk() {
            document.getElementById('successPopup').classList.remove('active');
            window.location.href = '/kiosk';
        }

        document.getElementById('successPopup').addEventListener('click', function(e) {
            if (e.target === this) { closeSuccessPopup(); }
        });
        document.getElementById('bulkSuccessPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                document.getElementById('bulkSuccessPopup').classList.remove('active');
                window.location.reload();
            }
        });

        // ============================================
        // AUTO START
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            loadTimerInfo();
            setInterval(function() {
                loadTimerInfo();
                checkCheckoutStatus();
            }, 30000);
            console.log('<i class="fas fa-check-circle"></i> Page loaded - waiting for timer info...');
        });
    </script>

</body>
</html>

