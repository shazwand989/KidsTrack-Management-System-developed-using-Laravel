<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidsTrack - Child Profile</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { 
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 50%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container { max-width: 420px; width: 100%; }
        
        .card {
            background: white;
            border-radius: 30px;
            padding: 40px 30px;
            box-shadow: 0 20px 60px rgba(109, 40, 217, 0.15);
            text-align: center;
            border: 1px solid rgba(255,255,255,0.5);
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 8px 16px;
            border-radius: 12px;
            transition: 0.3s;
        }
        .back-btn:hover { background: #f3f4f6; }

        .child-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #6d28d9, #9333ea);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            font-weight: 800;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.3);
        }
        .child-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .child-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .child-ic {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .child-class {
            display: inline-block;
            background: #f3e8ff;
            color: #6d28d9;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }

        .success-badge {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            margin: 10px 0;
        }
        .success-badge.on-time { background: #dcfce7; color: #16a34a; }
        .success-badge.late { background: #fef3c7; color: #d97706; }

        .info-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 8px 0;
            text-align: left;
        }
        .info-box .label { color: #6b7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-box .value { font-weight: 600; color: #1e293b; font-size: 15px; margin-top: 2px; }

        .fee-warning {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 15px 0;
            text-align: left;
            font-size: 14px;
            color: #92400e;
        }
        .fee-warning .icon { font-size: 18px; margin-right: 8px; }

        .btn-done {
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
        .btn-done:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 40, 217, 0.4);
        }

        .btn-kiosk {
            width: 100%;
            padding: 14px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-kiosk:hover { background: #e5e7eb; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            
            <button class="back-btn" onclick="window.location.href='{{ route('kiosk.index') }}'">←</button>

            {{-- Child Avatar --}}
            <div class="child-avatar">
                @if($child->photo)
                    <img src="{{ asset('storage/'.$child->photo) }}" alt="{{ $child->name }}">
                @else
                    {{ strtoupper(substr($child->name, 0, 1)) }}
                @endif
            </div>

            {{-- Child Info --}}
            <div class="child-name">{{ $child->name }}</div>
            <div class="child-ic">🆔 {{ $child->ic_number ?? 'No IC' }}</div>
            <div class="child-class">{{ $child->classroom->name ?? 'No Class' }}</div>

            <hr class="divider">

            {{-- Status Badge --}}
            @php
                $today = \Carbon\Carbon::now()->toDateString();
                $todayAttendance = App\Models\Attendance::where('child_id', $child->id)
                    ->whereDate('date', $today)
                    ->first();
                $isLate = $todayAttendance && $todayAttendance->status == 'late';
            @endphp

            <div class="success-badge {{ $isLate ? 'late' : 'on-time' }}">
                @if($todayAttendance)
                    @if($isLate)
                        ⏰ Late Check-in
                    @else
                        ✅ Check-in Berjaya
                    @endif
                @else
                    ✅ Belum Check-in
                @endif
            </div>

            {{-- Attendance Info --}}
            <div class="info-box">
                <div class="label">📅 Tarikh</div>
                <div class="value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
            </div>

            <div class="info-box">
                <div class="label">⏰ Masa Check-in</div>
                <div class="value">
                    @if($todayAttendance && $todayAttendance->checkin_time)
                        {{ \Carbon\Carbon::parse($todayAttendance->checkin_time)->format('h:i A') }}
                    @else
                        <span style="color: #94a3b8;">Belum check-in</span>
                    @endif
                </div>
            </div>

            @if($todayAttendance && $todayAttendance->status == 'late' && $todayAttendance->late_reason)
                <div class="info-box">
                    <div class="label">📝 Sebab Lewat</div>
                    <div class="value">{{ $todayAttendance->late_reason }}</div>
                </div>
            @endif

            <div class="info-box">
                <div class="label">🏫 Kelas</div>
                <div class="value">{{ $child->classroom->name ?? 'Tiada kelas' }}</div>
            </div>

            {{-- Fee Warning --}}
            @if($hasFeeWarning ?? false)
                <div class="fee-warning">
                    <div><span class="icon">⚠️</span> {{ $feeMessage ?? 'Anda mempunyai yuran tertunggak. Sila jelaskan segera.' }}</div>
                </div>
            @endif

            {{-- Buttons --}}
            <button class="btn-done" onclick="window.location.href='{{ route('kiosk.index') }}'">
                🏠 Kembali ke Kiosk
            </button>
            
            <button class="btn-kiosk" onclick="window.location.href='{{ route('kiosk.index') }}'">
                📷 Scan QR Lagi
            </button>

        </div>
    </div>

</body>
</html>