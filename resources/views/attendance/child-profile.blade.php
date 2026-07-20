<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $child->name }} - Check In/Out</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FF6B6B 0%, #FF9E7D 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container { max-width: 500px; margin: 0 auto; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            opacity: 0.8;
        }
        .back-link:hover { opacity: 1; }

        .profile-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .profile-header {
            background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 30px;
            background: white;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-avatar span { font-size: 48px; font-weight: 800; color: #FF6B6B; }

        .profile-header h2 { font-size: 24px; font-weight: 800; margin-bottom: 5px; }
        .profile-header p  { opacity: 0.9; font-size: 14px; }

        .alert {
            margin: 20px;
            padding: 12px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }
        .alert-success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* ── VERIFY PHONE SECTION ── */
        .verify-section {
            padding: 25px;
            border-bottom: 1px solid #f0f0f0;
        }
        .verify-section h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .verify-section p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 15px;
        }
        .phone-input-wrap {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 16px;
            gap: 10px;
            transition: border 0.2s;
            margin-bottom: 12px;
        }
        .phone-input-wrap:focus-within { border-color: #FF6B6B; }
        .phone-input-wrap input {
            flex: 1;
            border: none;
            background: none;
            font-size: 16px;
            outline: none;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .btn-verify {
            width: 100%;
            border: none;
            padding: 14px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
            color: white;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3);
            transition: all 0.2s;
        }
        .btn-verify:active { transform: translateY(-2px); }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 14px;
        }

        /* ── STATUS SECTION ── */
        .status-section {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 800;
        }
        .status-checked-in  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-checked-out { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-pending     { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        .time-info { margin-top: 15px; font-size: 13px; color: #64748b; }

        /* ── ACTION BUTTONS ── */
        .action-buttons {
            padding: 25px;
            display: flex;
            gap: 15px;
        }
        .btn {
            flex: 1;
            border: none;
            padding: 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }
        .btn-checkin  { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; box-shadow: 0 4px 15px rgba(34,197,94,0.3); }
        .btn-checkout { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; box-shadow: 0 4px 15px rgba(239,68,68,0.3); }
        .btn:active   { transform: translateY(-2px); }
        .btn-disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-disabled:active { transform: none; }

        .info-note {
            background: #f8fafc;
            padding: 15px;
            margin: 0 20px 20px;
            border-radius: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }

        @media (max-width: 480px) {
            .profile-avatar { width: 80px; height: 80px; }
            .profile-avatar span { font-size: 36px; }
            .btn { padding: 14px; font-size: 14px; }
            .status-badge { font-size: 14px; padding: 10px 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <a href="{{ route('attendance-scan.search') }}" class="back-link">← Cari Anak Lain</a>

    <div class="profile-card">

        {{-- Header --}}
        <div class="profile-header">
            <div class="profile-avatar">
                @if($child->photo)
                    <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                @else
                    <span>{{ strtoupper(substr($child->name, 0, 1)) }}</span>
                @endif
            </div>
            <h2>{{ $child->name }}</h2>
            <p>{{ $child->classroom->name ?? 'No classroom' }} • Age {{ $child->age }}</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">⚠️ {{ session('error') }}</div>
        @endif

        {{-- VERIFY PHONE -- tunjuk kalau belum verified --}}
        @if(!$verified)
        <div class="verify-section">
            <h3>🔒 Sahkan Identiti</h3>
            <p>Masukkan no telefon ibu bapa / penjaga untuk teruskan</p>

            <form action="{{ route('attendance-scan.verify', $child->id) }}" method="POST">
                @csrf
                <div class="phone-input-wrap">
                    <span style="font-size:20px">📱</span>
                    <input
                        type="tel"
                        name="phone"
                        placeholder="Contoh: 0123456789"
                        autocomplete="tel"
                        required
                    >
                </div>
                <button type="submit" class="btn-verify">
                    🔓 Sahkan & Teruskan
                </button>
            </form>
        </div>

        {{-- Kalau belum verify, sembunyikan status & butang --}}
        <div class="info-note">
            <p>🔒 Verification diperlukan untuk keselamatan anak anda</p>
        </div>

        @else
        {{-- STATUS -- tunjuk lepas verified --}}
        <div class="status-section">
            <div class="verified-badge">✅ Identiti Disahkan</div>

            <div style="margin-top: 20px;">
                @php
                    $status = $attendance->status ?? null;
                    $checkinTime = $attendance->checkin_time ?? null;
                    $checkoutTime = $attendance->checkout_time ?? null;
                    $dropOffBy = $attendance->drop_off_by ?? null;
                    $pickupBy = $attendance->pickup_by ?? null;
                @endphp

                @if($status == 'checkin')
                    <div class="status-badge status-checked-in">✅ Checked In</div>
                    <div class="time-info">
                        📅 Check-in: {{ $checkinTime ? date('h:i A', strtotime($checkinTime)) : '-' }}
                    </div>
                    @if($dropOffBy)
                    <div class="time-info">👨‍👩‍👧 Dihantar oleh: {{ $dropOffBy }}</div>
                    @endif

                @elseif($status == 'checkout')
                    <div class="status-badge status-checked-out">📤 Checked Out</div>
                    <div class="time-info">
                        📅 Check-out: {{ $checkoutTime ? date('h:i A', strtotime($checkoutTime)) : '-' }}
                    </div>
                    @if($pickupBy)
                    <div class="time-info">👨‍👩‍👧 Dijemput oleh: {{ $pickupBy }}</div>
                    @endif

                @else
                    <div class="status-badge status-pending">⏰ Belum Check In</div>
                    <div class="time-info">Sila check in untuk mulakan hari</div>
                @endif
            </div>
        </div>

        {{-- RESULT MESSAGE --}}
        <div id="resultMsg" style="display:none; margin: 0 20px;"></div>

        {{-- ACTION BUTTONS -- tunjuk lepas verified --}}
        <div class="action-buttons" id="actionButtons">
            @php $status = $status ?? null; @endphp
            <button class="btn btn-checkin {{ $status == 'checkin' ? 'btn-disabled' : '' }}"
                    {{ $status == 'checkin' ? 'disabled' : '' }}
                    onclick="doAction('checkin', {{ $child->id }})">
                ✅ {{ $status == 'checkin' ? 'Dah Check In' : 'CHECK IN' }}
            </button>

            <button class="btn btn-checkout {{ $status != 'checkin' ? 'btn-disabled' : '' }}"
                    {{ $status != 'checkin' ? 'disabled' : '' }}
                    onclick="doAction('checkout', {{ $child->id }})">
                📤 {{ $status == 'checkout' ? 'Dah Check Out' : ($status == 'checkin' ? 'CHECK OUT' : 'Check In Dulu') }}
            </button>
        </div>

        <div class="info-note">
            <p>⏰ Check-in: 7:00 AM - 12:00 PM | Check-out: 12:00 PM - 8:00 PM</p>
        </div>

        @endif {{-- end verified --}}

    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const childId = {{ $child->id }};

function showResult(msg, type) {
    const el = document.getElementById('resultMsg');
    el.textContent = msg;
    el.className = 'alert alert-' + (type === 'success' ? 'success' : 'error');
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

async function doAction(action, id) {
    const btnCheckin = document.querySelector('.btn-checkin');
    const btnCheckout = document.querySelector('.btn-checkout');
    const url = '/attendance-scan/' + action + '/' + id;

    // Disable both buttons during request
    btnCheckin.disabled = true;
    btnCheckout.disabled = true;
    const origIn = btnCheckin.textContent;
    const origOut = btnCheckout.textContent;
    btnCheckin.textContent = '⏳ ...';
    btnCheckout.textContent = '⏳ ...';

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await res.json();

        if (data.success) {
            showResult(data.message || '✅ Berjaya!', 'success');
            // Reload after short delay to show updated status
            setTimeout(() => { location.reload(); }, 800);
        } else {
            showResult(data.message || '❌ Gagal', 'error');
            btnCheckin.disabled = false;
            btnCheckout.disabled = false;
            btnCheckin.textContent = origIn;
            btnCheckout.textContent = origOut;
        }
    } catch (err) {
        showResult('⚠️ Ralat sambungan. Cuba semula.', 'error');
        btnCheckin.disabled = false;
        btnCheckout.disabled = false;
        btnCheckin.textContent = origIn;
        btnCheckout.textContent = origOut;
    }
}
</script>

</body>
</html>