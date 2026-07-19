@extends('layouts.template')

@section('title', 'Attendance Calendar')
@section('page-title', 'Attendance Calendar')

@section('content')

<style>
    .calendar-container { max-width: 1200px; margin: 0 auto; }
    .calendar-card { background: white; border-radius: 24px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
    .calendar-header h2 { font-size: 20px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .calendar-header h2 .badge { font-size: 11px; background: #e2e8f0; color: #64748b; padding: 2px 12px; border-radius: 20px; font-weight: 500; }
    .calendar-nav { display: flex; gap: 8px; align-items: center; }
    .calendar-nav button { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; cursor: pointer; font-weight: 600; font-size: 14px; color: #334155; transition: all 0.2s; }
    .calendar-nav button:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .calendar-nav .month-label { font-size: 16px; font-weight: 700; color: #0f172a; min-width: 140px; text-align: center; }
    .calendar-nav .btn-today { background: #6d28d9; color: white; border: none; padding: 8px 16px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .calendar-nav .btn-today:hover { background: #5b21b6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109, 40, 217, 0.3); }

    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; padding: 12px 16px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; align-items: center; }
    .filter-bar .filter-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-bar select { padding: 6px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; background: white; outline: none; transition: all 0.2s; min-width: 160px; }
    .filter-bar select:focus { border-color: #6d28d9; box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1); }
    .filter-bar .btn-filter { padding: 6px 18px; background: #6d28d9; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 13px; }
    .filter-bar .btn-filter:hover { background: #5b21b6; transform: translateY(-1px); }
    .filter-bar .btn-reset { padding: 6px 18px; background: #e2e8f0; color: #475569; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 13px; }
    .filter-bar .btn-reset:hover { background: #cbd5e1; }

    .timer-info-box { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: 16px; padding: 18px 22px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(34, 197, 94, 0.12); transition: all 0.3s ease; min-height: 80px; }
    .timer-info-box:hover { box-shadow: 0 4px 16px rgba(34, 197, 94, 0.2); transform: translateY(-2px); }
    .timer-info-box .timer-title { font-weight: 700; font-size: 15px; color: #065f46; display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px dashed #86efac; }
    .timer-info-box .timer-title .title-icon { font-size: 20px; }
    .timer-info-box .timer-title .title-badge { font-size: 10px; background: #065f46; color: white; padding: 2px 14px; border-radius: 20px; font-weight: 600; margin-left: auto; }
    .timer-info-box .timer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; }
    .timer-info-box .timer-item { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; background: white; border-radius: 10px; border: 1px solid #e5e7eb; transition: all 0.3s ease; flex-wrap: wrap; gap: 4px; }
    .timer-info-box .timer-item:hover { border-color: #86efac; transform: scale(1.02); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .timer-info-box .timer-item .slot-left { display: flex; align-items: center; gap: 8px; }
    .timer-info-box .timer-item .slot-icon { font-size: 16px; }
    .timer-info-box .timer-item .slot-label { font-weight: 600; font-size: 12px; color: #1f2937; }
    .timer-info-box .timer-item .slot-rule { font-size: 9px; color: #94a3b8; font-weight: 500; background: #f1f5f9; padding: 1px 8px; border-radius: 10px; }
    .timer-info-box .timer-item .slot-time { font-weight: 700; font-size: 12px; color: #065f46; background: #ecfdf5; padding: 2px 10px; border-radius: 6px; }
    .timer-info-box .timer-item .slot-status { font-size: 9px; padding: 3px 12px; border-radius: 20px; font-weight: 700; letter-spacing: 0.3px; text-transform: uppercase; }
    .timer-info-box .timer-item .slot-status.active { background: #22c55e; color: white; animation: pulse-green 1.5s ease-in-out infinite; }
    .timer-info-box .timer-item .slot-status.soon { background: #f59e0b; color: white; }
    .timer-info-box .timer-item .slot-status.closed { background: #e2e8f0; color: #64748b; }
    .timer-info-box .sim-status { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #86efac; font-size: 11px; color: #d97706; text-align: center; }
    @keyframes pulse-green { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(0.95); } }
    .timer-loading { text-align: center; padding: 20px; color: #64748b; font-size: 13px; grid-column: 1/-1; }
    .timer-loading .spinner-small { width: 24px; height: 24px; border: 3px solid #e2e8f0; border-top: 3px solid #6d28d9; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 8px; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .timer-error { text-align: center; padding: 15px 20px; background: #fef2f2; border-radius: 10px; border: 1px solid #fca5a5; color: #991b1b; grid-column: 1/-1; }
    .timer-no-data { text-align: center; padding: 15px; color: #64748b; font-size: 13px; grid-column: 1/-1; }

    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
    .calendar-grid .day-header { padding: 10px; text-align: center; font-weight: 700; font-size: 11px; color: #64748b; text-transform: uppercase; background: #f8fafc; border-radius: 10px; letter-spacing: 0.5px; }
    .calendar-grid .day-cell { min-height: 110px; background: #fafbfc; border-radius: 10px; padding: 6px 8px; cursor: pointer; transition: all 0.3s; border: 2px solid transparent; position: relative; }
    .calendar-grid .day-cell:hover { background: #f1f5f9; border-color: #cbd5e1; transform: scale(1.02); z-index: 5; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .calendar-grid .day-cell.selected { border-color: #6d28d9; background: #f5f3ff; box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.15); }
    .calendar-grid .day-cell.empty { background: transparent; cursor: default; min-height: auto; transform: none !important; box-shadow: none !important; }
    .calendar-grid .day-cell .date { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 4px; display: flex; align-items: center; gap: 4px; }
    .calendar-grid .day-cell .date.today { color: #6d28d9; background: #ede9fe; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; }
    .calendar-grid .day-cell .date .holiday-badge { font-size: 7px; background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-weight: 700; }
    .calendar-grid .day-cell .date .weekend-badge { font-size: 7px; background: #f59e0b; color: white; padding: 1px 6px; border-radius: 10px; font-weight: 700; }
    .calendar-grid .day-cell .status-summary { display: flex; gap: 3px; flex-wrap: wrap; margin-top: 4px; }
    .calendar-grid .day-cell .status-summary .badge { font-size: 9px; padding: 1px 6px; border-radius: 8px; font-weight: 600; }
    .badge.present { background: #dcfce7; color: #16a34a; }
    .badge.late { background: #fee2e2; color: #dc2626; }
    .badge.checkout { background: #dbeafe; color: #2563eb; }
    .badge.absent { background: #fef3c7; color: #d97706; }
    .calendar-grid .day-cell.holiday { background: #fef2f2; border-color: #fca5a5; }
    .calendar-grid .day-cell.weekend { background: #fffbeb; border-color: #fcd34d; }
    .calendar-grid .day-cell .cell-badge { font-size: 9px; padding: 1px 8px; border-radius: 10px; font-weight: 700; display: inline-block; margin-top: 2px; }
    .calendar-grid .day-cell .cell-badge.has-data { background: #6d28d9; color: white; }
    .calendar-grid .day-cell .child-item { font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px; display: flex; align-items: center; gap: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .calendar-grid .day-cell .child-item.present { background: #dcfce7; color: #16a34a; border-left: 3px solid #22c55e; }
    .calendar-grid .day-cell .child-item.late { background: #fee2e2; color: #dc2626; border-left: 3px solid #ef4444; }
    .calendar-grid .day-cell .child-item.checkout { background: #dbeafe; color: #2563eb; border-left: 3px solid #3b82f6; }
    .calendar-grid .day-cell .child-item.absent { background: #fef3c7; color: #d97706; border-left: 3px solid #f59e0b; }
    .calendar-grid .day-cell .child-count { font-size: 9px; color: #94a3b8; margin-top: 4px; font-weight: 600; }
    .calendar-grid .day-cell .holiday-label { font-size: 9px; color: #dc2626; font-weight: 700; background: #fee2e2; padding: 2px 8px; border-radius: 4px; margin-top: 4px; display: inline-block; }
    .calendar-grid .day-cell .weekend-label { font-size: 9px; color: #d97706; font-weight: 700; background: #fef3c7; padding: 2px 8px; border-radius: 4px; margin-top: 4px; display: inline-block; }

    .legend { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 500; color: #475569; cursor: pointer; transition: all 0.2s; padding: 4px 10px; border-radius: 8px; }
    .legend-item:hover { background: #f1f5f9; }
    .legend-color { width: 12px; height: 12px; border-radius: 4px; }
    .legend-color.present { background: #dcfce7; border: 1px solid #86efac; }
    .legend-color.late { background: #fee2e2; border: 1px solid #fca5a5; }
    .legend-color.checkout { background: #dbeafe; border: 1px solid #93c5fd; }
    .legend-color.absent { background: #fef3c7; border: 1px solid #fcd34d; }
    .legend-color.holiday { background: #fef2f2; border: 1px solid #fca5a5; }
    .legend-color.weekend { background: #fffbeb; border: 1px solid #fcd34d; }

    .calendar-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-top: 15px; margin-bottom: 15px; }
    .stat-mini { background: #f8fafc; padding: 10px 12px; border-radius: 10px; text-align: center; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.3s; }
    .stat-mini:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-mini .number { font-size: 18px; font-weight: 800; color: #0f172a; }
    .stat-mini .label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

    .timer-section { margin-top: 24px; padding-top: 24px; border-top: 2px solid #e2e8f0; }
    .timer-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
    .timer-header h2 { font-size: 18px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    .timer-header h2 .sub { font-size: 12px; color: #94a3b8; font-weight: 400; }
    .timer-actions { display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-save-all { padding: 10px 24px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 14px; background: #22c55e; color: white; }
    .btn-save-all:hover { background: #16a34a; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3); }
    .btn-save-all:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

    .btn-reset-all { padding: 10px 24px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-size: 14px; background: #ef4444; color: white; }
    .btn-reset-all:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
    .btn-reset-all:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

    .timer-status { margin-top: 15px; padding: 12px 16px; border-radius: 10px; font-size: 13px; display: none; }
    .timer-status.success { display: block; background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }
    .timer-status.error { display: block; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }

    .timer-single-set { background: #f8fafc; border-radius: 16px; padding: 24px 28px; border: 2px solid #e2e8f0; max-width: 600px; margin: 0 auto; }
    .timer-single-set .timer-label { font-weight: 700; font-size: 14px; color: #0f172a; margin-bottom: 16px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .timer-single-set .timer-label .info { font-size: 11px; color: #94a3b8; font-weight: 400; }
    .timer-single-set .slot-group { margin-bottom: 12px; padding: 12px 16px; border-radius: 10px; border-left: 4px solid; }
    .timer-single-set .slot-group.morning { background: #fef3c7; border-left-color: #f59e0b; }
    .timer-single-set .slot-group.evening { background: #e0e7ff; border-left-color: #8b5cf6; }
    .timer-single-set .slot-header { display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 600; margin-bottom: 6px; }
    .timer-single-set .slot-header .slot-label { display: flex; align-items: center; gap: 6px; color: #1e293b; }
    .timer-single-set .slot-header .slot-rule { font-size: 10px; font-weight: 400; padding: 1px 10px; border-radius: 10px; background: rgba(0,0,0,0.06); color: #64748b; }
    .timer-single-set .slot-time-inputs { display: flex; gap: 10px; align-items: center; }
    .timer-single-set .slot-time-inputs input[type="time"] { flex: 1; padding: 6px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: white; outline: none; transition: all 0.2s; font-family: inherit; font-weight: 600; color: #0f172a; }
    .timer-single-set .slot-time-inputs input[type="time"]:focus { border-color: #6d28d9; box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1); }
    .timer-single-set .slot-time-inputs .time-sep { color: #94a3b8; font-weight: 700; font-size: 14px; }

    .loading-spinner { text-align: center; padding: 40px; color: #94a3b8; }
    .loading-spinner .spinner { width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top: 4px solid #6d28d9; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px; }

    .popup-notification { position: fixed; top: 30px; right: 30px; z-index: 99999; min-width: 320px; max-width: 450px; width: 90%; padding: 20px 24px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); transform: translateX(120%); transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); display: flex; align-items: flex-start; gap: 16px; background: white; border: 2px solid; }
    .popup-notification.show { transform: translateX(0); animation: slideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .popup-notification.success { border-color: #22c55e; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
    .popup-notification.success .popup-icon { background: #22c55e; color: white; }
    .popup-notification.error { border-color: #ef4444; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); }
    .popup-notification.error .popup-icon { background: #ef4444; color: white; }
    .popup-notification .popup-icon { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; margin-top: 2px; }
    .popup-notification .popup-content { flex: 1; min-width: 0; }
    .popup-notification .popup-content .popup-title { font-weight: 700; font-size: 15px; color: #0f172a; margin-bottom: 2px; }
    .popup-notification .popup-content .popup-message { font-size: 13px; color: #475569; line-height: 1.5; }
    .popup-notification .popup-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; padding: 0 4px; transition: color 0.2s; flex-shrink: 0; line-height: 1; margin-top: -2px; }
    .popup-notification .popup-close:hover { color: #1e293b; }

    @media (max-width: 480px) {
        .popup-notification { top: 10px; right: 10px; padding: 16px 18px; min-width: auto; max-width: calc(100% - 20px); }
        .popup-notification .popup-icon { width: 36px; height: 36px; font-size: 18px; }
        .popup-notification .popup-content .popup-title { font-size: 14px; }
        .popup-notification .popup-content .popup-message { font-size: 12px; }
        .timer-single-set { padding: 16px 18px; }
    }

    @media (max-width: 768px) {
        .calendar-grid .day-cell { min-height: 60px; padding: 4px; }
        .calendar-grid .day-cell .date { font-size: 11px; }
        .calendar-grid .day-cell .status-summary .badge { font-size: 7px; padding: 1px 4px; }
        .calendar-header { flex-direction: column; align-items: stretch; }
        .calendar-nav { justify-content: center; flex-wrap: wrap; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar select { width: 100%; }
        .timer-info-box .timer-grid { grid-template-columns: 1fr; }
        .timer-single-set .slot-time-inputs { flex-wrap: wrap; }
    }
</style>

<div class="calendar-container">

    <div class="calendar-card">
        <div class="calendar-header">
            <h2>📅 Attendance Calendar <span class="badge"><span id="holidayCount">0</span> holidays</span></h2>
            <div class="calendar-nav">
                <button onclick="changeMonth(-1)">◀</button>
                <span class="month-label" id="monthLabel">{{ Carbon\Carbon::now()->format('F Y') }}</span>
                <button onclick="changeMonth(1)">▶</button>
                <button class="btn-today" onclick="goToday()">📍 Today</button>
            </div>
        </div>

        <div class="filter-bar">
            <span class="filter-label">🔍 Filter:</span>
            <select id="filterClassroom">
                <option value="">All Classrooms</option>
                @foreach($classrooms ?? [] as $classroom)
                    <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                @endforeach
            </select>
            <button class="btn-filter" onclick="applyFilters()">Apply Filter</button>
            <button class="btn-reset" onclick="resetFilters()">Reset</button>
        </div>

        <!-- TIMER INFO BOX -->
        <div class="timer-info-box" id="timerInfoBox">
            <div class="timer-title">
                <span class="title-icon">⏱️</span> Waktu Operasi Hari Ini
                <span class="title-badge" id="todayDateBadge">{{ Carbon\Carbon::now()->format('d M Y') }}</span>
            </div>
            <div class="timer-grid" id="timerInfoContent">
                <div class="timer-loading"><div class="spinner-small"></div>⏳ Memuatkan...</div>
            </div>
        </div>

        <div id="calendarLoading" class="loading-spinner" style="display: none;"><div class="spinner"></div><p>Loading calendar...</p></div>

        <div id="calendarContent">
            <div class="calendar-grid" id="calendarGrid"></div>
        </div>

        <div class="calendar-stats" id="calendarStats">
            <div class="stat-mini" onclick="filterByStatus('all')"><div class="number" id="statTotal">0</div><div class="label">Total</div></div>
            <div class="stat-mini" onclick="filterByStatus('present')"><div class="number" style="color:#16a34a;" id="statPresent">0</div><div class="label">✅ Present</div></div>
            <div class="stat-mini" onclick="filterByStatus('late')"><div class="number" style="color:#dc2626;" id="statLate">0</div><div class="label">⏰ Late</div></div>
            <div class="stat-mini" onclick="filterByStatus('checkout')"><div class="number" style="color:#2563eb;" id="statCheckout">0</div><div class="label">👋 Checkout</div></div>
            <div class="stat-mini" onclick="filterByStatus('absent')"><div class="number" style="color:#d97706;" id="statAbsent">0</div><div class="label">❌ Absent</div></div>
            <div class="stat-mini" onclick="filterByStatus('holiday')"><div class="number" style="color:#dc2626;" id="statHoliday">0</div><div class="label">🎉 Holidays</div></div>
        </div>

        <div class="legend">
            <div class="legend-item"><div class="legend-color present"></div> Present</div>
            <div class="legend-item"><div class="legend-color late"></div> Late</div>
            <div class="legend-item"><div class="legend-color checkout"></div> Check-out</div>
            <div class="legend-item"><div class="legend-color absent"></div> Absent</div>
            <div class="legend-item"><div class="legend-color holiday"></div> Holiday</div>
            <div class="legend-item"><div class="legend-color weekend"></div> Weekend</div>
        </div>

        <!-- TIMER SETTINGS -->
        <div class="timer-section">
            <div class="timer-header">
                <h2>⏱️ Timer Settings <span class="sub">Set sekali untuk SEMUA hari</span></h2>
                <div class="timer-actions">
                    <form action="{{ route('save.timer.settings') }}" method="POST" id="timerForm">
                        @csrf
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        @endphp
                        @foreach($days as $day)
                            <input type="hidden" name="{{ $day }}[morning][start]" id="ms_{{ $day }}" value="07:00">
                            <input type="hidden" name="{{ $day }}[morning][end]" id="me_{{ $day }}" value="07:30">
                            <input type="hidden" name="{{ $day }}[evening][start]" id="es_{{ $day }}" value="17:00">
                            <input type="hidden" name="{{ $day }}[evening][end]" id="ee_{{ $day }}" value="17:30">
                        @endforeach
                        <button type="submit" class="btn-save-all" id="saveBtn">💾 Save All</button>
                    </form>
                    <button class="btn-reset-all" onclick="resetAllTimers()">🔄 Reset All</button>
                </div>
            </div>

            <div id="timerStatus" class="timer-status"></div>

            <div class="timer-single-set">
                <div class="timer-label">⏱️ Set Waktu Operasi (Akan digunakan untuk SEMUA hari) <span class="info">Monday - Sunday</span></div>

                <div class="slot-group morning">
                    <div class="slot-header">
                        <span class="slot-label">🌅 Morning (Check-in)</span>
                        <span class="slot-rule">Rule: Check-in</span>
                    </div>
                    <div class="slot-time-inputs">
                        <input type="time" id="morning_start" value="07:00" onchange="updateHidden()">
                        <span class="time-sep">-</span>
                        <input type="time" id="morning_end" value="07:30" onchange="updateHidden()">
                    </div>
                </div>

                <div class="slot-group evening">
                    <div class="slot-header">
                        <span class="slot-label">🌙 Evening (Check-out)</span>
                        <span class="slot-rule">Rule: Check-out</span>
                    </div>
                    <div class="slot-time-inputs">
                        <input type="time" id="evening_start" value="17:00" onchange="updateHidden()">
                        <span class="time-sep">-</span>
                        <input type="time" id="evening_end" value="17:30" onchange="updateHidden()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup-notification" id="popupNotification">
    <div class="popup-icon" id="popupIcon">✅</div>
    <div class="popup-content">
        <div class="popup-title" id="popupTitle">Success!</div>
        <div class="popup-message" id="popupMessage">Timer settings saved successfully.</div>
    </div>
    <button class="popup-close" onclick="closePopup()">✕</button>
</div>

<script>
    // ============================================
    // VARIABLES
    // ============================================
    let currentMonth = {{ Carbon\Carbon::now()->month }};
    let currentYear = {{ Carbon\Carbon::now()->year }};
    let attendanceData = @json($attendances ?? []);
    let allClassrooms = @json($classrooms ?? []);
    let holidaysData = [];
    let selectedDateStr = null;
    let timerSettings = {};

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const timerDayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // ============================================
    // UPDATE HIDDEN
    // ============================================
    function updateHidden() {
        const morningStart = document.getElementById('morning_start').value;
        const morningEnd = document.getElementById('morning_end').value;
        const eveningStart = document.getElementById('evening_start').value;
        const eveningEnd = document.getElementById('evening_end').value;
        
        timerDayNames.forEach(day => {
            const ms = document.getElementById(`ms_${day}`);
            const me = document.getElementById(`me_${day}`);
            const es = document.getElementById(`es_${day}`);
            const ee = document.getElementById(`ee_${day}`);
            if (ms) ms.value = morningStart;
            if (me) me.value = morningEnd;
            if (es) es.value = eveningStart;
            if (ee) ee.value = eveningEnd;
        });
    }

    // ============================================
    // LOAD TIMER INFO - GUNA timer_settings SAHAJA!
    // ============================================
    function loadTimerInfo() {
        const content = document.getElementById('timerInfoContent');
        content.innerHTML = `<div class="timer-loading"><div class="spinner-small"></div>⏳ Memuatkan...</div>`;

        fetch('/get-timer-settings')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    timerSettings = data.data;
                    renderTimerInfo(data.data);
                    loadDataToForm(data.data);
                } else {
                    content.innerHTML = `<div class="timer-error">❌ Gagal memuat data</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading timer info:', error);
                content.innerHTML = `<div class="timer-error">⚠️ Ralat memuat waktu operasi</div>`;
            });
    }

    function renderTimerInfo(timerSettings) {
        const content = document.getElementById('timerInfoContent');
        const now = new Date();
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const today = days[now.getDay()];
        const currentTime = parseInt(now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0'));
        
        let timer = timerSettings[today];
        if (!timer) {
            content.innerHTML = `<div class="timer-no-data">📅 Tiada waktu operasi</div>`;
            return;
        }

        function checkStatus(start, end) {
            const s = parseInt(start.replace(':', ''));
            const e = parseInt(end.replace(':', ''));
            if (currentTime >= s && currentTime <= e) return 'active';
            else if (currentTime < s) return 'soon';
            else return 'closed';
        }

        function getLabel(status) {
            const map = { 'active': '🟢 Aktif', 'soon': '⏳ Akan Datang', 'closed': '🔒 Tutup' };
            return map[status] || '❓';
        }

        function getClass(status) {
            const map = { 'active': 'active', 'soon': 'soon', 'closed': 'closed' };
            return map[status] || 'closed';
        }

        const morningStatus = checkStatus(timer.morning.start, timer.morning.end);
        const eveningStatus = checkStatus(timer.evening.start, timer.evening.end);

        content.innerHTML = `
            <div class="timer-item">
                <div class="slot-left"><span class="slot-icon">🌅</span><span class="slot-label">Morning</span><span class="slot-rule">Check-in</span></div>
                <span class="slot-time">${timer.morning.start} - ${timer.morning.end}</span>
                <span class="slot-status ${getClass(morningStatus)}">${getLabel(morningStatus)}</span>
            </div>
            <div class="timer-item">
                <div class="slot-left"><span class="slot-icon">🌙</span><span class="slot-label">Evening</span><span class="slot-rule">Check-out</span></div>
                <span class="slot-time">${timer.evening.start} - ${timer.evening.end}</span>
                <span class="slot-status ${getClass(eveningStatus)}">${getLabel(eveningStatus)}</span>
            </div>
        `;
    }

    function loadDataToForm(timerSettings) {
        const dayKey = 'Monday';
        const timer = timerSettings[dayKey] || {
            morning: { start: '07:00', end: '07:30' },
            evening: { start: '17:00', end: '17:30' }
        };
        
        document.getElementById('morning_start').value = timer.morning.start;
        document.getElementById('morning_end').value = timer.morning.end;
        document.getElementById('evening_start').value = timer.evening.start;
        document.getElementById('evening_end').value = timer.evening.end;
        
        updateHidden();
    }

    // ============================================
    // SAVE ALL TIMERS
    // ============================================
    function saveAllTimers() {
        const timerData = collectSingleTimer();
        const payload = {};
        timerDayNames.forEach(day => { payload[day] = timerData; });

        const btn = document.querySelector('.btn-save-all');
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Saving...'; }

        fetch('/save-timer-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopup('success', '✅ Success!', data.message);
                loadTimerInfo();
            } else {
                showPopup('error', '❌ Failed!', data.message);
            }
        })
        .catch(error => {
            showPopup('error', '❌ Error!', error.message);
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.textContent = '💾 Save All'; }
        });
    }

    function collectSingleTimer() {
        return {
            morning: {
                start: document.getElementById('morning_start').value,
                end: document.getElementById('morning_end').value
            },
            evening: {
                start: document.getElementById('evening_start').value,
                end: document.getElementById('evening_end').value
            }
        };
    }

    // ============================================
    // RESET ALL TIMERS
    // ============================================
    function resetAllTimers() {
        if (!confirm('Reset all timers to default?')) return;

        const btn = document.querySelector('.btn-reset-all');
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Resetting...'; }

        fetch('/reset-timer-settings', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopup('success', '✅ Reset Complete!', 'All timers reset to default!');
                loadTimerInfo();
                document.getElementById('morning_start').value = '07:00';
                document.getElementById('morning_end').value = '07:30';
                document.getElementById('evening_start').value = '17:00';
                document.getElementById('evening_end').value = '17:30';
                updateHidden();
            } else {
                showPopup('error', '❌ Failed!', data.message);
            }
        })
        .catch(error => {
            showPopup('error', '❌ Error!', error.message);
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.textContent = '🔄 Reset All'; }
        });
    }

    // ============================================
    // POPUP NOTIFICATION
    // ============================================
    let popupTimer = null;

    function showPopup(type, title, message) {
        const popup = document.getElementById('popupNotification');
        const icon = document.getElementById('popupIcon');
        const titleEl = document.getElementById('popupTitle');
        const msgEl = document.getElementById('popupMessage');

        popup.classList.remove('success', 'error', 'show');
        icon.innerHTML = '';

        if (type === 'success') {
            popup.classList.add('success');
            icon.textContent = '✅';
        } else if (type === 'error') {
            popup.classList.add('error');
            icon.textContent = '❌';
        }

        titleEl.textContent = title;
        msgEl.textContent = message;
        popup.classList.add('show');

        clearTimeout(popupTimer);
        popupTimer = setTimeout(() => { closePopup(); }, 5000);
    }

    function closePopup() {
        document.getElementById('popupNotification').classList.remove('show');
        clearTimeout(popupTimer);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closePopup(); }
    });

    // ============================================
    // HOLIDAYS
    // ============================================
    function fetchHolidays(month, year) {
        return fetch(`/api/holidays/${year}/${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    holidaysData = data.data.map(h => ({ date: h.date, name: h.localName || h.name, type: 'public' }));
                } else {
                    holidaysData = getFallbackHolidays(month, year);
                }
                return holidaysData;
            })
            .catch(() => { holidaysData = getFallbackHolidays(month, year); return holidaysData; });
    }

    function getFallbackHolidays(month, year) {
        const all = {
            1: [{ date: `${year}-01-01`, name: "New Year's Day" }],
            2: [{ date: `${year}-02-01`, name: "Federal Territory Day" }],
            5: [{ date: `${year}-05-01`, name: "Labour Day" }, { date: `${year}-05-22`, name: "Wesak Day" }],
            6: [{ date: `${year}-06-01`, name: "Agong's Birthday" }, { date: `${year}-06-17`, name: "Hari Raya Haji" }],
            7: [{ date: `${year}-07-07`, name: "Awal Muharram" }],
            8: [{ date: `${year}-08-31`, name: "Merdeka Day" }],
            9: [{ date: `${year}-09-16`, name: "Malaysia Day" }, { date: `${year}-09-30`, name: "Maulidur Rasul" }],
            11: [{ date: `${year}-11-04`, name: "Deepavali" }],
            12: [{ date: `${year}-12-25`, name: "Christmas" }]
        };
        if (month === 4) {
            all[4] = [{ date: `${year}-04-10`, name: "Hari Raya Puasa" }, { date: `${year}-04-11`, name: "Hari Raya Puasa (Day 2)" }];
        }
        return all[month] || [];
    }

    // ============================================
    // RENDER CALENDAR
    // ============================================
    function renderCalendar(month, year) {
        const grid = document.getElementById('calendarGrid');
        document.getElementById('monthLabel').textContent = monthNames[month - 1] + ' ' + year;

        const firstDay = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        const today = new Date();
        const todayDate = today.getDate();
        const todayMonth = today.getMonth() + 1;
        const todayYear = today.getFullYear();

        const monthHolidays = holidaysData.filter(h => {
            const hDate = new Date(h.date);
            return hDate.getMonth() + 1 === month && hDate.getFullYear() === year;
        });

        let html = '';
        let totalPresent = 0, totalLate = 0, totalCheckout = 0, totalAbsent = 0, totalRecords = 0;

        dayNames.forEach(day => { html += `<div class="day-header">${day}</div>`; });

        for (let i = 0; i < firstDay; i++) { html += `<div class="day-cell empty"></div>`; }

        for (let day = 1; day <= daysInMonth; day++) {
            const isToday = (day === todayDate && month === todayMonth && year === todayYear);
            const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayOfWeek = new Date(year, month - 1, day).getDay();
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            const holiday = monthHolidays.find(h => h.date === dateStr);
            const isHoliday = !!holiday;

            const dayAttendances = attendanceData.filter(a => {
                const aDate = new Date(a.date);
                return aDate.getDate() === day && (aDate.getMonth() + 1) === month && aDate.getFullYear() === year;
            });

            const presentCount = dayAttendances.filter(a => a.status === 'present' || a.status === 'checkin').length;
            const lateCount = dayAttendances.filter(a => a.status === 'late').length;
            const checkoutCount = dayAttendances.filter(a => a.status === 'checkout').length;
            const absentCount = dayAttendances.filter(a => a.status === 'absent').length;

            totalPresent += presentCount;
            totalLate += lateCount;
            totalCheckout += checkoutCount;
            totalAbsent += absentCount;
            totalRecords += dayAttendances.length;

            let cellClass = '', labelHtml = '';

            if (isHoliday) {
                cellClass = 'holiday';
                labelHtml = `<div class="holiday-label">🎉 ${holiday.name}</div>`;
            } else if (isWeekend) {
                cellClass = 'weekend';
                labelHtml = `<div class="weekend-label">📅 Weekend</div>`;
            }

            let childItemsHtml = '';
            dayAttendances.forEach(a => {
                let statusClass = 'absent', statusText = '❌';
                if (a.status === 'present' || a.status === 'checkin') { statusClass = 'present'; statusText = '✅'; }
                else if (a.status === 'late') { statusClass = 'late'; statusText = '⏰'; }
                else if (a.status === 'checkout') { statusClass = 'checkout'; statusText = '👋'; }
                childItemsHtml += `<div class="child-item ${statusClass}">${statusText} ${a.child ? a.child.name : 'Unknown'}</div>`;
            });

            if (childItemsHtml.split('</div>').length - 1 > 3) {
                const items = childItemsHtml.split('</div>').filter(s => s.trim());
                childItemsHtml = items.slice(0, 3).join('</div>') + '</div>';
                if (items.length > 3) {
                    childItemsHtml += `<div class="child-item" style="font-size:9px; color:#94a3b8;">+${items.length - 3} more</div>`;
                }
            }

            const dateDisplay = isToday ? `<span class="date today">${day}</span>` : `<span class="date">${day}</span>`;
            const hasData = dayAttendances.length > 0;
            const isSelected = (selectedDateStr === dateStr);

            let statusHtml = '';
            if (presentCount > 0) statusHtml += `<span class="badge present">✅${presentCount}</span>`;
            if (lateCount > 0) statusHtml += `<span class="badge late">⏰${lateCount}</span>`;
            if (checkoutCount > 0) statusHtml += `<span class="badge checkout">👋${checkoutCount}</span>`;
            if (absentCount > 0) statusHtml += `<span class="badge absent">❌${absentCount}</span>`;

            html += `
                <div class="day-cell ${cellClass} ${isSelected ? 'selected' : ''}" 
                     onclick="selectDate('${dateStr}')" data-date="${dateStr}">
                    <div class="date">
                        ${dateDisplay}
                        ${isHoliday ? `<span class="holiday-badge">🎉</span>` : ''}
                        ${isWeekend && !isHoliday ? `<span class="weekend-badge">📅</span>` : ''}
                        ${hasData ? `<span class="cell-badge has-data">${dayAttendances.length}</span>` : ''}
                    </div>
                    ${statusHtml ? `<div class="status-summary">${statusHtml}</div>` : ''}
                    ${childItemsHtml}
                    ${dayAttendances.length > 0 ? `<div class="child-count">${dayAttendances.length} records</div>` : ''}
                    ${labelHtml}
                </div>
            `;
        }

        grid.innerHTML = html;

        document.getElementById('statTotal').textContent = totalRecords;
        document.getElementById('statPresent').textContent = totalPresent;
        document.getElementById('statLate').textContent = totalLate;
        document.getElementById('statCheckout').textContent = totalCheckout;
        document.getElementById('statAbsent').textContent = totalAbsent;
        document.getElementById('statHoliday').textContent = monthHolidays.length;
        document.getElementById('holidayCount').textContent = monthHolidays.length;
    }

    function selectDate(dateStr) {
        selectedDateStr = dateStr;
        renderCalendar(currentMonth, currentYear);
    }

    // ============================================
    // FILTER
    // ============================================
    function applyFilters() {
        const classroom = document.getElementById('filterClassroom').value;
        const cells = document.querySelectorAll('.day-cell:not(.empty)');
        let visibleCount = 0;
        cells.forEach(cell => {
            const date = cell.dataset.date;
            if (!date) return;
            const dayAttendances = attendanceData.filter(a => {
                const aDate = new Date(a.date);
                return aDate.toISOString().split('T')[0] === date;
            });
            let show = true;
            if (classroom) {
                const hasClassroom = dayAttendances.some(a => a.child && a.child.classroom_id == classroom);
                if (!hasClassroom) show = false;
            }
            cell.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        document.getElementById('monthLabel').textContent = document.getElementById('monthLabel').textContent.split('(')[0] + ` (${visibleCount} days)`;
    }

    function resetFilters() {
        document.getElementById('filterClassroom').value = '';
        document.querySelectorAll('.day-cell:not(.empty)').forEach(cell => cell.style.display = '');
        document.getElementById('monthLabel').textContent = document.getElementById('monthLabel').textContent.split('(')[0].trim();
    }

    function filterByStatus(status) {
        const cells = document.querySelectorAll('.day-cell:not(.empty)');
        cells.forEach(cell => { cell.style.display = ''; });
    }

    // ============================================
    // NAVIGATION
    // ============================================
    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth > 12) { currentMonth = 1; currentYear++; }
        else if (currentMonth < 1) { currentMonth = 12; currentYear--; }
        loadCalendarData(currentMonth, currentYear);
    }

    function goToday() {
        const today = new Date();
        currentMonth = today.getMonth() + 1;
        currentYear = today.getFullYear();
        loadCalendarData(currentMonth, currentYear);
        setTimeout(() => selectDate(today.toISOString().split('T')[0]), 100);
    }

    // ============================================
    // LOAD DATA
    // ============================================
    function loadCalendarData(month, year) {
        const loading = document.getElementById('calendarLoading');
        const content = document.getElementById('calendarContent');
        loading.style.display = 'block';
        content.style.display = 'none';

        fetchHolidays(month, year)
            .then(() => fetch(`/attendance-calendar-data?month=${month}&year=${year}`, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    attendanceData = data.data;
                    renderCalendar(month, year);
                }
            })
            .catch(error => { renderCalendar(month, year); })
            .finally(() => {
                loading.style.display = 'none';
                content.style.display = 'block';
            });
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        loadTimerInfo();
        setInterval(loadTimerInfo, 60000);
        loadCalendarData(currentMonth, currentYear);
        setTimeout(() => {
            const today = new Date();
            selectDate(today.toISOString().split('T')[0]);
        }, 200);
        
        setTimeout(() => {
            updateHidden();
        }, 500);
    });
</script>

@endsection