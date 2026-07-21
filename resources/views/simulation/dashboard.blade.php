@extends('layouts.template')

@section('title', 'Simulation Clock')
@section('page-title', 'Simulation Clock')

@section('content')

<style>
    .sim-wrap { max-width: 700px; margin: 0 auto; }

    .clock-card {
        background: white; border-radius: 20px; padding: 32px;
        box-shadow: 0 4px 16px rgba(0,0,0,.05); border: 1px solid #f1f5f9;
        text-align: center; margin-bottom: 20px;
    }
    .clock-card .label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 8px; }
    .clock-card .time {
        font-size: 48px; font-weight: 800; color: #0f172a; font-family: monospace;
        letter-spacing: 4px; margin-bottom: 4px;
    }
    .clock-card .date {
        font-size: 16px; color: #64748b; font-weight: 600;
    }
    .clock-card .badge {
        display: inline-block; margin-top: 12px; padding: 4px 14px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .badge.sim-on  { background: #fef3c7; color: #d97706; }
    .badge.sim-off { background: #f1f5f9; color: #64748b; }

    .info-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;
    }
    .info-card {
        background: white; border-radius: 16px; padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,.03); border: 1px solid #f1f5f9;
    }
    .info-card .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 6px; }
    .info-card .info-value { font-size: 18px; font-weight: 800; color: #1e293b; font-family: monospace; }

    .btn-settings {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white; border: none; padding: 12px 24px; border-radius: 14px;
        font-size: 14px; font-weight: 700; text-decoration: none;
        box-shadow: 0 6px 16px rgba(255,107,107,.25); transition: .2s;
    }
    .btn-settings:hover { opacity: .9; transform: translateY(-2px); color: white; }

    @media (max-width: 500px) { .info-row { grid-template-columns: 1fr; } }
</style>

<div class="sim-wrap">

    <div class="clock-card">
        <div class="label">🕐 Simulation Time</div>
        <div class="time">{{ $clock ? substr($clock->simulation_time, 11, 8) : '--:--:--' }}</div>
        <div class="date">{{ $clock ? \Carbon\Carbon::parse($clock->simulation_time)->format('l, d F Y') : 'Not set' }}</div>
        <div>
            @if($clock && $clock->use_simulation)
                <span class="badge sim-on">⚡ Simulation ON</span>
            @else
                <span class="badge sim-off">🔒 Using Real Time</span>
            @endif
        </div>
    </div>

    <div class="info-row">
        <div class="info-card">
            <div class="info-label">🌅 Morning Window</div>
            <div class="info-value">
                {{ $clock ? substr($clock->morning_start, 0, 5) : '--:--' }}
                –
                {{ $clock ? substr($clock->morning_end, 0, 5) : '--:--' }}
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">🌙 Evening Window</div>
            <div class="info-value">
                {{ $clock ? substr($clock->evening_start, 0, 5) : '--:--' }}
                –
                {{ $clock ? substr($clock->evening_end, 0, 5) : '--:--' }}
            </div>
        </div>
    </div>

    <div style="text-align:center;">
        <a href="{{ route('simulation.setting') }}" class="btn-settings">
            ⚙️ Simulation Settings
        </a>
    </div>

</div>

@endsection
