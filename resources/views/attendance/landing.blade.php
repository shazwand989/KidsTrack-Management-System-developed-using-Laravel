{{-- resources/views/attendance/landing.blade.php --}}
@extends('layouts.guest')

@section('title', 'KidsTrack')
@section('content')

<style>
    body {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }
    
    .landing-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .logo-section {
        text-align: center;
        padding: 40px 20px;
        color: white;
    }
    
    .logo-section h1 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 10px;
    }
    
    .logo-section p {
        opacity: 0.9;
    }
    
    .children-list {
        background: white;
        border-radius: 30px;
        padding: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .child-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    
    .child-card:last-child {
        border-bottom: none;
    }
    
    .child-card:hover {
        background: #FFF5F2;
        border-radius: 20px;
        transform: translateX(5px);
    }
    
    .child-avatar {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 800;
        overflow: hidden;
    }
    
    .child-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .child-info {
        flex: 1;
    }
    
    .child-name {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
    }
    
    .child-class {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    
    .status-checked-in {
        background: #f0fdf4;
        color: #16a34a;
    }
    
    .status-checked-out {
        background: #fef2f2;
        color: #dc2626;
    }
    
    .arrow-icon {
        color: #FF6B6B;
        font-size: 20px;
    }
    
    .footer-note {
        text-align: center;
        padding: 20px;
        color: white;
        font-size: 12px;
        opacity: 0.8;
    }
</style>

<div class="landing-container">
    <div class="logo-section">
        <h1>🏫 KidsTrack</h1>
        <p>Scan to check-in / check-out your child</p>
    </div>
    
    <div class="children-list">
        <div style="padding: 10px 15px; border-bottom: 1px solid #f0f0f0;">
            <span style="font-weight: 700; color: #1e293b;">👶 Select Your Child</span>
        </div>
        
        @foreach($children as $child)
        <a href="{{ route('attendance-scan.child', $child->id) }}" class="child-card">
            <div class="child-avatar">
                @if($child->photo)
                    <img src="{{ asset('storage/'.$child->photo) }}" alt="">
                @else
                    {{ strtoupper(substr($child->name, 0, 1)) }}
                @endif
            </div>
            <div class="child-info">
                <div class="child-name">{{ $child->name }}</div>
                <div class="child-class">
                    <span>🏫 {{ $child->classroom->name ?? 'No class' }}</span>
                    <span>👶 {{ $child->age }} yrs</span>
                </div>
            </div>
            <div class="arrow-icon">→</div>
        </a>
        @endforeach
    </div>
    
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Secure check-in/out system
    </div>
</div>

@endsection