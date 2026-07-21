@extends('layouts.template')

@section('title', 'Child Profile')
@section('page-title', 'Child Profile')

@section('content')

<style>
    .profile-container {
        max-width: 1200px;
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
        width: 100px;
        height: 100px;
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
        font-size: 48px;
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

    .badge-nursery {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge.active {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-badge.inactive {
        background: #fef2f2;
        color: #dc2626;
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

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .info-card {
        background: #fff5f2;
        border-radius: 18px;
        padding: 20px;
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
        padding: 10px 0;
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

    .full-width {
        grid-column: span 2;
    }

    .parent-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        background: white;
        border-radius: 14px;
        border: 1px solid #FFE4D6;
        margin-bottom: 10px;
    }

    .parent-card:last-child {
        margin-bottom: 0;
    }

    .parent-avatar {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .parent-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .parent-avatar span {
        font-size: 20px;
        font-weight: 700;
        color: white;
    }

    .parent-details {
        flex: 1;
    }

    .parent-name {
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .parent-phone {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .parent-relation-badge {
        font-size: 10px;
        padding: 3px 10px;
        border-radius: 12px;
        background: #FFF5F2;
        color: #FF6B6B;
        font-weight: 700;
    }

    .additional-section {
        background: #fff5f2;
        border-radius: 18px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #FFE4D6;
    }

    .additional-title {
        font-size: 14px;
        font-weight: 800;
        color: #FF6B6B;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .additional-text {
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }

    .empty-text {
        color: #cbd5e1;
        font-style: italic;
    }

    .qr-code-container {
        display: flex;
        align-items: center;
        gap: 30px;
        padding: 10px 0;
        flex-wrap: wrap;
    }

    .qr-code-box {
        background: white;
        padding: 15px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        display: inline-block;
        text-align: center;
    }

    .qr-code-box img {
        width: 150px;
        height: 150px;
    }

    .qr-details {
        flex: 1;
    }

    .qr-details .qr-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
    }

    .qr-details .qr-value {
        color: #6b7280;
        font-size: 13px;
        word-break: break-all;
        display: block;
        background: #f3f4f6;
        padding: 8px 12px;
        border-radius: 8px;
        margin-top: 4px;
    }

    .qr-details .qr-value a {
        color: #6d28d9;
        text-decoration: none;
    }

    .qr-actions {
        display: flex;
        gap: 10px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .btn-qr {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: .2s;
    }

    .btn-qr.btn-success {
        background: #16a34a;
        color: white;
    }

    .btn-qr.btn-success:hover {
        background: #15803d;
    }

    .btn-qr.btn-primary {
        background: #6d28d9;
        color: white;
    }

    .btn-qr.btn-primary:hover {
        background: #5b21b6;
    }

    .btn-qr.btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-qr.btn-secondary:hover {
        background: #4b5563;
    }

    .btn-qr.btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-qr.btn-warning:hover {
        background: #d97706;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: span 1;
        }

        .profile-header-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-header-actions {
            margin-left: 0;
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .info-value {
            text-align: left;
        }

        .qr-code-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="profile-container">

    {{-- Profile Header --}}
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    @if($child->photo)
                        <img src="{{ asset('storage/'.$child->photo) }}" alt="{{ $child->name }}">
                    @else
                        <span>{{ strtoupper(substr($child->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div class="profile-info">
                    <h1>{{ $child->name }}</h1>
                    <p><span>🆔</span> IC: {{ $child->ic_number }}</p>
                    <p><span>📅</span>
                        @if($child->dob)
                            {{ \Carbon\Carbon::parse($child->dob)->format('d M Y') }} ({{ $child->age }} years old)
                        @else
                            {{ $child->age }} years old
                        @endif
                    </p>

                    <div class="profile-badges">
                        <span class="badge-nursery">
                            <span>🏫</span> {{ $child->classroom->name ?? 'No Classroom' }}
                        </span>
                        <span class="status-badge {{ $child->is_active ? 'active' : 'inactive' }}">
                            {{ $child->is_active ? '✅ Active' : '❌ Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="profile-header-actions">
                    <a href="{{ route('children.edit', hash_id($child->id)) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="{{ route('children.index') }}" class="btn-back">
                        <span>⬅️</span> Back to List
                    </a>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="content-section">

            {{-- Main Information Grid --}}
            <div class="info-grid">

                {{-- Personal Information --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span>👶</span>
                        <h3>Personal Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📛</span> Full Name</div>
                        <div class="info-value">{{ $child->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🆔</span> IC / Birth Cert</div>
                        <div class="info-value">{{ $child->ic_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📅</span> Age</div>
                        <div class="info-value">{{ $child->age }} years old</div>
                    </div>
                    @if($child->dob)
                    <div class="info-row">
                        <div class="info-label"><span>🎂</span> Date of Birth</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($child->dob)->format('d M Y') }}</div>
                    </div>
                    @endif
                </div>

                {{-- Address Information --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span>🏠</span>
                        <h3>Address</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📍</span> Home Address</div>
                        <div class="info-value">{{ $child->address ?? '-' }}</div>
                    </div>
                </div>

                {{-- Classroom Information --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span>🏫</span>
                        <h3>Classroom Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📋</span> Classroom</div>
                        <div class="info-value">{{ $child->classroom->name ?? 'Not Assigned' }}</div>
                    </div>
                    @if($child->classroom)
                    <div class="info-row">
                        <div class="info-label"><span>📅</span> Age Group</div>
                        <div class="info-value">{{ $child->classroom->min_age }}-{{ $child->classroom->max_age }} yrs</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🆔</span> Classroom Code</div>
                        <div class="info-value">{{ $child->classroom->code ?? '-' }}</div>
                    </div>
                    @endif
                </div>

                {{-- Enrollment Details --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span>📝</span>
                        <h3>Enrollment Details</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🆔</span> Child ID</div>
                        <div class="info-value">#{{ str_pad($child->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📅</span> Registered On</div>
                        <div class="info-value">{{ $child->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🔄</span> Last Updated</div>
                        <div class="info-value">{{ $child->updated_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>⚙️</span> Status</div>
                        <div class="info-value">{{ $child->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                </div>
            </div>

            {{-- Parents & Guardians Section --}}
            <div class="info-card" style="margin-bottom: 20px;">
                <div class="info-card-header">
                    <span>👨‍👩‍👧‍👦</span>
                    <h3>Parents & Guardians</h3>
                </div>

                {{-- Main Parent --}}
                <div class="parent-card">
                    <div class="parent-avatar">
                        @if($child->parent && $child->parent->photo)
                            <img src="{{ asset('storage/'.$child->parent->photo) }}" alt="">
                        @else
                            <span>{{ strtoupper(substr($child->parent->name ?? 'M', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="parent-details">
                        <div class="parent-name">
                            {{ $child->parent->name ?? 'Not Assigned' }}
                            <span class="parent-relation-badge">Main Parent</span>
                        </div>
                        <div class="parent-phone">
                            <span>📞</span> {{ $child->parent->phone_number ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- SECOND PARENT --}}
                @if($child->secondParent)
                <div class="parent-card">
                    <div class="parent-avatar">
                        @if($child->secondParent->photo)
                            <img src="{{ asset('storage/'.$child->secondParent->photo) }}" alt="">
                        @else
                            <span>{{ strtoupper(substr($child->secondParent->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="parent-details">
                        <div class="parent-name">
                            {{ $child->secondParent->name }}
                            <span class="parent-relation-badge">Second Parent</span>
                        </div>
                        <div class="parent-phone">
                            <span>📞</span> {{ $child->secondParent->phone_number ?? '-' }}
                        </div>
                    </div>
                </div>
                @else
                <div class="parent-card" style="background: #f8fafc;">
                    <div class="parent-avatar">
                        <span>➖</span>
                    </div>
                    <div class="parent-details">
                        <div class="parent-name empty-text">No second parent registered</div>
                        <div class="parent-phone">Optional field</div>
                    </div>
                </div>
                @endif

                {{-- Guardian --}}
                @if($child->guardian)
                <div class="parent-card">
                    <div class="parent-avatar">
                        @if($child->guardian->photo)
                            <img src="{{ asset('storage/'.$child->guardian->photo) }}" alt="">
                        @else
                            <span>{{ strtoupper(substr($child->guardian->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="parent-details">
                        <div class="parent-name">
                            {{ $child->guardian->name }}
                            <span class="parent-relation-badge">Guardian</span>
                        </div>
                        <div class="parent-phone">
                            <span>📞</span> {{ $child->guardian->phone_number ?? '-' }}
                        </div>
                    </div>
                </div>
                @else
                <div class="parent-card" style="background: #f8fafc;">
                    <div class="parent-avatar">
                        <span>🛡️</span>
                    </div>
                    <div class="parent-details">
                        <div class="parent-name empty-text">No guardian registered</div>
                        <div class="parent-phone">Optional field</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- QR CODE SECTION --}}
            <div class="info-card" style="margin-bottom: 20px;">
                <div class="info-card-header">
                    <span>📱</span>
                    <h3>QR Code</h3>
                </div>

                <div class="qr-code-container">
                    <div class="qr-code-box">
                        @if($child->qr_code)
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($child->qr_code) }}"
                                 alt="QR Code">
                            <div style="margin-top: 8px; font-size: 12px; color: #6b7280;">
                                Scan for check-in/out
                            </div>
                        @else
                            <div style="padding: 30px; text-align: center; color: #9ca3af;">
                                <div style="font-size: 40px;">📱</div>
                                <p style="margin: 10px 0;">No QR Code generated</p>
                                <a href="{{ route('child.qr.generate', $child->id) }}" class="btn-qr btn-primary">
                                    Generate QR Code
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($child->qr_code)
                    <div class="qr-details">
                        <div style="margin-bottom: 8px;">
                            <span class="qr-label">QR Data:</span>
                            <span class="qr-value">{{ $child->qr_code }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <span class="qr-label">QR URL:</span>
                            <span class="qr-value">
                                <a href="{{ $child->qr_code_url ?? url('/scan-qr/'.$child->qr_code) }}" target="_blank">
                                    {{ $child->qr_code_url ?? url('/scan-qr/'.$child->qr_code) }}
                                </a>
                            </span>
                        </div>
                        <div class="qr-actions">
                            <a href="{{ route('child.qr.download', $child->id) }}" class="btn-qr btn-success">
                                📥 Download PNG
                            </a>
                            <a href="{{ route('child.qr.show', $child->id) }}" class="btn-qr btn-primary">
                                🔍 View Full QR
                            </a>
                            <button onclick="printQR()" class="btn-qr btn-secondary">
                                🖨️ Print QR
                            </button>
                            <a href="{{ route('child.qr.generate', $child->id) }}" class="btn-qr btn-warning">
                                🔄 Regenerate QR
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($child->medical_notes || $child->dietary)
            <div class="additional-section">
                <div class="additional-title">
                    <span>📝</span> Additional Information
                </div>

                @if($child->medical_notes)
                <div style="margin-bottom: 15px;">
                    <div style="font-size: 12px; font-weight: 700; color: #FF6B6B; margin-bottom: 5px;">
                        <span>💊</span> Medical Notes / Allergies
                    </div>
                    <div class="additional-text">{{ $child->medical_notes }}</div>
                </div>
                @endif

                @if($child->dietary)
                <div>
                    <div style="font-size: 12px; font-weight: 700; color: #FF6B6B; margin-bottom: 5px;">
                        <span>🍽️</span> Dietary Requirements
                    </div>
                    <div class="additional-text">{{ $child->dietary }}</div>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>

</div>

<script>
function printQR() {
    var qrContent = document.querySelector('.qr-code-box').innerHTML;
    var printWindow = window.open('', '', 'height=400,width=400');
    printWindow.document.write('<html><head><title>Print QR Code</title>');
    printWindow.document.write('<style>body{text-align:center;padding:20px;font-family:Arial,sans-serif;}img{max-width:300px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h2>QR Code - {{ $child->name }}</h2>');
    printWindow.document.write(qrContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>

@endsection
