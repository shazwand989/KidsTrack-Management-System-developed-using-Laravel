<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - SAFECARE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 50%, #fff7ed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 420px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
        .header p { font-size: 14px; color: #94a3b8; }
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            margin-bottom: 14px;
        }
        .card-title {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .child-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            border-radius: 14px;
            border: 1.5px solid #f1f5f9;
            cursor: pointer;
            transition: all .2s;
            margin-bottom: 10px;
            background: white;
            text-decoration: none;
            color: inherit;
        }
        .child-item:last-child { margin-bottom: 0; }
        .child-item:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59,130,246,0.1);
        }
        .child-avatar {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 20px;
            flex-shrink: 0;
        }
        .child-info { flex: 1; }
        .child-name { font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 2px; }
        .child-meta { font-size: 12px; color: #94a3b8; display: flex; gap: 12px; }
        .child-arrow { color: #cbd5e1; font-size: 18px; }
        .child-item:hover .child-arrow { color: #3b82f6; }
        .checkin-time-badge {
            font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 20px;
            background: #dcfce7; color: #16a34a;
            white-space: nowrap;
        }
        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border-radius: 14px;
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: .2s;
        }
        .btn-back:hover { background: #e2e8f0; }
        .empty {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
        }
        .empty i { font-size: 40px; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-upload"></i> Checkout</h1>
        <p>Pilih anak untuk check-out</p>
    </div>

    <div class="card">
        <div class="card-title">
            <i class="fas fa-sign-out-alt" style="color:#3b82f6;"></i>
            Anak Yang Perlu Checkout
        </div>

        @foreach($children as $child)
        <a href="{{ route('kiosk.checkin.page', $child->id) }}" class="child-item">
            <div class="child-avatar">{{ strtoupper(substr($child->name, 0, 1)) }}</div>
            <div class="child-info">
                <div class="child-name">{{ $child->name }}</div>
                <div class="child-meta">
                    <span><i class="fas fa-school"></i> {{ $child->classroom->name ?? '-' }}</span>
                </div>
            </div>
            <span class="checkin-time-badge">
                🕐 {{ \Carbon\Carbon::parse($child->checkin_time)->format('h:i A') }}
            </span>
            <i class="fas fa-chevron-right child-arrow"></i>
        </a>
        @endforeach
    </div>

    <a href="{{ route('kiosk.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Kiosk
    </a>
</div>
</body>
</html>
