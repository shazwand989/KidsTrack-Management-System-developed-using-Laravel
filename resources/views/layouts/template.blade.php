<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================
-->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('material/assets/img/favicon.png') }}">

  <title>@yield('title', 'Dashboard') - SAFECARE</title>

  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

  <!-- Nucleo Icons -->
  <link href="{{ asset('material/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('material/assets/css/nucleo-svg.css') }}" rel="stylesheet" />

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('material/assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />

  <style>
    /* =========================
       ROOT VARIABLES
    ========================== */
    :root {
        --sidebar-width: 270px;
        --sidebar-collapsed: 0px;
        --purple-500: #6d28d9;
        --purple-600: #5b21b6;
        --purple-400: #9333ea;
    }

    /* =========================
       SIDEBAR — DESKTOP (≥992px)
    ========================== */
    .sidenav {
        position: fixed !important;
        top: 18px !important;
        left: 10px !important;
        width: var(--sidebar-width) !important;
        height: calc(100vh - 36px) !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        z-index: 1040 !important;
        border-radius: 18px !important;
        box-shadow: 0 10px 30px rgba(91, 33, 182, 0.12) !important;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .sidenav::-webkit-scrollbar { width: 5px; }
    .sidenav::-webkit-scrollbar-track { background: transparent; }
    .sidenav::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 20px; }

    .sidenav .navbar-brand span {
        color: #2e1065 !important; font-weight: 800 !important;
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
        color: #374151 !important; transition: all 0.25s ease !important;
    }

    .sidenav .nav-link:hover {
        background: linear-gradient(135deg, var(--purple-500), var(--purple-400)) !important;
        color: white !important;
        transform: translateX(4px);
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .sidenav .nav-link:hover i, .sidenav .nav-link:hover span { color: white !important; }

    .sidenav .nav-link.active {
        background: linear-gradient(135deg, var(--purple-500), var(--purple-400)) !important;
        color: white !important;
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .sidenav .nav-link.active i, .sidenav .nav-link.active span { color: white !important; }

    .sidenav .section-header {
        font-size: 11px; font-weight: 700; letter-spacing: 0.5px;
        color: #94a3b8 !important; padding: 12px 24px 4px 24px;
        margin-top: 8px; display: block;
    }

    /* =========================
       SIDEBAR OVERLAY — MOBILE
    ========================== */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 1035;
        transition: opacity 0.35s ease;
    }

    /* =========================
       HAMBURGER TOGGLE BUTTON
    ========================== */
    .sidebar-toggle-btn {
        display: none !important;
        width: 42px; height: 42px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: white;
        color: #374151;
        font-size: 22px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-right: 12px;
    }
    .sidebar-toggle-btn:hover {
        background: #f3e8ff; border-color: var(--purple-400); color: var(--purple-500);
    }
    .sidebar-toggle-btn i { font-size: 20px; }

    /* =========================
       MOBILE SIDEBAR CLOSE "X"
    ========================== */
    .mobile-sidebar-close {
        display: none;
        position: absolute;
        top: 16px; right: 16px;
        width: 36px; height: 36px;
        border-radius: 50%;
        border: none;
        background: #f3e8ff;
        color: var(--purple-500);
        font-size: 20px;
        cursor: pointer;
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    /* =========================
       TOP NAVBAR
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
        flex-wrap: nowrap !important;
    }

    .navbar-main .breadcrumb { margin-bottom: 4px !important; padding: 0 !important; }
    .navbar-main .breadcrumb-item { font-size: 14px !important; line-height: 1.2 !important; }
    .navbar-main h6 { font-size: 18px !important; line-height: 1.2 !important; margin: 0 !important; padding: 0 !important; }

    /* User logout button */
    .user-logout-btn {
        border: none !important; background: #f3e8ff !important;
        color: #4c1d95 !important; font-weight: 800 !important;
        display: flex !important; align-items: center !important;
        gap: 4px; cursor: pointer !important;
        padding: 10px 14px !important; border-radius: 14px !important;
        transition: 0.25s ease; white-space: nowrap;
    }
    .user-logout-btn:hover {
        background: linear-gradient(135deg, var(--purple-500), var(--purple-400)) !important;
        color: white !important; box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }
    .user-logout-btn:hover i { color: white !important; }
    .user-logout-btn i { color: var(--purple-500) !important; }

    /* Header left section */
    .header-left { display: flex; align-items: center; min-width: 0; flex: 1; overflow: hidden; }
    .header-right { display: flex; align-items: center; flex-shrink: 0; }

    /* =========================
       LOGOUT MODAL (unchanged)
    ========================== */
    .custom-logout-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(17, 24, 39, 0.55); backdrop-filter: blur(6px);
        z-index: 9999; align-items: center; justify-content: center;
    }
    .custom-logout-overlay.show { display: flex; }

    .custom-logout-box {
        width: 420px; max-width: 92%; background: white;
        border-radius: 26px; padding: 28px; position: relative;
        box-shadow: 0 25px 60px rgba(76, 29, 149, 0.35);
        animation: logoutPop 0.25s ease;
    }

    @keyframes logoutPop {
        from { opacity: 0; transform: scale(0.92) translateY(15px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .logout-close-btn {
        position: absolute; top: 14px; right: 18px;
        border: none; background: #f3e8ff; color: var(--purple-500);
        width: 34px; height: 34px; border-radius: 50%;
        font-size: 22px; line-height: 1; cursor: pointer;
    }

    .logout-profile { text-align: center; padding-top: 8px; }
    .logout-avatar {
        width: 88px; height: 88px; border-radius: 50%;
        margin: 0 auto 14px;
        background: linear-gradient(135deg, var(--purple-500), #ec4899);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 12px 28px rgba(109, 40, 217, 0.28);
    }
    .logout-avatar i { color: white !important; font-size: 54px; }
    .logout-profile h4 { margin-bottom: 4px; color: #2e1065; font-weight: 900; }
    .logout-profile p { color: #6b7280; margin-bottom: 18px; font-size: 14px; }

    .logout-content {
        background: #f7f3ff; border-radius: 18px; padding: 18px;
        text-align: center; margin-bottom: 22px;
    }
    .logout-content h5 { color: #4c1d95; font-weight: 900; margin-bottom: 6px; }
    .logout-content p { color: #6b7280; margin-bottom: 0; font-size: 14px; }

    .logout-actions { display: flex; gap: 12px; justify-content: center; }
    .logout-actions form { margin: 0; }

    .btn-cancel-logout, .btn-confirm-logout {
        border: none; padding: 11px 22px; border-radius: 14px;
        font-weight: 800; cursor: pointer; transition: 0.25s ease;
    }
    .btn-cancel-logout { background: #f3f4f6; color: #374151; }
    .btn-cancel-logout:hover { background: #e5e7eb; }
    .btn-confirm-logout {
        background: linear-gradient(135deg, var(--purple-500), var(--purple-400));
        color: white; box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }
    .btn-confirm-logout:hover { opacity: 0.9; }

    /* =========================
       MAIN CONTENT
    ========================== */
    .main-content {
        margin-left: 290px !important;
        padding-right: 20px !important;
        transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* =========================
       RESPONSIVE BREAKPOINTS
    ========================== */

    /* Tablet (768px – 991px) */
    @media (max-width: 991.98px) {
        .sidebar-toggle-btn { display: flex !important; }

        .sidenav {
            transform: translateX(-110%) !important;
            left: 0 !important;
            top: 0 !important;
            height: 100vh !important;
            width: 280px !important;
            border-radius: 0 18px 18px 0 !important;
            z-index: 1050 !important;
            box-shadow: 0 0 60px rgba(0,0,0,0.25) !important;
        }

        .sidenav.mobile-open {
            transform: translateX(0) !important;
        }

        .sidebar-overlay.mobile-open {
            display: block !important;
        }

        .mobile-sidebar-close {
            display: flex !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding-right: 10px !important;
            padding-left: 10px !important;
        }

        .navbar-main {
            height: 68px !important;
            min-height: 68px !important;
            max-height: 68px !important;
            padding: 0 12px !important;
            top: 6px !important;
            margin-bottom: 12px !important;
            border-radius: 14px !important;
        }

        .navbar-main .container-fluid {
            height: 68px !important;
            min-height: 68px !important;
            max-height: 68px !important;
        }

        .navbar-main h6 { font-size: 15px !important; }
        .navbar-main .breadcrumb-item { font-size: 12px !important; }

        .user-logout-btn {
            padding: 8px 10px !important;
            font-size: 14px !important;
            border-radius: 10px !important;
        }
        .user-logout-btn span { display: none; }

        .container-fluid.py-4 { padding: 12px 8px !important; }
    }

    /* Mobile (< 576px) */
    @media (max-width: 575.98px) {
        .navbar-main {
            height: 60px !important;
            min-height: 60px !important;
            max-height: 60px !important;
            padding: 0 10px !important;
            border-radius: 12px !important;
            top: 4px !important;
            margin-bottom: 10px !important;
        }

        .navbar-main .container-fluid {
            height: 60px !important;
            min-height: 60px !important;
            max-height: 60px !important;
        }

        .navbar-main h6 { font-size: 14px !important; }

        .navbar-main nav[aria-label="breadcrumb"] .breadcrumb { display: none; }

        .sidebar-toggle-btn {
            width: 36px; height: 36px;
            border-radius: 10px; font-size: 18px;
            margin-right: 8px;
        }

        .sidenav {
            width: 280px !important;
            border-radius: 0 16px 16px 0 !important;
        }

        .sidenav.mobile-open {
            width: calc(100vw - 40px) !important;
            max-width: 300px !important;
        }

        .main-content {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        .container-fluid.py-4 { padding: 8px 4px !important; }

        .user-logout-btn {
            padding: 6px 8px !important;
            font-size: 13px !important;
        }

        /* Responsive tables */
        .table-responsive-stack {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive cards */
        .card { margin-bottom: 12px !important; }
        .card-body { padding: 16px !important; }

        /* Form elements full width */
        .form-control, .form-select, .btn {
            font-size: 15px !important;
        }

        .copyright { font-size: 12px !important; }
    }
  </style>

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

</head>

<body class="g-sidenav-show bg-gray-100">

  <!-- ============================================ -->
  <!-- SIDEBAR OVERLAY (mobile)                     -->
  <!-- ============================================ -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ============================================ -->
  <!-- SIDEBAR / NAVIGATION                         -->
  <!-- ============================================ -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">

    {{-- Mobile close button --}}
    <button class="mobile-sidebar-close" id="mobileSidebarClose" aria-label="Close menu">
      <i class="fas fa-times"></i>
    </button>

    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
         aria-hidden="true"
         id="iconSidenav"></i>

      <a class="navbar-brand px-4 py-3 m-0" href="{{ url('/dashboard') }}">
        <img src="{{ asset('material/assets/img/logo-ct-dark.png') }}"
             class="navbar-brand-img"
             width="26"
             height="26"
             alt="main_logo">
        <span class="ms-1 text-sm text-dark fw-bold">SAFECARE</span>
      </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
      <ul class="navbar-nav">

        <!-- ============================================ -->
        <!-- MAIN -->
        <!-- ============================================ -->
        <li class="nav-item">
          <span class="section-header">MAIN</span>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('dashboard')) active @endif" href="{{ url('/dashboard') }}">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('kiosk.index')) active @endif" href="{{ route('kiosk.index') }}" target="_blank">
            <i class="material-symbols-rounded opacity-5">qr_code_scanner</i>
            <span class="nav-link-text ms-1">Kiosk</span>
          </a>
        </li>

        <!-- ============================================ -->
        <!-- MANAGEMENT -->
        <!-- ============================================ -->
        <li class="nav-item mt-3">
          <span class="section-header">MANAGEMENT</span>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('children.*')) active @endif" href="{{ route('children.index') }}">
            <i class="material-symbols-rounded opacity-5">child_care</i>
            <span class="nav-link-text ms-1">Little Blossoms</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('parents.*')) active @endif" href="{{ route('parents.index') }}">
            <i class="material-symbols-rounded opacity-5">family_restroom</i>
            <span class="nav-link-text ms-1">Loving Guardians</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('teachers.*')) active @endif" href="{{ route('teachers.index') }}">
            <i class="material-symbols-rounded opacity-5">school</i>
            <span class="nav-link-text ms-1">Nurturing Team</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('classrooms.*')) active @endif" href="{{ route('classrooms.index') }}">
            <i class="material-symbols-rounded opacity-5">meeting_room</i>
            <span class="nav-link-text ms-1">Classrooms</span>
          </a>
        </li>

        <!-- ============================================ -->
        <!-- ATTENDANCE -->
        <!-- ============================================ -->
        <li class="nav-item mt-3">
          <span class="section-header">ATTENDANCE</span>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('attendance.index') || (request()->routeIs('attendance.*') && !request()->routeIs('attendance.calendar*'))) active @endif" href="{{ route('attendance.index') }}">
            <i class="material-symbols-rounded opacity-5">fact_check</i>
            <span class="nav-link-text ms-1">Attendance</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('attendance.calendar*')) active @endif" href="{{ route('attendance.calendar') }}">
            <i class="material-symbols-rounded opacity-5">calendar_month</i>
            <span class="nav-link-text ms-1">Calendar</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('reports.*')) active @endif" href="{{ route('reports.class') }}">
            <i class="material-symbols-rounded opacity-5">assessment</i>
            <span class="nav-link-text ms-1">Reports</span>
          </a>
        </li>

        <!-- ============================================ -->
        <!-- FINANCE -->
        <!-- ============================================ -->
        <li class="nav-item mt-3">
          <span class="section-header">FINANCE</span>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('penalties.fines')) active @endif" href="{{ route('penalties.fines') }}">
            <i class="material-symbols-rounded opacity-5">payments</i>
            <span class="nav-link-text ms-1">Fine</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('penalties.settings')) active @endif" href="{{ route('penalties.settings') }}">
            <i class="material-symbols-rounded opacity-5">settings</i>
            <span class="nav-link-text ms-1">Penalty Settings</span>
          </a>
        </li>

        <!-- ============================================ -->
        <!-- SYSTEM -->
        <!-- ============================================ -->
        <li class="nav-item mt-3">
          <span class="section-header">SYSTEM</span>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('audit.*')) active @endif" href="{{ route('audit.log') }}">
            <i class="material-symbols-rounded opacity-5">history</i>
            <span class="nav-link-text ms-1">Audit Trail</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('qr.code')) active @endif" href="{{ route('qr.code') }}">
            <i class="material-symbols-rounded opacity-5">qr_code_2</i>
            <span class="nav-link-text ms-1">QR Code</span>
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

        {{-- Hamburger toggle (mobile) --}}
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle navigation">
          <i class="fas fa-bars"></i>
        </button>

        {{-- Left side: Breadcrumb + title --}}
        <div class="header-left">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0">
              <li class="breadcrumb-item text-sm">
                <a class="opacity-5 text-dark" href="{{ url('/dashboard') }}">Home</a>
              </li>
              <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                @yield('page-title', 'Dashboard')
              </li>
            </ol>
            <h6 class="font-weight-bolder mb-0">
              @yield('page-title', 'Dashboard')
            </h6>
          </nav>
        </div>

        {{-- Right side: User + logout --}}
        <div class="header-right">
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
      @yield('content')

      <!-- Footer -->
      <footer class="footer py-4">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-12 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted">
                © {{ date('Y') }} SAFECARE. All rights reserved.
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

  <!-- Chart.js Initialization -->
  <script>
    if (document.getElementById("chart-bars")) {
      var ctx = document.getElementById("chart-bars").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: ["M", "T", "W", "T", "F", "S", "S"],
          datasets: [{
            label: "Views",
            tension: 0.4,
            borderWidth: 0,
            borderRadius: 4,
            borderSkipped: false,
            backgroundColor: "#43A047",
            data: [50, 45, 22, 28, 50, 60, 76],
            barThickness: 'flex'
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          interaction: { intersect: false, mode: 'index' },
        },
      });
    }

    if (document.getElementById("chart-line")) {
      var ctx2 = document.getElementById("chart-line").getContext("2d");
      new Chart(ctx2, {
        type: "line",
        data: {
          labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
          datasets: [{
            label: "Sales",
            tension: 0,
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: "#43A047",
            pointBorderColor: "transparent",
            borderColor: "#43A047",
            backgroundColor: "transparent",
            fill: true,
            data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220],
            maxBarThickness: 6
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          interaction: { intersect: false, mode: 'index' },
        },
      });
    }
  </script>

  <!-- Sidebar Toggle (Mobile) -->
  <script>
    (function() {
      const toggleBtn = document.getElementById('sidebarToggleBtn');
      const closeBtn = document.getElementById('mobileSidebarClose');
      const overlay = document.getElementById('sidebarOverlay');
      const sidenav = document.getElementById('sidenav-main');

      if (!toggleBtn || !sidenav || !overlay) return;

      function openSidebar() {
        sidenav.classList.add('mobile-open');
        overlay.classList.add('mobile-open');
        document.body.style.overflow = 'hidden';
      }

      function closeSidebar() {
        sidenav.classList.remove('mobile-open');
        overlay.classList.remove('mobile-open');
        document.body.style.overflow = '';
      }

      toggleBtn.addEventListener('click', openSidebar);
      if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
      overlay.addEventListener('click', closeSidebar);

      // Close on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidenav.classList.contains('mobile-open')) {
          closeSidebar();
        }
      });

      // Close on sidebar link click (mobile)
      sidenav.querySelectorAll('.nav-link').forEach(function(link) {
        link.addEventListener('click', function() {
          if (window.innerWidth < 992) {
            setTimeout(closeSidebar, 200);
          }
        });
      });

      // Handle resize: close sidebar if window grows past breakpoint
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 992 && sidenav.classList.contains('mobile-open')) {
          closeSidebar();
        }
      });
    })();
  </script>

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

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function confirmDelete(formOrBtn, message) {
      event.preventDefault();
      Swal.fire({
        title: 'Are you sure?',
        text: message || 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          if (formOrBtn.tagName === 'FORM') {
            formOrBtn.submit();
          } else {
            formOrBtn.closest('form').submit();
          }
        }
      });
      return false;
    }
  </script>

</body>

</html>
