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

    /* Content Sections */
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
                    <p><span>📅</span> {{ $child->dob ? $child->dob->format('d M Y') . ' (' . $child->age . ' years old)' : $child->age . ' years old' }}</p>
                    
                    <div class="profile-badges">
                        <span class="badge-nursery">
                            <span>🏫</span> {{ $child->nursery_type_label }}
                        </span>
                        <span class="status-badge {{ $child->is_active ? 'active' : 'inactive' }}">
                            {{ $child->is_active ? '✅ Active' : '❌ Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="profile-header-actions">
                    <a href="{{ route('children.edit', $child->id) }}" class="btn-edit">
                        <span>✏️</span> Edit Profile
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
                        <div class="info-value">{{ $child->dob->format('d M Y') }}</div>
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
                
                {{-- Nursery Information --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span>🏫</span>
                        <h3>Nursery Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📋</span> Nursery Type</div>
                        <div class="info-value">{{ $child->nursery_type_label }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📅</span> Enrollment Date</div>
                        <div class="info-value">{{ $child->enrollment_date ? $child->enrollment_date->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>⚙️</span> Status</div>
                        <div class="info-value">{{ $child->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
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
                            <span>📞</span> {{ $child->parent->phone ?? '-' }}
                        </div>
                    </div>
                </div>
                
                {{-- Second Parent --}}
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
                            <span>📞</span> {{ $child->secondParent->phone ?? '-' }}
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
                            <span>📞</span> {{ $child->guardian->phone ?? '-' }}
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
            
            {{-- Additional Information --}}
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

@endsection