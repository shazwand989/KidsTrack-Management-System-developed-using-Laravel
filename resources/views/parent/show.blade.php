@extends('layouts.template')

@section('title', 'Parent Profile')
@section('page-title', 'Parent Profile')

@section('content')

<style>
    .profile-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .profile-card {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        background: white;
        margin-bottom: 24px;
    }

    .profile-header {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .profile-header::after {
        content: "";
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        position: absolute;
        right: -50px;
        bottom: -50px;
    }

    .profile-header-content {
        display: flex;
        align-items: center;
        gap: 25px;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 25px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar span {
        font-size: 56px;
        font-weight: 800;
        color: #FF6B6B;
    }

    .profile-info h1 {
        color: white;
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .profile-info p {
        color: rgba(255,255,255,0.9);
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-badges {
        display: flex;
        gap: 10px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .badge-status {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .profile-header-actions {
        margin-left: auto;
        display: flex;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .btn-edit {
        background: white;
        color: #FF6B6B !important;
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-back {
        background: rgba(255,255,255,0.2);
        color: white !important;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s;
    }

    .btn-back:hover {
        background: rgba(255,255,255,0.3);
    }

    .content-section {
        padding: 28px;
    }

    /* Two Column Layout */
    .two-column-layout {
        display: flex;
        gap: 24px;
    }

    .left-column {
        flex: 2;
    }

    .right-column {
        flex: 1;
    }

    /* Info Cards */
    .info-card {
        background: #fff5f2;
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #FFE4D6;
        transition: all .2s;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255,107,107,0.1);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFE4D6;
    }

    .info-card-header span {
        font-size: 24px;
    }

    .info-card-header h3 {
        font-size: 14px;
        font-weight: 800;
        color: #FF6B6B;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #FFF0EC;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        text-align: right;
    }

    /* Right Column Cards */
    .right-card {
        background: #fff5f2;
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #FFE4D6;
        text-align: center;
    }

    .right-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #FFE4D6;
    }

    .right-card-header span {
        font-size: 20px;
    }

    .right-card-header h3 {
        font-size: 13px;
        font-weight: 800;
        color: #FF6B6B;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .photo-gallery {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .photo-item {
        text-align: center;
    }

    .photo-item .label {
        font-size: 11px;
        font-weight: 700;
        color: #FF6B6B;
        margin-bottom: 8px;
    }

    .photo-preview {
        width: 100%;
        max-width: 180px;
        height: 120px;
        border-radius: 15px;
        background: white;
        overflow: hidden;
        margin: 0 auto;
        border: 1px solid #FFE4D6;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-box {
        text-align: center;
    }

    .qr-code {
        background: white;
        padding: 15px;
        border-radius: 15px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .qr-code img {
        width: 150px;
        height: 150px;
    }

    .settings-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
    }

    .settings-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-badge.verified {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-badge.emergency {
        background: #fffbeb;
        color: #d97706;
    }

    .status-badge.unverified {
        background: #fef2f2;
        color: #dc2626;
    }

    .child-badge {
        background: #FFE5DD;
        color: #FF6B6B;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin: 0 5px 5px 0;
    }

    @media (max-width: 992px) {
        .two-column-layout {
            flex-direction: column;
        }
        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        .info-value {
            text-align: left;
        }
    }
</style>

<div class="profile-container">

<div class="profile-card">

    {{-- HEADER --}}
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                @if($parent->photo)
                    <img src="{{ asset('storage/'.$parent->photo) }}" alt="{{ $parent->name }}">
                @else
                    <span>{{ strtoupper(substr($parent->name, 0, 1)) }}</span>
                @endif
            </div>

            <div class="profile-info">
                <h1>{{ $parent->name }}</h1>
                <p><span><i class="fas fa-phone"></i></span> {{ $parent->phone_number ?? '-' }}</p>
                <p><span><i class="fas fa-envelope" style="font-size:10px;"></i></span> {{ $parent->email ?? 'No email' }}</p>

                <div class="profile-badges">
                    <span class="badge-status">
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">family_restroom</i></span>
                        {{ $parent->role == 'parent1' ? 'Main Parent' : ($parent->role == 'parent2' ? 'Second Parent' : ($parent->role == 'guardian' ? 'Guardian' : 'Parent')) }}
                    </span>
                    @if($parent->verified)
                        <span class="badge-status"><i class="fas fa-check-circle" style="font-size:10px;"></i> Verified</span>
                    @endif
                </div>
            </div>

            <div class="profile-header-actions">
                <a href="{{ route('parents.edit', $parent->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('parents.index') }}" class="btn-back">
                    <span>⬅️</span> Back
                </a>
            </div>
        </div>
    </div>

    {{-- BODY - TWO COLUMN LAYOUT --}}
    <div class="content-section">

        <div class="two-column-layout">

            {{-- LEFT COLUMN - INFO CARDS --}}
            <div class="left-column">

                {{-- MAIN PARENT INFO CARD --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">person</i></span>
                        <h3>Main Parent Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📛</span> Full Name</div>
                        <div class="info-value">{{ $parent->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-envelope" style="font-size:10px;"></i></span> Email</div>
                        <div class="info-value">{{ $parent->email ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-chart-bar"></i></span> Age</div>
                        <div class="info-value">{{ $parent->age ?? '-' }} years old</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-phone"></i></span> Phone Number</div>
                        <div class="info-value">{{ $parent->phone_number ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🏠</span> Home Address</div>
                        <div class="info-value">{{ $parent->address ?? '-' }}</div>
                    </div>
                </div>

                {{-- CHILDREN CARD --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">child_care</i></span>
                        <h3>Children</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📚</span> Registered Children</div>
                        <div class="info-value">
                            @if($parent->children && count($parent->children))
                                @foreach($parent->children as $child)
                                    <span class="child-badge">👨‍🎓 {{ $child->name }}</span>
                                @endforeach
                            @else
                                <span style="color:#cbd5e1;">No children registered yet</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- LINKED FAMILY MEMBERS --}}
                @if(isset($relatedUsers) && count($relatedUsers))
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">group</i></span>
                        <h3>Linked Family Members</h3>
                    </div>
                    @foreach($relatedUsers as $role => $users)
                        @foreach($users as $related)
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                            <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#60a5fa);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:16px;">
                                {{ strtoupper(substr($related->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:600;font-size:14px;">{{ $related->name }}</div>
                                <div style="font-size:12px;color:#64748b;">
                                    {{ $role === 'parent2' ? 'Second Parent' : ($role === 'guardian' ? 'Guardian' : ucfirst($role)) }}
                                    @if($related->phone_number) · <i class="fas fa-phone"></i> {{ $related->phone_number }} @endif
                                </div>
                                @php
                                    $sharedChildren = $related->guardianships->pluck('child_id');
                                    $sharedNames = $parent->children->whereIn('id', $sharedChildren)->pluck('name')->join(', ');
                                @endphp
                                <div style="font-size:11px;color:#94a3b8;">Shared: {{ $sharedNames }}</div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
                @endif

            </div>

            {{-- RIGHT COLUMN - PHOTO, QR, SETTINGS --}}
            <div class="right-column">

                {{-- PHOTO GALLERY CARD --}}
                <div class="right-card">
                    <div class="right-card-header">
                        <span><i class="fas fa-camera"></i></span>
                        <h3>Photos</h3>
                    </div>
                    <div class="photo-gallery">
                        @if($parent->photo)
                        <div class="photo-item">
                            <div class="label">Photo</div>
                            <div class="photo-preview">
                                <img src="{{ asset('storage/'.$parent->photo) }}" alt="">
                            </div>
                        </div>
                        @endif

                        @if(!$parent->photo)
                            <div class="empty-text" style="padding:20px;">No photo uploaded yet</div>
                        @endif
                    </div>
                </div>

                {{-- QR CODE CARD --}}
                <div class="right-card">
                    <div class="right-card-header">
                        <span><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">smartphone</i></span>
                        <h3>QR Check-in</h3>
                    </div>
                    <div class="qr-box">
                        <div class="qr-code">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ url('/checkin/'.$parent->id) }}" alt="QR Code">
                        </div>
                        <p style="color:#64748b; margin-bottom:5px; font-size:12px;">
                            Scan to check-in for events
                        </p>
                        <small style="color:#94a3b8;">ID: {{ str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}</small>
                    </div>
                </div>

                {{-- SETTINGS CARD --}}
                <div class="right-card">
                    <div class="right-card-header">
                        <span>⚙️</span>
                        <h3>Settings</h3>
                    </div>
                    <div class="settings-row">
                        <span class="settings-label"><i class="fas fa-check-circle" style="font-size:10px;"></i> Verified Status:</span>
                        <span class="status-badge {{ $parent->verified ? 'verified' : 'unverified' }}">
                            @if($parent->verified)
                                <i class="fas fa-check-circle" style="font-size:10px;"></i> Verified
                            @else
                                <i class="fas fa-times-circle"></i> Unverified
                            @endif
                        </span>
                    </div>
                    <div class="settings-row">
                        <span class="settings-label">👑 Role:</span>
                        <span class="status-badge verified">
                            {{ $parent->role ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- QUICK INFO CARD --}}
                <div class="right-card">
                    <div class="right-card-header">
                        <span>ℹ️</span>
                        <h3>Quick Info</h3>
                    </div>
                    <div class="settings-row">
                        <span class="settings-label">🆔 Parent ID:</span>
                        <span class="settings-label">#{{ str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="settings-row">
                        <span class="settings-label"><i class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;">calendar_month</i> Registered:</span>
                        <span class="settings-label">{{ $parent->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="settings-row">
                        <span class="settings-label">🔄 Last Updated:</span>
                        <span class="settings-label">{{ $parent->updated_at->format('d M Y') }}</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
