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

    /* Sidebar hover purple */
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

    /* Sidebar active purple */
    .sidenav .nav-link.active {
        background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
        color: white !important;
        box-shadow: 0 8px 18px rgba(109, 40, 217, 0.25);
    }

    .sidenav .nav-link.active i,
    .sidenav .nav-link.active span {
        color: white !important;
    }

    /* Section headers in sidebar */
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

    .navbar-main nav[aria-label="breadcrumb"] {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.2 !important;
    }

    .navbar-main .breadcrumb {
        margin-bottom: 4px !important;
        padding: 0 !important;
    }

    .navbar-main .breadcrumb-item {
        font-size: 14px !important;
        line-height: 1.2 !important;
    }

    .navbar-main h6 {
        font-size: 18px !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .navbar-main .collapse,
    .navbar-main .navbar-collapse {
        height: 78px !important;
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
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

    /* Main content adjustment */
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
  </style>

</head>

<body class="g-sidenav-show bg-gray-100">

  <!-- ============================================ -->
  <!-- SIDEBAR / NAVIGATION -->
  <!-- ============================================ -->
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">

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
        <!-- MAIN SECTION -->
        <!-- ============================================ -->
        <li class="nav-item">
          <span class="section-header">MAIN</span>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('dashboard')) active @endif" href="{{ url('/dashboard') }}">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>

        <!-- Little Blossoms (Children) -->
        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('children.*')) active @endif" href="{{ route('children.index') }}">
            <i class="material-symbols-rounded opacity-5">child_care</i>
            <span class="nav-link-text ms-1">Little Blossoms</span>
          </a>
        </li>

        <!-- ============================================ -->
<!-- TAMBAH: KIOSK -->
<!-- ============================================ -->
<li class="nav-item">
  <a class="nav-link text-dark @if(request()->routeIs('kiosk.index')) active @endif" href="{{ route('kiosk.index') }}" target="_blank">
    <i class="material-symbols-rounded opacity-5">qr_code_scanner</i>
    <span class="nav-link-text ms-1">Kiosk</span>
  </a>
</li>

        <!-- Loving Guardians (Parents) -->
        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('parents.*')) active @endif" href="{{ route('parents.index') }}">
            <i class="material-symbols-rounded opacity-5">family_restroom</i>
            <span class="nav-link-text ms-1">Loving Guardians</span>
          </a>
        </li>

        
        <!-- Nurturing Team (Teachers) -->
        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('teachers.*')) active @endif" href="{{ route('teachers.index') }}">
            <i class="material-symbols-rounded opacity-5">school</i>
            <span class="nav-link-text ms-1">Nurturing Team</span>
          </a>
        </li>

        <!-- Classrooms -->
        <li class="nav-item">
          <a class="nav-link text-dark @if(request()->routeIs('classrooms.*')) active @endif" href="{{ route('classrooms.index') }}">
            <i class="material-symbols-rounded opacity-5">meeting_room</i>
            <span class="nav-link-text ms-1">Classrooms</span>
          </a>
        </li>

        <!-- Attendance -->
<li class="nav-item">
    <a class="nav-link text-dark @if(request()->routeIs('attendance.index') || request()->routeIs('attendance.*') && !request()->routeIs('attendance.calendar*')) active @endif" href="{{ route('attendance.index') }}">
    <i class="material-symbols-rounded opacity-5">fact_check</i>
        <span class="nav-link-text ms-1">Attendance</span>
    </a>
</li>

<!-- Attendance Calendar -->
<li class="nav-item">
    <a class="nav-link text-dark @if(request()->routeIs('attendance.calendar*')) active @endif" href="{{ route('attendance.calendar') }}">
    <i class="material-symbols-rounded opacity-5">calendar_month</i>
        <span class="nav-link-text ms-1">Calendar</span>
    </a>
</li>

{{-- Dalam layouts/template.blade.php - tambah dalam OPERATIONS section --}}
<!-- QR Code -->
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

        {{-- Left side: Breadcrumb + title --}}
        <div>
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