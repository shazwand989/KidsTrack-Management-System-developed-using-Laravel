<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidsTrack - Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: #f1f5f9;
        }
        
        .status-card {
            background: white;
            border-radius: 30px;
            padding: 0;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            transition: all 0.5s ease;
        }
        
        /* ==========================================
                   HEADER - Dynamic
                   ========================================== */
        .card-header {
            padding: 30px 30px 25px;
            text-align: center;
            color: white;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }
        
        .card-header.birthday-mode {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            color: #4a1942;
        }
        .card-header.weekend-mode {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-header.weekday-morning {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .card-header.weekday-evening {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .card-header.main-parent {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .card-header.guardian-mode {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
        }
        .card-header.unauthorized-mode {
            background: linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%);
        }
        
        .card-header .icon-big { font-size: 48px; margin-bottom: 8px; }
        .card-header .badge-role {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(4px);
            margin-bottom: 10px;
        }
        .card-header h2 { font-size: 22px; font-weight: 800; margin-bottom: 4px; }
        .card-header .sub-text { font-size: 14px; opacity: 0.9; font-weight: 500; }
        
        .birthday-text {
            font-size: 16px;
            font-weight: 700;
            margin-top: 8px;
            padding: 10px 16px;
            background: rgba(255,255,255,0.4);
            border-radius: 12px;
            backdrop-filter: blur(4px);
            animation: pulseGlow 1.5s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); box-shadow: 0 0 30px rgba(255,255,255,0.3); }
        }
        
        .confetti-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            opacity: 0.7;
            animation: confettiFall 3s linear infinite;
        }
        @keyframes confettiFall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(200px) rotate(720deg); opacity: 0; }
        }
        
        /* ==========================================
                   BODY
                   ========================================== */
        .card-body { padding: 25px 30px 30px; }
        
        .child-name { font-size: 20px; font-weight: 700; color: #6d28d9; }
        
        .status-box {
            background: #f3f4f6;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .status-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .status-item:last-child { border-bottom: none; }
        .status-item .label { color: #6b7280; font-size: 14px; }
        .status-item .value { font-weight: 600; color: #1f2937; font-size: 14px; }
        
        .badge-status {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 0;
        }
        .badge-status.on-time { background: #d1fae5; color: #065f46; }
        .badge-status.late { background: #fef3c7; color: #92400e; }
        .badge-status.checkout { background: #dbeafe; color: #1e40af; }
        .badge-status.closed { background: #fef2f2; color: #991b1b; }
        .badge-status.checked { background: #d1fae5; color: #065f46; }
        
        /* ==========================================
                   FEE BANNER
                   ========================================== */
        .fee-banner {
            margin-top: 15px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            display: none;
            border: 2px solid transparent;
        }
        .fee-banner.unpaid {
            display: block;
            background: #fdf2f8;
            border-color: #f472b6;
            color: #9d174d;
        }
        .fee-banner.unpaid i { margin-right: 8px; color: #db2777; }
        
        /* ==========================================
                   BUTTONS
                   ========================================== */
        .btn-next {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
        }
        .btn-next:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4); }
        .btn-next:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        
        .btn-next.birthday-btn {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }
        .btn-next.birthday-btn:hover {
            box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
        }
        
        .btn-add-another {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-add-another:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4);
        }
        .btn-add-another:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-checkin-all {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-checkin-all:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
        }
        .btn-checkin-all:disabled { opacity: 0.5; cursor: not-allowed; }
        
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
        
        /* ==========================================
                   OTHER CHILDREN SECTION
                   ========================================== */
        .other-children-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            display: none;
        }
        .other-children-section.show { display: block; }
        .other-children-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 12px;
            text-align: left;
        }
        .other-child-item {
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
        .other-child-item:hover {
            background: #f1f5f9;
            border-color: #6d28d9;
            transform: translateX(5px);
        }
        .other-child-item .info { text-align: left; }
        .other-child-item .info .name { font-weight: 600; color: #1f2937; }
        .other-child-item .info .class { font-size: 12px; color: #6b7280; }
        .other-child-item .arrow { font-size: 20px; color: #6b7280; }
        .other-child-item .badge {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-available { background: #dbeafe; color: #1e40af; }
        .badge-checked { background: #d1fae5; color: #065f46; }
        .badge-checkout-done { background: #fef3c7; color: #92400e; }
        .no-children { color: #94a3b8; font-size: 14px; padding: 10px; text-align: center; }
        
        /* ==========================================
                   UNAUTHORIZED
                   ========================================== */
        .unauthorized-content { text-align: center; padding: 10px 0; }
        .unauthorized-content .icon { font-size: 64px; color: #ef4444; margin-bottom: 12px; }
        .unauthorized-content .log-id {
            margin-top: 15px;
            padding: 10px;
            background: #fef2f2;
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        /* ==========================================
                   POPUP
                   ========================================== */
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
        .result-item.late { border-left: 4px solid #f59e0b; }
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
        .stat.late { background: #fef3c7; color: #d97706; }
        .stat.already { background: #dbeafe; color: #2563eb; }
        
        /* ==========================================
                   RESPONSIVE
                   ========================================== */
        @media (max-width: 480px) {
            .card-header { padding: 25px 20px 20px; }
            .card-header h2 { font-size: 18px; }
            .card-body { padding: 20px; }
            .birthday-text { font-size: 14px; padding: 8px 12px; }
        }
    </style>
</head>
<body>

    @php
        // Determine header class
        $headerClass = 'weekday-morning';
        $iconBig = '<i class="fas fa-map-marker-alt"></i>';
        $badgeText = 'Status';
        $greetingText = "Status Kehadiran";
        $subText = $child->name ?? 'Anak';
        
        if ($userRole == 'unknown') {
            $headerClass = 'unauthorized-mode';
            $iconBig = '<i class="fas fa-exclamation-triangle"></i>';
            $badgeText = 'Akses Dihalang';
            $greetingText = 'Akses Tidak Dibenarkan!';
            $subText = 'Sila log masuk untuk melihat status.';
        } elseif ($isBirthday) {
            $headerClass = 'birthday-mode';
            $iconBig = '<i class="fas fa-party-horn"></i>';
            $badgeText = '<i class="fas fa-birthday-cake"></i> Hari Lahir!';
            $greetingText = "Selamat Hari Lahir, {$child->name}!";
            $subText = $birthdayMessage;
        } elseif ($isWeekend) {
            $headerClass = 'weekend-mode';
            $iconBig = '🎨';
            $badgeText = 'Hujung Minggu';
            $greetingText = 'Aktiviti Hujung Minggu / Kelas Tambahan';
            $subText = 'Semoga hari anda ceria! ✨';
        } elseif ($isMainParent) {
            $headerClass = 'main-parent';
            $iconBig = $timeMode == 'morning' ? '☀️' : ($timeMode == 'afternoon' ? '' : '🌙');
            $badgeText = $timeStatus == 'already_checkout' ? '<i class="fas fa-check-circle"></i> Selesai' : ($timeStatus == 'already_checkin' ? '<i class="fas fa-check-circle"></i> Checked In' : '📌 Status');
            $greetingText = "Status Kehadiran";
            $subText = $child->name;
        } elseif ($isSecondParent || $isGuardian) {
            $headerClass = 'guardian-mode';
            $iconBig = '<i class="fas fa-user"></i>';
            $badgeText = 'Status (Wakil)';
            $greetingText = "Status Kehadiran";
            $subText = $child->name . ' - Penjaga Berdaftar';
        }
    @endphp

    <div class="status-card">
        
        <!-- ==========================================
        HEADER - Dynamic
        ========================================== -->
        <div class="card-header {{ $headerClass }}">
            
            @if($isBirthday)
                <div class="confetti-container" id="confettiContainer"></div>
            @endif
            
            <div class="icon-big">{{ $iconBig }}</div>
            <div class="badge-role">{{ $badgeText }}</div>
            <h2>{{ $greetingText }}</h2>
            <p class="sub-text">{{ $subText }}</p>
            
            @if($isBirthday)
                <div class="birthday-text"><i class="fas fa-birthday-cake"></i> {{ $birthdayMessage }}</div>
            @endif
        </div>
        
        <!-- ==========================================
        BODY
        ========================================== -->
        <div class="card-body">
            
            @if($userRole == 'unknown')
                <!-- ==========================================
                UNAUTHORIZED ACCESS
                ========================================== -->
                <div class="unauthorized-content">
                    <div class="icon"><i class="fas fa-user-slash"></i></div>
                    <p style="color: #6b7280; font-size: 15px; margin-bottom: 8px;">
                        Sistem mengesan cubaan tidak sah. Log data keselamatan telah dikemas kini.
                    </p>
                    <div class="log-id">
                        <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                        LOG_ID: {{ rand(10000, 99999) }}
                    </div>
                </div>
                
            @else
                <!-- ==========================================
                STATUS BOX
                ========================================== -->
                <div class="child-name">🧸 {{ $child->name }}</div>
                
                <div class="status-box">
                    <div class="status-item">
                        <span class="label"><i class="fas fa-map-marker-alt"></i> GPS</span>
                        <span class="value" style="color:#16a34a;"><i class="fas fa-check-circle"></i> Kawasan taska</span>
                    </div>
                    <div class="status-item">
                        <span class="label">🕐 Masa</span>
                        <span class="value">{{ $currentTime }}</span>
                    </div>
                    <div class="status-item">
                        <span class="label"><i class="fas fa-chart-bar"></i> Status</span>
                        <span class="value">
                            @if($timeStatus == 'already_checkout')
                                <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Anak sudah check-out</span>
                            @elseif($timeStatus == 'already_checkin')
                                <span style="color:#1e40af;"><i class="fas fa-check-circle"></i> Anak sudah check-in</span>
                            @elseif($timeStatus == 'checkin_on_time')
                                <span style="color:#16a34a;"><i class="fas fa-check-circle"></i> Check-in (On-Time)</span>
                            @elseif($timeStatus == 'checkin_late')
                                <span style="color:#d97706;"> Check-in (Late)</span>
                            @else
                                <span style="color:#991b1b;">🚫 Taska ditutup</span>
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- ==========================================
                FEE BANNER
                ========================================== -->
                @if($hasUnpaidFee)
                    <div class="fee-banner unpaid">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $feeMessage }}
                    </div>
                @endif
                
                <!-- ==========================================
                STATUS BADGE
                ========================================== -->
                @if($timeStatus == 'already_checkout')
                    <div class="badge-status checkout"><i class="fas fa-check-circle"></i> Check-out Selesai</div>
                @elseif($timeStatus == 'already_checkin')
                    <div class="badge-status checked"><i class="fas fa-check-circle"></i> Sudah Check-in</div>
                @elseif($timeStatus == 'checkin_on_time')
                    <div class="badge-status on-time"><i class="fas fa-check-circle"></i> On Time</div>
                @elseif($timeStatus == 'checkin_late')
                    <div class="badge-status late"> Late</div>
                @else
                    <div class="badge-status closed">🚫 Ditutup</div>
                @endif
                
                <!-- ==========================================
                BUTTON NEXT
                ========================================== -->
                @if($timeStatus == 'checkin_on_time' || $timeStatus == 'checkin_late')
                    <button class="btn-next {{ $isBirthday ? 'birthday-btn' : '' }}" 
                            onclick="window.location.href='{{ route('kiosk.checkin.page', $child->id) }}'">
                        📌 Seterusnya →
                    </button>
                @elseif($timeStatus == 'already_checkin' && !$hasCheckout)
                    <button class="btn-next {{ $isBirthday ? 'birthday-btn' : '' }}" 
                            onclick="window.location.href='{{ route('kiosk.checkin.page', $child->id) }}'">
                        📌 Seterusnya →
                    </button>
                @else
                    <button class="btn-next" disabled><i class="fas fa-check-circle"></i> Selesai</button>
                @endif
                
                <!-- ==========================================
                BUTTON ADD ANOTHER CHILD
                ========================================== -->
                @if(isset($otherChildren) && $otherChildren->count() > 0)
                    <button class="btn-add-another" onclick="toggleOtherChildren()">
                        <i class="fas fa-child"></i> Add Another Child ({{ $otherChildren->count() }})
                    </button>
                @endif
                
                <!-- ==========================================
                BUTTON CHECK-IN ALL
                ========================================== -->
                @if(isset($allChildren) && $allChildren->count() > 1)
                    <button class="btn-checkin-all" onclick="checkinAll()">
                        ⚡ Check-in Semua ({{ $allChildren->count() }})
                    </button>
                @endif
                
                <!-- ==========================================
                OTHER CHILDREN SECTION
                ========================================== -->
                <div class="other-children-section" id="otherChildrenSection">
                    @if(isset($otherChildren) && $otherChildren->count() > 0)
                        <div class="other-children-title"><i class="fas fa-child"></i> Pilih Anak Lain:</div>
                        @foreach($otherChildren as $otherChild)
                            <div class="other-child-item" 
                                 onclick="window.location.href='{{ route('kiosk.confirm.child', $otherChild->id) }}'">
                                <div class="info">
                                    <div class="name">{{ $otherChild->name }}</div>
                                    <div class="class"><i class="fas fa-school"></i> {{ $otherChild->classroom->name ?? 'Tiada kelas' }}</div>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    @if($otherChild->checked_in_today ?? false)
                                        @if($otherChild->checked_out_today ?? false)
                                            <span class="badge badge-checkout-done"><i class="fas fa-upload"></i> Checkout</span>
                                        @else
                                            <span class="badge badge-checked"><i class="fas fa-check-circle"></i> Checked</span>
                                        @endif
                                    @else
                                        <span class="badge badge-available">⏳ Available</span>
                                    @endif
                                    <span class="arrow">→</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-children">Tiada anak lain yang berdaftar.</div>
                    @endif
                </div>
            @endif
            
            <!-- ==========================================
            BUTTON BACK
            ========================================== -->
            <button class="btn-back" onclick="window.location.href='/kiosk'"><i class="fas fa-arrow-left"></i> Kembali ke Kiosk</button>
            
        </div>
    </div>

    <!-- Popup Bulk Check-in Success -->
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
        function toggleOtherChildren() {
            const section = document.getElementById('otherChildrenSection');
            if (section) {
                section.classList.toggle('show');
            }
        }

        function checkinAll() {
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
                    child_ids: childIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showBulkPopup(data);
                    btn.textContent = '<i class="fas fa-check-circle"></i> Selesai!';
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
            window.location.href = '/kiosk';
        }

        // Close popup on overlay click
        document.getElementById('bulkSuccessPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBulkPopup();
            }
        });

        // ==========================================
        // BIRTHDAY CONFETTI
        // ==========================================
        @if($isBirthday)
        document.addEventListener('DOMContentLoaded', function() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#f472b6', '#ec4899', '#fbc2eb', '#a6c1ee', '#fbbf24', '#34d399']
            });
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    spread: 100,
                    origin: { y: 0.5, x: 0.3 }
                });
            }, 500);
            
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    spread: 100,
                    origin: { y: 0.5, x: 0.7 }
                });
            }, 1000);
            
            const container = document.getElementById('confettiContainer');
            if (container) {
                const colors = ['#f472b6', '#ec4899', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa'];
                for (let i = 0; i < 30; i++) {
                    const piece = document.createElement('div');
                    piece.className = 'confetti-piece';
                    piece.style.left = Math.random() * 100 + '%';
                    piece.style.top = '-' + (Math.random() * 20) + '%';
                    piece.style.width = (Math.random() * 8 + 4) + 'px';
                    piece.style.height = (Math.random() * 8 + 4) + 'px';
                    piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                    piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                    piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    piece.style.animationDelay = (Math.random() * 2) + 's';
                    container.appendChild(piece);
                }
            }
        });
        @endif
    </script>

</body>
</html>