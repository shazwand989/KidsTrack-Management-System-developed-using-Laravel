@extends('layouts.parent-template')

@section('content')
<style>
    .settings-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px; }
    .settings-card h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 20px; display: flex; align-items: center; gap: 8px; }
    .setting-item { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
    .setting-item:last-child { border-bottom: none; }
    .setting-item .label { font-size: 14px; font-weight: 600; color: #1e293b; }
    .setting-item .desc { font-size: 12px; color: #94a3b8; margin-top: 2px; }
    .toggle-switch { position: relative; width: 48px; height: 26px; display: inline-block; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #e2e8f0; border-radius: 26px; transition: .3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: .3s; }
    .toggle-switch input:checked + .toggle-slider { background: #6d28d9; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
    .btn-secondary { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; background: #f1f5f9; color: #475569; border-radius: 12px; font-weight: 700; font-size: 13px; text-decoration: none; }
    .btn-primary { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #6d28d9; color: white; border-radius: 12px; font-weight: 700; font-size: 13px; text-decoration: none; }
    .btn-primary:hover { background: #5b21b6; }
</style>

<div class="settings-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">notifications</i> Notification Preferences</h4>
    <div class="setting-item">
        <div>
            <div class="label">Push Notifications</div>
            <div class="desc">Receive push notifications on your device</div>
        </div>
        <label class="toggle-switch">
            <input type="checkbox" checked>
            <span class="toggle-slider"></span>
        </label>
    </div>
    <div class="setting-item">
        <div>
            <div class="label">Email Notifications</div>
            <div class="desc">Receive attendance alerts via email</div>
        </div>
        <label class="toggle-switch">
            <input type="checkbox" checked>
            <span class="toggle-slider"></span>
        </label>
    </div>
    <div class="setting-item">
        <div>
            <div class="label">Late Check-in Alerts</div>
            <div class="desc">Get notified when your child is late</div>
        </div>
        <label class="toggle-switch">
            <input type="checkbox" checked>
            <span class="toggle-slider"></span>
        </label>
    </div>
</div>

<div class="settings-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">display_settings</i> Display</h4>
    <div class="setting-item">
        <div>
            <div class="label">Dark Mode</div>
            <div class="desc">Switch between light and dark theme</div>
        </div>
        <label class="toggle-switch">
            <input type="checkbox">
            <span class="toggle-slider"></span>
        </label>
    </div>
</div>

<div class="settings-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">account_circle</i> Account Information</h4>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Name</div>
            <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ $parent->name ?? Auth::user()->name }}</div>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Email</div>
            <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ Auth::user()->email }}</div>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Phone</div>
            <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ $parent->phone_number ?? 'N/A' }}</div>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Role</div>
            <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
    </div>
</div>

<div style="text-align:right;">
    <a href="{{ route('parent.profile.index') }}" class="btn-secondary">
        <i class="material-symbols-rounded" style="font-size:16px;">edit</i> Edit Profile
    </a>
</div>
@endsection
