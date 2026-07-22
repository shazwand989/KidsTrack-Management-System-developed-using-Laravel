@extends('layouts.template')

@section('title', 'Simulation Settings')
@section('page-title', 'Simulation Settings')

@section('content')

<style>
    .sim-wrap { max-width: 600px; margin: 0 auto; }

    .card {
        background: white; border-radius: 20px; padding: 28px;
        box-shadow: 0 4px 16px rgba(0,0,0,.05); border: 1px solid #f1f5f9;
        margin-bottom: 20px;
    }
    .card h3 { font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
    .card .desc { font-size: 12px; color: #94a3b8; margin-bottom: 20px; }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .5px; }
    .form-group input, .form-group select {
        width: 100%; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 10px 14px; font-size: 14px; color: #1e293b; font-weight: 600;
    }
    .form-group input:focus { border-color: #FF6B6B; outline: none; box-shadow: 0 0 0 3px rgba(255,107,107,.12); }

    .time-row { display: flex; gap: 10px; align-items: center; }
    .time-row input { flex: 1; }
    .time-row .sep { color: #94a3b8; font-weight: 700; flex-shrink: 0; }

    .toggle-row { display: flex; align-items: center; gap: 12px; margin-top: 20px; }
    .toggle-label { font-weight: 700; font-size: 14px; color: #1e293b; }
    .toggle-switch { position: relative; width: 48px; height: 26px; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background: #e2e8f0; border-radius: 26px; transition: .3s;
    }
    .toggle-slider:before {
        content: ""; position: absolute; height: 20px; width: 20px;
        left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: .3s;
    }
    .toggle-switch input:checked + .toggle-slider { background: #FF6B6B; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }

    .btn-row { display: flex; gap: 10px; margin-top: 20px; }
    .btn-save {
        flex: 1; border: none; padding: 12px 24px; border-radius: 14px;
        font-size: 14px; font-weight: 700; cursor: pointer; color: white;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        box-shadow: 0 6px 16px rgba(255,107,107,.25); transition: .2s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-2px); }
    .btn-back {
        padding: 12px 20px; border-radius: 14px; font-size: 13px; font-weight: 700;
        border: 1px solid #e2e8f0; background: white; color: #475569;
        text-decoration: none; display: flex; align-items: center; gap: 6px;
    }
    .btn-back:hover { background: #f8fafc; }

    .alert {
        padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; margin-bottom: 16px;
    }
    .alert.success { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
</style>

<div class="sim-wrap">

    @if(session('success'))
        <div class="alert success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <form action="{{ route('simulation.save') }}" method="POST">
        @csrf

        <div class="card">
            <h3>🕐 Simulation Clock</h3>
            <p class="desc">Override the system time for testing attendance windows.</p>

            <div class="form-group">
                <label>Simulation Date & Time</label>
                <input type="datetime-local" name="simulation_time"
                    value="{{ $clock ? \Carbon\Carbon::parse($clock->simulation_time)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
            </div>

            <div class="toggle-row">
                <label class="toggle-switch">
                    <input type="checkbox" name="use_simulation" value="1"
                        {{ ($clock && $clock->use_simulation) ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label">Enable Simulation Mode</span>
            </div>
        </div>

        <div class="card">
            <h3>🌅 Morning Check-in Window</h3>
            <p class="desc">Time range when morning check-in is allowed.</p>

            <div class="time-row">
                <input type="time" name="morning_start"
                    value="{{ $clock ? substr($clock->morning_start, 0, 5) : '07:00' }}">
                <span class="sep">to</span>
                <input type="time" name="morning_end"
                    value="{{ $clock ? substr($clock->morning_end, 0, 5) : '07:30' }}">
            </div>
        </div>

        <div class="card">
            <h3>🌙 Evening Check-out Window</h3>
            <p class="desc">Time range when evening check-out is allowed.</p>

            <div class="time-row">
                <input type="time" name="evening_start"
                    value="{{ $clock ? substr($clock->evening_start, 0, 5) : '17:00' }}">
                <span class="sep">to</span>
                <input type="time" name="evening_end"
                    value="{{ $clock ? substr($clock->evening_end, 0, 5) : '17:30' }}">
            </div>
        </div>

        <div class="btn-row">
            <a href="{{ route('simulation.dashboard') }}" class="btn-back">← Back</a>
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>

</div>

@endsection
