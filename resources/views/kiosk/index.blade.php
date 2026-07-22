<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>KidsTrack Kiosk</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- QR Code Decoder Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .kiosk-container {
            width: 100%;
            max-width: 500px;
        }
        .kiosk-card {
            background: white;
            border-radius: 30px;
            padding: 30px 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .kiosk-card .logo { font-size: 48px; margin-bottom: 10px; }
        .kiosk-card h1 { font-size: 22px; color: #1f2937; margin-bottom: 5px; }
        .kiosk-card .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 20px; }

        /* ROLE BADGE */
        .role-badge {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .role-badge.main-parent { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
        .role-badge.second-parent { background: #ede9fe; color: #5b21b6; border: 2px solid #8b5cf6; }
        .role-badge.guardian { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
        .role-badge.admin { background: #e2e8f0; color: #1e293b; border: 2px solid #475569; }
        .role-badge.teacher { background: #dbeafe; color: #1e40af; border: 2px solid #3b82f6; }
        .role-badge.parent { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }

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
        .role-info .role-name.teacher { color: #2563eb; }

        /* TIMER INFO */
        .timer-info-box {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 15px;
            text-align: left;
        }
        .timer-info-box .timer-title {
            font-weight: 700;
            font-size: 13px;
            color: #065f46;
            margin-bottom: 6px;
        }
        .timer-info-box .timer-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 12px;
            border-bottom: 1px solid #d1fae5;
        }
        .timer-info-box .timer-row:last-child { border-bottom: none; }
        .timer-info-box .timer-row .slot-label { font-weight: 600; color: #1f2937; }
        .timer-info-box .timer-row .slot-time { font-weight: 700; color: #065f46; }
        .timer-info-box .timer-row .slot-status {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .timer-info-box .timer-row .slot-status.active { background: #dbeafe; color: #1e40af; }
        .timer-info-box .timer-row .slot-status.info { background: #f3f4f6; color: #6b7280; }
        .timer-info-box .timer-row .slot-status.upcoming { background: #e0f2fe; color: #0369a1; }
        .timer-info-box .timer-row .slot-status.ended { background: #f1f5f9; color: #94a3b8; }

        /* Classroom rows in timer */
        .timer-info-box .timer-row.class-row {
            padding: 6px 0;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .timer-info-box .timer-row.class-row:last-child { border-bottom: none; }
        .timer-info-box .timer-row.class-row .slot-label {
            flex: 1;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-right: 8px;
        }
        .timer-info-box .timer-row.class-row .slot-time {
            font-size: 12px;
            margin-right: 8px;
            white-space: nowrap;
        }

        /* QR SCANNER */
        #qr-reader {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            min-height: 300px;
        }
        #qr-reader video {
            width: 100% !important;
            height: auto !important;
        }

        .scanner-controls {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .scanner-controls button {
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-start { background: #16a34a; color: white; }
        .btn-start:hover { background: #15803d; }
        .btn-stop { background: #dc2626; color: white; }
        .btn-stop:hover { background: #b91c1c; }

        #scannerStatus {
            margin-top: 8px;
            font-size: 14px;
            color: #6b7280;
            padding: 8px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        /* UPLOAD SECTION */
        .upload-section {
            margin-top: 15px;
            padding: 15px;
            background: #f0f4ff;
            border-radius: 12px;
            border: 2px dashed #6d28d9;
        }
        .upload-section .upload-label {
            display: block;
            padding: 12px;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .upload-section .upload-label:hover { background: #f5f3ff; border-color: #6d28d9; }
        .upload-section input[type="file"] {
            display: none;
        }
        .upload-preview {
            margin-top: 10px;
            text-align: center;
            display: none;
        }
        .upload-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 5px;
            background: white;
        }
        .upload-preview .btn-primary {
            padding: 8px 20px;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .upload-preview .btn-clear {
            padding: 8px 20px;
            font-size: 14px;
            display: inline-block;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-left: 8px;
        }
        #uploadStatus {
            margin-top: 8px;
            font-size: 13px;
            color: #6b7280;
        }

        /* POPUP */
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
            padding: 35px 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.4s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .popup-icon { font-size: 64px; margin-bottom: 10px; }
        .popup-box h2 { font-size: 20px; font-weight: 700; color: #dc2626; margin-bottom: 8px; }
        .popup-box .popup-sub { color: #6b7280; font-size: 14px; line-height: 1.6; }
        .popup-box .popup-detail {
            background: #fef2f2;
            border-radius: 10px;
            padding: 10px 14px;
            margin: 12px 0;
            font-size: 13px;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .popup-btn-group { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 10px; }
        .popup-btn {
            padding: 10px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .popup-btn-primary { background: #dc2626; color: white; }
        .popup-btn-primary:hover { background: #b91c1c; }
        .popup-btn-secondary { background: #f3f4f6; color: #374151; }
        .popup-btn-secondary:hover { background: #e5e7eb; }

        /* SCANNING OVERLAY */
        .scanning-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .scanning-overlay.active { display: flex; }
        .scanning-box {
            background: white;
            padding: 35px;
            border-radius: 30px;
            text-align: center;
            max-width: 350px;
        }
        .scanning-box .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #6d28d9;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .simulate-box {
            background: #fef3c7;
            padding: 12px 16px;
            border-radius: 12px;
            margin-top: 15px;
            border: 2px dashed #d97706;
        }
        .simulate-box h4 { color: #92400e; margin-bottom: 8px; font-size: 14px; }
        .simulate-box input {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            width: 100px;
            font-size: 14px;
        }
        .simulate-box button {
            padding: 8px 16px;
            background: #6d28d9;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .simulate-box button:hover { background: #5b21b6; }
        .simulate-box .btn-quick { background: #16a34a; }
        .simulate-box .btn-quick:hover { background: #15803d; }

        .divider {
            display: flex;
            align-items: center;
            margin: 12px 0;
            color: #9ca3af;
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        .divider::before { margin-right: 10px; }
        .divider::after { margin-left: 10px; }

        @media (max-width: 480px) {
            .kiosk-card { padding: 20px 15px; }
            .kiosk-card h1 { font-size: 18px; }
            #qr-reader { min-height: 250px; }
        }
    </style>
</head>
<body>

    <div class="kiosk-container">
        <div class="kiosk-card">
            <div class="logo">🧸</div>
            <h1>KidsTrack Kiosk</h1>
            <p class="subtitle">Scan QR Code untuk kehadiran</p>

            <!-- ROLE BADGE -->
            @auth
                @php
                    $user = auth()->user();
                    $roleData = App\Http\Controllers\QRScanController::getRoleDataStatic($user->role);
                @endphp
                <div class="role-badge {{ $roleData['badge_class'] ?? 'parent' }}">
                    {{ $roleData['badge_text'] ?? '<i class="fas fa-user"></i> User' }}
                </div>
                <div class="role-info">
                    <span class="role-icon">{{ $roleData['icon'] ?? '<i class="fas fa-user"></i>' }}</span>
                    <span>Logged in as</span>
                    <span class="role-name {{ $roleData['name_class'] ?? '' }}">
                        {{ $roleData['display_name'] ?? $user->name }}
                    </span>
                </div>
            @else
                <div class="role-badge" style="background:#f3f4f6; color:#6b7280; border-color:#d1d5db;">
                    <i class="fas fa-user"></i> Guest
                </div>
                <div class="role-info">
                    <span class="role-icon"><i class="fas fa-user"></i></span>
                    <span>Not logged in</span>
                    <span class="role-name" style="color:#6b7280;">Guest</span>
                </div>
            @endauth

            <!-- TIMER INFO -->
            <div class="timer-info-box" id="timerInfoBox">
                <div class="timer-title">⏱️ Waktu Operasi Hari Ini</div>
                <div id="timerInfoContent">
                    <div class="no-timer">⏳ Memuatkan...</div>
                </div>
            </div>

            <!-- QR SCANNER -->
            <div id="scannerContainer">
                <div id="qr-reader"></div>
                <div class="scanner-controls">
                    <button class="btn-start" onclick="startScanner()">📷 Start Camera</button>
                    <button class="btn-stop" onclick="stopScanner()">⏹ Stop Camera</button>
                </div>
                <div id="scannerStatus">Klik "Start Camera" untuk imbas QR Code</div>
            </div>

            <div class="divider">— ATAU —</div>

            <!-- UPLOAD QR -->
            <div class="upload-section">
                <label class="upload-label" onclick="document.getElementById('qrFileInput').click()">
                    <i class="fas fa-upload"></i> Upload QR Code Image
                </label>
                <input type="file" id="qrFileInput" accept="image/*">
                <div class="upload-preview" id="uploadPreview">
                    <img id="qrPreviewImage" src="" alt="QR Preview">
                    <div style="margin-top:10px;">
                        <button onclick="processUploadedQR()" class="btn-primary"><i class="fas fa-search"></i> Scan QR</button>
                        <button onclick="clearUpload()" class="btn-clear"><i class="fas fa-times"></i> Clear</button>
                    </div>
                </div>
                <div id="uploadStatus"></div>
            </div>

            <div class="divider">— ATAU —</div>

            <!-- SIMULATE -->
            <div class="simulate-box">
                <h4>🧪 Simulasi Scan (Manual)</h4>
                <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:center; align-items:center;">
                    <input type="number" id="parentIdInput" placeholder="Parent ID" value="1">
                    <button onclick="simulateScan()"><i class="fas fa-search"></i> Scan</button>
                    <button onclick="quickScan()" class="btn-quick">⚡ Quick</button>
                </div>
            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div class="popup-overlay" id="warningPopup">
        <div class="popup-box">
            <div class="popup-icon">🚫</div>
            <h2 id="popupTitle"><i class="fas fa-exclamation-triangle"></i> Akses Ditolak!</h2>
            <p class="popup-sub" id="popupMessage">Maaf, anda <strong>tidak mempunyai akses</strong> ke anak ini.</p>
            <div class="popup-detail" id="popupDetail"><i class="fas fa-map-marker-alt"></i> <span id="popupScannedCode">---</span></div>
            <p style="font-size:13px; color:#6b7280;" id="popupFooter">Sila pastikan anda mengimbas QR Code anak anda sendiri.</p>
            <div class="popup-btn-group">
                <button class="popup-btn popup-btn-primary" onclick="closeWarningPopup()">👍 Faham</button>
                <button class="popup-btn popup-btn-secondary" onclick="closeWarningPopup()"><i class="fas fa-times"></i> Tutup</button>
            </div>
        </div>
    </div>

    <!-- LOADING -->
    <div class="scanning-overlay" id="scanOverlay">
        <div class="scanning-box">
            <div class="spinner"></div>
            <h3>Mengimbas QR Code...</h3>
            <p style="color:#6b7280; font-size:14px;">Sila tunggu sebentar</p>
        </div>
    </div>

    <script>
        let html5QrCode = null;
        let isProcessing = false;
        let isScanning = false;

        // ============================================
        // LOAD TIMER INFO
        // ============================================
        function loadTimerInfo() {
            fetch('/get-timer-settings')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        renderTimerInfo(data.data);
                    } else {
                        document.getElementById('timerInfoContent').innerHTML =
                            '<div class="no-timer"><i class="fas fa-exclamation-triangle"></i> Gagal memuat</div>';
                    }
                })
                .catch(() => {
                    document.getElementById('timerInfoContent').innerHTML =
                        '<div class="no-timer"><i class="fas fa-exclamation-triangle"></i> Ralat memuat</div>';
                });
        }

        function renderTimerInfo(timerSettings) {
            const now = new Date();
            const currentTime = parseInt(now.getHours().toString().padStart(2,'0') + now.getMinutes().toString().padStart(2,'0'));

            if (!Array.isArray(timerSettings) || timerSettings.length === 0) {
                document.getElementById('timerInfoContent').innerHTML =
                    '<div class="no-timer"><i class="fas fa-calendar-alt"></i> Tiada kelas aktif</div>';
                return;
            }

            function getSlotStatus(start, end) {
                if (!start || !end || start === '--:--' || end === '--:--') return 'info';
                const s = parseInt(start.replace(':', ''));
                const e = parseInt(end.replace(':', ''));
                if (currentTime >= s && currentTime <= e) return 'active';
                if (currentTime < s) return 'upcoming';
                return 'ended';
            }

            function getStatusBadge(status) {
                const map = {
                    'active': '<span class="slot-status active">🟢 Aktif</span>',
                    'upcoming': '<span class="slot-status upcoming">🔵 Akan Datang</span>',
                    'ended': '<span class="slot-status ended">⚫ Tamat</span>',
                    'info': '<span class="slot-status info"><i class="fas fa-info-circle"></i></span>'
                };
                return map[status] || map['info'];
            }

            let html = '';
            timerSettings.forEach(function(cls) {
                const startTime = cls.start_time || cls.morning?.start || '--:--';
                const endTime = cls.end_time || cls.evening?.start || '--:--';
                const status = getSlotStatus(startTime, endTime);

                html += `
                <div class="timer-row class-row">
                    <span class="slot-label"><i class="fas fa-door-open"></i> ${cls.name}</span>
                    <span class="slot-time">${startTime} - ${endTime}</span>
                    ${getStatusBadge(status)}
                </div>`;
            });

            document.getElementById('timerInfoContent').innerHTML = html;
        }

        // ============================================
        // POPUP
        // ============================================
        function showWarningPopup(title, message, detail, footer) {
            document.getElementById('popupTitle').innerHTML = title || '<i class="fas fa-exclamation-triangle"></i> Akses Ditolak!';
            document.getElementById('popupMessage').innerHTML = message || 'Maaf, anda tidak mempunyai akses.';
            document.getElementById('popupScannedCode').textContent = detail || '---';
            document.getElementById('popupFooter').textContent = footer || 'Sila imbas QR Code yang sah.';
            document.getElementById('warningPopup').classList.add('active');
        }

        function closeWarningPopup() {
            document.getElementById('warningPopup').classList.remove('active');
            isProcessing = false;
        }

        document.getElementById('warningPopup').addEventListener('click', function(e) {
            if (e.target === this) closeWarningPopup();
        });

        // ============================================
        // QR SCANNER
        // ============================================
        function startScanner() {
            const statusDiv = document.getElementById('scannerStatus');

            if (isScanning) {
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Kamera sudah aktif';
                return;
            }

            if (html5QrCode) {
                html5QrCode.clear();
                html5QrCode = null;
            }

            statusDiv.textContent = '⏳ Memulakan kamera...';
            statusDiv.style.color = '#d97706';

            html5QrCode = new Html5Qrcode("qr-reader");

            const config = {
                fps: 15,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                isScanning = true;
                statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> Kamera aktif. Imbas QR Code!';
                statusDiv.style.color = '#16a34a';
            }).catch((err) => {
                statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Gagal akses kamera: ' + err.message;
                statusDiv.style.color = '#dc2626';
            });
        }

        function stopScanner() {
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    html5QrCode = null;
                    isScanning = false;
                    document.getElementById('scannerStatus').textContent = '⏹ Kamera dihentikan';
                    document.getElementById('scannerStatus').style.color = '#6b7280';
                }).catch(() => {});
            }
        }

        // ============================================
        // ON SCAN SUCCESS
        // ============================================
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            isProcessing = true;

            console.log('<i class="fas fa-check-circle"></i> QR Code detected:', decodedText);

            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    html5QrCode = null;
                    isScanning = false;
                }).catch(() => {});
            }

            showScanning(true);

            checkAccess(decodedText);
        }

        function onScanError(errorMessage) {
            // Ignore
        }

        // ============================================
        // CHECK ACCESS
        // ============================================
        function checkAccess(qrData) {
            console.log('<i class="fas fa-upload"></i> Sending to server:', { qr_code: qrData });

            fetch('/kiosk/check-access', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    qr_code: qrData
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                showScanning(false);
                console.log('<i class="fas fa-download"></i> Server response:', data);

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    let title = '🚫 Akses Ditolak!';
                    let detail = 'Anda tidak mempunyai akses ke anak ini.';
                    let footer = 'Sila imbas QR Code anak anda sendiri.';

                    if (data.message) {
                        if (data.message.includes('QR')) {
                            title = '<i class="fas fa-times-circle"></i> QR Code Tidak Sah!';
                            detail = 'QR Code tidak dikenali.';
                            footer = 'Sila imbas QR Code yang sah.';
                        } else if (data.message.includes('waktu')) {
                            title = ' Di Luar Waktu Operasi!';
                            detail = 'Sila scan dalam waktu yang ditetapkan.';
                            footer = 'Semak waktu operasi di atas.';
                        } else {
                            detail = data.message;
                        }
                    }

                    showWarningPopup(title, detail, 'QR: ' + qrData.substring(0, 30) + '...', footer);
                    isProcessing = false;
                }
            })
            .catch(error => {
                console.error('<i class="fas fa-times-circle"></i> Error:', error);
                showScanning(false);
                showWarningPopup('<i class="fas fa-times-circle"></i> Ralat Sistem!', 'Gagal memproses.', 'Sila cuba lagi.', 'Ralat: ' + error.message);
                isProcessing = false;
            });
        }

        // ============================================
        // UPLOAD QR
        // ============================================
        document.getElementById('qrFileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const previewDiv = document.getElementById('uploadPreview');
            const previewImg = document.getElementById('qrPreviewImage');
            const statusDiv = document.getElementById('uploadStatus');

            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewDiv.style.display = 'block';
                statusDiv.textContent = '📷 Gambar dimuat naik. Klik "Scan QR" untuk proses.';
                statusDiv.style.color = '#d97706';
            };
            reader.readAsDataURL(file);
        });

        function processUploadedQR() {
            if (isProcessing) return;

            const previewImg = document.getElementById('qrPreviewImage');
            const statusDiv = document.getElementById('uploadStatus');

            if (!previewImg.src || previewImg.src === '') {
                statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Sila muat naik gambar QR Code dahulu.';
                statusDiv.style.color = '#dc2626';
                return;
            }

            statusDiv.textContent = '⏳ Memproses gambar...';
            statusDiv.style.color = '#d97706';
            isProcessing = true;

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0, img.width, img.height);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                if (typeof jsQR !== 'undefined') {
                    try {
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        if (code && code.data) {
                            statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> QR Code dikesan: ' + code.data;
                            statusDiv.style.color = '#16a34a';
                            showScanning(true);

                            checkAccess(code.data);
                        } else {
                            statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Tiada QR Code dikesan. Sila cuba gambar lain.';
                            statusDiv.style.color = '#dc2626';
                            isProcessing = false;
                        }
                    } catch (err) {
                        statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Error: ' + err.message;
                        statusDiv.style.color = '#dc2626';
                        isProcessing = false;
                    }
                } else {
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> Library jsQR tidak dimuat. Sila refresh.';
                    statusDiv.style.color = '#dc2626';
                    isProcessing = false;
                }
            };
            img.src = previewImg.src;
        }

        function clearUpload() {
            document.getElementById('qrPreviewImage').src = '';
            document.getElementById('uploadPreview').style.display = 'none';
            document.getElementById('qrFileInput').value = '';
            document.getElementById('uploadStatus').textContent = '';
        }

        // ============================================
        // SIMULATE
        // ============================================
        function simulateScan() {
            const parentId = document.getElementById('parentIdInput').value;
            if (parentId) {
                processSimulate(parentId);
            }
        }

        function quickScan() {
            document.getElementById('parentIdInput').value = 1;
            processSimulate(1);
        }

        function processSimulate(parentId) {
            if (isProcessing) return;
            isProcessing = true;

            const qrData = 'SIMULATED-' + parentId;
            showScanning(true);

            checkAccess(qrData);
        }

        function showScanning(show) {
            document.getElementById('scanOverlay').className = 'scanning-overlay' + (show ? ' active' : '');
        }

        // ============================================
        // AUTO START
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            loadTimerInfo();
            setInterval(loadTimerInfo, 30000);
            setTimeout(startScanner, 1500);
        });

        window.addEventListener('beforeunload', function() {
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(() => {});
            }
        });
    </script>

</body>
</html>
