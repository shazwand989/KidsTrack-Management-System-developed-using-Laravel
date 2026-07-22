@extends('layouts.template')

@section('title', 'Teacher Profile')
@section('page-title', 'Teacher Profile')

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

    .badge-position {
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

    .status-badge.on-leave {
        background: #fffbeb;
        color: #d97706;
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

    .qualification-box {
        background: #fff5f2;
        border-radius: 18px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #FFE4D6;
    }

    .qualification-title {
        font-size: 14px;
        font-weight: 800;
        color: #FF6B6B;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qualification-text {
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
                    @if($teacher->photo)
                        <img src="{{ asset('storage/'.$teacher->photo) }}" alt="{{ $teacher->name }}">
                    @else
                        <span>{{ $teacher->initial }}</span>
                    @endif
                </div>
                
                <div class="profile-info">
                    <h1>{{ $teacher->name }}</h1>
                    <p><span><i class="fas fa-phone"></i></span> {{ $teacher->phone ?? 'No phone number' }}</p>
                    <p><span>✉️</span> {{ $teacher->email ?? 'No email' }}</p>
                    
                    <div class="profile-badges">
                        <span class="badge-position">
                            <span>👩‍<i class="fas fa-school"></i></span> {{ $teacher->position }}
                        </span>
                        <span class="status-badge {{ $teacher->status_color }}">
                            {{ $teacher->status_badge }}
                        </span>
                    </div>
                </div>
                
                <div class="profile-header-actions">
                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="{{ route('teachers.index') }}" class="btn-back">
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
                        <span><i class="fas fa-user"></i></span>
                        <h3>Personal Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📛</span> Full Name</div>
                        <div class="info-value">{{ $teacher->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>👩‍<i class="fas fa-school"></i></span> Position</div>
                        <div class="info-value">{{ $teacher->position }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-calendar-alt"></i></span> Age</div>
                        <div class="info-value">{{ $teacher->age }} years old</div>
                    </div>
                </div>
                
                {{-- Contact Information --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="fas fa-phone"></i></span>
                        <h3>Contact Information</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📱</span> Phone Number</div>
                        <div class="info-value">{{ $teacher->phone ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>✉️</span> Email</div>
                        <div class="info-value">{{ $teacher->email ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-map-marker-alt"></i></span> Address</div>
                        <div class="info-value">{{ $teacher->address ?? '-' }}</div>
                    </div>
                </div>
                
                {{-- Teaching Assignment --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="fas fa-school"></i></span>
                        <h3>Teaching Assignment</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>📚</span> Nursery Class</div>
                        <div class="info-value">{{ $teacher->nursery_class ?? 'Not assigned' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>⚙️</span> Status</div>
                        <div class="info-value">
                            <span class="status-badge {{ $teacher->status_color }}" style="font-size: 11px;">
                                {{ $teacher->status_badge }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Employment Details --}}
                <div class="info-card">
                    <div class="info-card-header">
                        <span><i class="fas fa-calendar-alt"></i></span>
                        <h3>Employment Details</h3>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🆔</span> Teacher ID</div>
                        <div class="info-value">#{{ str_pad($teacher->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span><i class="fas fa-calendar-alt"></i></span> Join Date</div>
                        <div class="info-value">{{ $teacher->join_date ? $teacher->join_date->format('d M Y') : 'Not set' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><span>🔄</span> Registered On</div>
                        <div class="info-value">{{ $teacher->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i class="fas fa-edit"></i> Last Updated</div>
                        <div class="info-value">{{ $teacher->updated_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
            
            {{-- Qualifications Section --}}
            <div class="qualification-box">
                <div class="qualification-title">
                    <span>🎓</span> Qualifications & Certifications
                </div>
                @if($teacher->qualifications)
                    <div class="qualification-text">
                        {{ $teacher->qualifications }}
                    </div>
                @else
                    <div class="qualification-text empty-text">
                        No qualifications recorded yet.
                    </div>
                @endif
            </div>
            
        </div>
    </div>
    
</div>

@endsection