@extends('layouts.parent-template')

@section('content')
<style>
    .page-card { background: white; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; }
    .page-card h4 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 20px; display: flex; align-items: center; gap: 8px; }
    .notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px; border-radius: 14px; background: #f8fafc; margin-bottom: 10px; border: 1px solid #f1f5f9; }
    .notif-item.unread { background: #ede9fe; border-color: #ddd6fe; }
    .notif-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .notif-icon.info { background: #e3f2fd; color: #1565c0; }
    .notif-icon.warn { background: #fff3e0; color: #e65100; }
    .notif-icon.success { background: #e8f5e9; color: #2e7d32; }
    .notif-body h5 { font-size: 14px; font-weight: 700; color: #1e293b; margin: 0; }
    .notif-body p { font-size: 12px; color: #94a3b8; margin: 4px 0 0; }
    .notif-time { font-size: 11px; color: #94a3b8; white-space: nowrap; }
</style>

<div class="page-card">
    <h4><i class="material-symbols-rounded" style="font-size:20px;">notifications</i> Notifications</h4>

    <div class="notif-item unread">
        <div class="notif-icon info"><i class="material-symbols-rounded" style="font-size:18px;">info</i></div>
        <div class="notif-body">
            <h5>Welcome to KidsTrack!</h5>
            <p>Track your child's attendance and stay updated with real-time notifications.</p>
        </div>
        <div class="notif-time">Just now</div>
    </div>

    <div class="notif-item">
        <div class="notif-icon success"><i class="material-symbols-rounded" style="font-size:18px;">check_circle</i></div>
        <div class="notif-body">
            <h5>QR Code Generated</h5>
            <p>Your QR code has been generated successfully. You can now use it for check-in.</p>
        </div>
        <div class="notif-time">2 hours ago</div>
    </div>

    <div class="notif-item">
        <div class="notif-icon warn"><i class="material-symbols-rounded" style="font-size:18px;">schedule</i></div>
        <div class="notif-body">
            <h5>Attendance Reminder</h5>
            <p>Don't forget to check in your child tomorrow morning. School starts at 8:00 AM.</p>
        </div>
        <div class="notif-time">Yesterday</div>
    </div>

    <div style="text-align:center;padding:20px;color:#94a3b8;">
        <i class="material-symbols-rounded" style="font-size:32px;display:block;margin-bottom:8px;">inbox</i>
        No more notifications
    </div>
</div>
@endsection
