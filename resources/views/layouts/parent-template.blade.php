<!--
=========================================================
* Material Dashboard 3 - v3.2.0
* Modified for Parent Dashboard
=========================================================
-->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('material/assets/img/favicon.png') }}">

  <title>Parent Dashboard - KidsTrack</title>

  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

  <!-- Nucleo Icons -->
  <link href="{{ asset('material/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('material/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('material/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />
  
  <style>
    /* =========================
       FIX SIDEBAR POSITION
    ========================== */
    .sidenav {
        position: fixed !important;
        top: 18px !important;
        left: 10px !important;
        height: calc(100vh - 36px) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        z-index: 1040 !important;
        border-radius: 18px !important;
        box-shadow: 0 10px 30px rgba(91, 33, 182, 0.12) !important;
    }

    .sidenav::-webkit-scrollbar {
        width: 5px;
    }

    .sidenav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidenav::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 20px;
    }

    .sidenav .navbar-brand span {
        color: #2e1065 !important;
        font-weight: 800 !important;
    }

    .sidenav .nav-link {
        margin: 4px 12px !important;
        border-radius: 12px !important;
        color: #374151 !important;
        font-weight: 600 !important;
        transition: all 0.25s ease !important;
    }

    .sidenav .nav-link i,
    .sidenav .nav-link span {
        color: #374151 !important;
        transition: all 0.25s ease !important;
    }

    .sidenav .nav-link:hover {
        background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
        color: white !important;
        transform: translateX(4px);
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .sidenav .nav-link:hover i,
    .sidenav .nav-link:hover span {
        color: white !important;
    }

    .sidenav .nav-link.active {
        background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
        color: white !important;
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .sidenav .nav-link.active i,
    .sidenav .nav-link.active span {
        color: white !important;
    }

    .sidenav .section-header {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #94a3b8 !important;
        padding: 12px 24px 4px 24px;
        margin-top: 8px;
        display: block;
    }

    /* =========================
       FIX TOP BAR STICKY
    ========================== */
    .navbar-main {
        height: 78px !important;
        min-height: 78px !important;
        max-height: 78px !important;
        padding: 0 18px !important;
        margin-bottom: 18px !important;
        overflow: hidden !important;
        position: sticky !important;
        top: 10px !important;
        z-index: 1030 !important;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px);
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(91, 33, 182, 0.08) !important;
        display: flex !important;
        align-items: center !important;
    }

    .navbar-main .container-fluid {
        height: 78px !important;
        min-height: 78px !important;
        max-height: 78px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    /* User logout button */
    .user-logout-btn {
        border: none !important;
        background: #f3e8ff !important;
        color: #4c1d95 !important;
        font-weight: 800 !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px;
        cursor: pointer !important;
        padding: 10px 14px !important;
        border-radius: 14px !important;
        transition: 0.25s ease;
    }

    .user-logout-btn:hover {
        background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
        color: white !important;
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .user-logout-btn:hover i {
        color: white !important;
    }

    .user-logout-btn i {
        color: #6d28d9 !important;
    }

    /* Logout Modal */
    .custom-logout-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.55);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .custom-logout-overlay.show {
        display: flex;
    }

    .custom-logout-box {
        width: 420px;
        max-width: 92%;
        background: white;
        border-radius: 26px;
        padding: 28px;
        position: relative;
        box-shadow: 0 25px 60px rgba(76, 29, 149, 0.35);
        animation: logoutPop 0.25s ease;
    }

    @keyframes logoutPop {
        from {
            opacity: 0;
            transform: scale(0.92) translateY(15px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .logout-close-btn {
        position: absolute;
        top: 14px;
        right: 18px;
        border: none;
        background: #f3e8ff;
        color: #6d28d9;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
    }

    .logout-profile {
        text-align: center;
        padding-top: 8px;
    }

    .logout-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        margin: 0 auto 14px;
        background: linear-gradient(135deg, #6d28d9, #ec4899);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 28px rgba(109, 40, 217, 0.28);
    }

    .logout-avatar i {
        color: white !important;
        font-size: 54px;
    }

    .logout-profile h4 {
        margin-bottom: 4px;
        color: #2e1065;
        font-weight: 900;
    }

    .logout-profile p {
        color: #6b7280;
        margin-bottom: 18px;
        font-size: 14px;
    }

    .logout-content {
        background: #f7f3ff;
        border-radius: 18px;
        padding: 18px;
        text-align: center;
        margin-bottom: 22px;
    }

    .logout-content h5 {
        color: #4c1d95;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .logout-content p {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }

    .logout-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .logout-actions form {
        margin: 0;
    }

    .btn-cancel-logout,
    .btn-confirm-logout {
        border: none;
        padding: 11px 22px;
        border-radius: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.25s ease;
    }

    .btn-cancel-logout {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-cancel-logout:hover {
        background: #e5e7eb;
    }

    .btn-confirm-logout {
        background: linear-gradient(135deg, #6d28d9, #9333ea);
        color: white;
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .btn-confirm-logout:hover {
        opacity: 0.9;
    }

    /* =========================
       PARENT DASHBOARD CUSTOM STYLES
    ========================== */
    .card {
        border-radius: 18px !important;
        border: none !important;
        box-shadow: 0 8px 25px rgba(91, 33, 182, 0.08) !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease !important;
    }

    .card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 16px 40px rgba(91, 33, 182, 0.12) !important;
    }

    .card .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .card .card-icon.purple {
        background: #f3e8ff;
        color: #6d28d9;
    }

    .card .card-icon.blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .card .card-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .card .card-icon.orange {
        background: #fef3c7;
        color: #d97706;
    }

    .card .card-number {
        font-size: 32px;
        font-weight: 900;
        color: #2e1065;
        margin: 4px 0 2px 0;
    }

    .card .card-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 600;
    }

    /* Children list styling */
    .child-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6d28d9, #9333ea);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }

    .child-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .status-badge {
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge.present {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-badge.absent {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-badge.late {
        background: #fef3c7;
        color: #d97706;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }

    .empty-state .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state h5 {
        color: #4b5563;
        margin-bottom: 4px;
    }

    .main-content {
        margin-left: 270px !important;
        padding-right: 20px !important;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0 !important;
            padding-right: 10px !important;
        }
    }

    .verified-badge {
        color: #16a34a;
        font-size: 14px;
    }

    .unverified-badge {
        color: #dc2626;
        font-size: 14px;
    }

    .parent-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .parent-photo-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6d28d9, #9333ea);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 800;
        flex-shrink: 0;
    }
  </style>

</head>

<body class="g-sidenav-show bg-gray-100">

  <!-- ============================================ -->
  <!-- SIDEBAR / NAVIGATION - PARENT VERSION -->
  <!-- ============================================ -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">

    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
         aria-hidden="true"
         id="iconSidenav"></i>

      <a class="navbar-brand px-4 py-3 m-0" href="{{ route('parent.dashboard') }}">
        <img src="{{ asset('material/assets/img/logo-ct-dark.png') }}"
             class="navbar-brand-img"
             width="26"
             height="26"
             alt="main_logo">
        <span class="ms-1 text-sm text-dark fw-bold">🧸 KidsTrack</span>
      </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">

        <!-- MAIN SECTION -->
        <li class="nav-item">
          <span class="section-header">MAIN</span>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link text-dark active" href="{{ route('parent.dashboard') }}">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <!-- My Children -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.children') }}">
            <i class="material-symbols-rounded opacity-5">child_care</i>
            <span class="nav-link-text ms-1">My Children</span>
          </a>
        </li>

        <!-- ATTENDANCE SECTION -->
        <li class="nav-item mt-3">
          <span class="section-header">ATTENDANCE</span>
        </li>

        <!-- Attendance -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.attendance') }}">
            <i class="material-symbols-rounded opacity-5">event_note</i>
            <span class="nav-link-text ms-1">Attendance</span>
          </a>
        </li>

        <!-- Attendance Calendar -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.attendance.calendar') }}">
            <i class="material-symbols-rounded opacity-5">calendar_month</i>
            <span class="nav-link-text ms-1">Calendar</span>
          </a>
        </li>

<li class="nav-item">
  <a class="nav-link text-dark @if(request()->routeIs('kiosk.index')) active @endif" href="{{ route('kiosk.index') }}" target="_blank">
    <i class="material-symbols-rounded opacity-5">qr_code_scanner</i>
    <span class="nav-link-text ms-1">📱 Kiosk</span>
  </a>
</li>

        <!-- ACCOUNT SECTION -->
        <li class="nav-item mt-3">
          <span class="section-header">ACCOUNT</span>
        </li>

        <!-- Notifications -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.notifications') }}">
            <i class="material-symbols-rounded opacity-5">notifications</i>
            <span class="nav-link-text ms-1">Notifications</span>
          </a>
        </li>

        <!-- Payment -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.payment') }}">
            <i class="material-symbols-rounded opacity-5">payments</i>
            <span class="nav-link-text ms-1">Payment</span>
          </a>
        </li>

        <!-- Fine -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.fine') }}">
            <i class="material-symbols-rounded opacity-5">warning</i>
            <span class="nav-link-text ms-1">Fine</span>
          </a>
        </li>

        <!-- Profile -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.profile') }}">
            <i class="material-symbols-rounded opacity-5">account_circle</i>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>

        <!-- Settings -->
        <li class="nav-item">
          <a class="nav-link text-dark" href="{{ route('parent.settings') }}">
            <i class="material-symbols-rounded opacity-5">settings</i>
            <span class="nav-link-text ms-1">Settings</span>
          </a>
        </li>

      </ul>
    </div>
  </aside>

  <!-- ============================================ -->
  <!-- MAIN CONTENT -->
  <!-- ============================================ -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

    <!-- Top Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl">
      <div class="container-fluid py-1 px-3 d-flex align-items-center justify-content-between">

        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0">
              <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ route('parent.dashboard') }}">Home</a>
              </li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                Parent Dashboard
              </li>
            </ol>
            <h6 class="font-weight-bolder mb-0">
              Parent Dashboard
            </h6>
          </nav>
        </div>

        <div class="d-flex align-items-center ms-auto">
          @auth
          <button type="button" class="user-logout-btn" id="openLogoutBtn">
            <i class="material-symbols-rounded me-1">account_circle</i>
            <span>{{ Auth::user()->name }}</span>
            <i class="material-symbols-rounded ms-1" style="font-size:18px;">logout</i>
          </button>
          @endauth
        </div>

      </div>
    </nav>

    <!-- Page Content -->
    <div class="container-fluid py-4">

      <!-- Welcome Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card p-4">
            <div class="d-flex align-items-center">
              @if($parent->photo)
                <img src="{{ Storage::url($parent->photo) }}" class="parent-photo me-3" alt="{{ $parent->name }}">
              @else
                <div class="parent-photo-placeholder me-3">
                  {{ $parent->initial }}
                </div>
              @endif
              <div>
                <h3 class="mb-0 fw-bold" style="color: #2e1065;">
                  Welcome back, {{ $parent->name }}! 🎉
                  @if($parent->verified)
                    <span class="verified-badge">✅ Verified</span>
                  @else
                    <span class="unverified-badge">⏳ Pending Verification</span>
                  @endif
                </h3>
                <p class="mb-0 text-muted">
                  📞 {{ $parent->phone }}
                  @if($parent->emergency)
                    <span class="badge bg-danger ms-2">🚨 Emergency Contact</span>
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row mb-4">

        <!-- Children -->
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="card p-3">
            <div class="card-icon purple">
              <i class="material-symbols-rounded">child_care</i>
            </div>
            <div class="card-number">{{ $totalChildren }}</div>
            <div class="card-label">👶 Children</div>
          </div>
        </div>

        <!-- Attendance Today -->
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="card p-3">
            <div class="card-icon blue">
              <i class="material-symbols-rounded">event_note</i>
            </div>
            <div class="card-number">{{ $todayAttendance->where('status', 'present')->count() }}</div>
            <div class="card-label">📅 Present Today</div>
          </div>
        </div>

        <!-- Invoice -->
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="card p-3">
            <div class="card-icon green">
              <i class="material-symbols-rounded">payments</i>
            </div>
            <div class="card-number">RM{{ number_format($totalInvoices, 2) }}</div>
            <div class="card-label">💳 Total Due</div>
          </div>
        </div>

        <!-- Notification -->
        <div class="col-xl-3 col-md-6 mb-3">
          <div class="card p-3">
            <div class="card-icon orange">
              <i class="material-symbols-rounded">notifications</i>
            </div>
            <div class="card-number">{{ $unreadNotifications }}</div>
            <div class="card-label">🔔 Unread</div>
          </div>
        </div>

      </div>

      <!-- My Children Section -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold" style="color: #2e1065;">
                <i class="material-symbols-rounded me-2" style="font-size: 24px; vertical-align: middle;">child_care</i>
                My Children
              </h5>
              <a href="{{ route('parent.children') }}" class="btn btn-sm" style="background: linear-gradient(135deg, #6d28d9, #9333ea); color: white; border: none; border-radius: 10px; padding: 6px 16px; font-weight: 600;">
                View All
              </a>
            </div>
            <div class="card-body p-4">

              @if($children->count() > 0)

                @foreach($children as $child)
                  <div class="d-flex align-items-center py-3 border-bottom border-light">
                    <div class="child-avatar me-3">
                      {{ strtoupper(substr($child->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-bold" style="color: #2e1065;">{{ $child->name }}</h6>
                      <small class="text-muted">
                        @if($child->classroom)
                          Class: {{ $child->classroom->name }}
                        @else
                          No class assigned
                        @endif
                      </small>
                    </div>
                    <div>
                      @php
                        $todayStatus = $todayAttendance->where('child_id', $child->id)->first();
                      @endphp
                      @if($todayStatus)
                        <span class="status-badge {{ $todayStatus->status }}">
                          {{ ucfirst($todayStatus->status) }}
                        </span>
                      @else
                        <span class="status-badge pending">Pending</span>
                      @endif
                    </div>
                  </div>
                @endforeach

              @else
                <div class="empty-state">
                  <div class="empty-icon">👶</div>
                  <h5>No Children Registered</h5>
                  <p class="text-muted">You haven't added any children to your account yet.</p>
                  <a href="#" class="btn mt-2" style="background: linear-gradient(135deg, #6d28d9, #9333ea); border: none; border-radius: 12px; padding: 10px 24px; font-weight: 700; color: white;">
                    <i class="material-symbols-rounded me-1" style="font-size: 18px; vertical-align: middle;">add</i>
                    Add Child
                  </a>
                </div>
              @endif

            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer py-4 mt-4">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-12 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted">
                © {{ date('Y') }} 🧸 KidsTrack. All rights reserved.
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div>

  </main>

  <!-- ============================================ -->
  <!-- SCRIPTS -->
  <!-- ============================================ -->

  <!-- Core JS Files -->
  <script src="{{ asset('material/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('material/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('material/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('material/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('material/assets/js/plugins/chartjs.min.js') }}"></script>

  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' };
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Control Center for Material Dashboard -->
  <script src="{{ asset('material/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>

  <!-- Logout Modal -->
  @auth
  <div id="customLogoutModal" class="custom-logout-overlay">
    <div class="custom-logout-box">
      <button type="button" class="logout-close-btn" id="closeLogoutBtn">&times;</button>

      <div class="logout-profile">
        <div class="logout-avatar">
          <i class="material-symbols-rounded">account_circle</i>
        </div>
        <h4>{{ Auth::user()->name }}</h4>
        <p>{{ Auth::user()->email }}</p>
      </div>

      <div class="logout-content">
        <h5>Logout Confirmation</h5>
        <p>Are you sure you want to logout from the system?</p>
      </div>

      <div class="logout-actions">
        <button type="button" class="btn-cancel-logout" id="cancelLogoutBtn">Cancel</button>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn-confirm-logout">Yes, Logout</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const openBtn = document.getElementById('openLogoutBtn');
      const modal = document.getElementById('customLogoutModal');
      const closeBtn = document.getElementById('closeLogoutBtn');
      const cancelBtn = document.getElementById('cancelLogoutBtn');

      if (openBtn && modal) {
        openBtn.addEventListener('click', function () { modal.style.display = 'flex'; });
      }
      if (closeBtn && modal) {
        closeBtn.addEventListener('click', function () { modal.style.display = 'none'; });
      }
      if (cancelBtn && modal) {
        cancelBtn.addEventListener('click', function () { modal.style.display = 'none'; });
      }
      if (modal) {
        modal.addEventListener('click', function (event) {
          if (event.target === modal) modal.style.display = 'none';
        });
      }
    });
  </script>
  @endauth

</body>

</html>