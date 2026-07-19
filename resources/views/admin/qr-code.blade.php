@extends('layouts.template')

@section('title', 'QR Code')
@section('page-title', 'QR Code Check-in/Check-out')

@section('content')

<style>
    .qr-container {
        max-width: 500px;
        margin: 0 auto;
        text-align: center;
    }
    
    .qr-card {
        background: white;
        border-radius: 30px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .qr-code {
        background: white;
        padding: 20px;
        border-radius: 20px;
        display: inline-block;
        margin: 20px 0;
    }
    
    .qr-code img {
        width: 250px;
        height: 250px;
    }
    
    .btn-download {
        background: linear-gradient(135deg, #FF6B6B, #FF9E7D);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 20px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-download:hover {
        transform: translateY(-2px);
        color: white;
    }
    
    .qr-url {
        background: #f8fafc;
        padding: 10px;
        border-radius: 12px;
        font-size: 12px;
        word-break: break-all;
        margin-top: 15px;
    }
    
    .info-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 12px;
        margin-top: 20px;
        font-size: 12px;
        color: #16a34a;
    }

    .warning-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 16px;
        padding: 12px;
        margin-top: 10px;
        font-size: 12px;
        color: #d97706;
    }
</style>

<div class="qr-container">
    <div class="qr-card">
        <h2>📱 QR Code Check-in / Check-out</h2>
        <p>Scan QR code ini untuk check in / check out</p>
@php
    $scanUrl = 'https://photo-routers-clinics-documented.trycloudflare.com/attendance/search';
@endphp

        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($scanUrl) }}" alt="QR Code">
        </div>
        
        <div class="qr-url">
            <strong>Scan URL:</strong><br>
            {{ $scanUrl }}
        </div>
        
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($scanUrl) }}" 
           download="qrcode-attendance.png" 
           class="btn-download">
            ⬇️ Download QR Code
        </a>

        <div class="info-box">
            ✅ Parents boleh scan guna <strong>data sendiri</strong> — tak perlu WiFi taska<br>
            ✅ Boleh scan dari rumah, kedai, mana-mana sahaja!
        </div>

        <div class="warning-box">
            ⚠️ URL ini hanya valid selagi terminal cloudflared dibuka.<br>
            ⚠️ Jangan tutup terminal yang running cloudflared!
        </div>
    </div>
</div>

@endsection