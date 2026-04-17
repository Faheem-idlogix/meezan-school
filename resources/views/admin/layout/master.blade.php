<!DOCTYPE html>
<html lang="en" data-skin="{{ auth()->check() && auth()->user()->skin ? auth()->user()->skin : config('skins.default', 'cyber') }}">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', setting('school_name', 'School Management System'))</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  {{-- Restore skin from localStorage before any paint to prevent flash --}}
  <script>
    (function(){
      var s = localStorage.getItem('meezan_skin');
      if(s) document.documentElement.setAttribute('data-skin', s);
    })();
  </script>

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{asset("WSTheme/WsImg/WSFavicon.png")}}" />
  <link href="{{asset("resources/css/app.css")}}" rel="stylesheet">
  <!-- <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon"> -->

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset("assets/vendor/bootstrap/css/bootstrap.min.css")}}" rel="stylesheet">
  <link href="{{asset("assets/vendor/bootstrap-icons/bootstrap-icons.css")}}"  rel="stylesheet">
  <link href="{{asset("assets/vendor/boxicons/css/boxicons.min.css")}}"  rel="stylesheet">
  <link href="{{asset("assets/vendor/quill/quill.snow.css")}}"  rel="stylesheet">
  <link href="{{asset("assets/vendor/quill/quill.bubble.css")}}"  rel="stylesheet">
  <link href="{{asset("assets/vendor/remixicon/remixicon.css")}}"  rel="stylesheet">
  <link href="{{asset("assets/vendor/simple-datatables/style.css")}}"  rel="stylesheet">
  <script src="{{asset("assets/vendor/jquery/jquery-3.7.1.min.js")}}"></script>
  <link href="{{asset("assets/vendor/select2/dist/css/select2.min.css")}}" rel="stylesheet" />
   <script src="{{asset("assets/vendor/select2/dist/js/select2.min.js")}}"></script>



  <!-- Template Main CSS File -->
  <link href="{{asset("assets/css/style.css")}}"  rel="stylesheet">
  <!-- Skin System CSS -->
  <link href="{{asset("css/skins.css")}}"  rel="stylesheet">
  <style>
    /* ============================================================
       EliteAdmin Skin — Meezan School System
       Primary: #4154f1  |  Sidebar: #012970  |  Font: Poppins
    ============================================================ */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
      --ea-primary:       #4154f1;
      --ea-primary-rgb:   65,84,241;
      --ea-primary-light: #717ff5;
      --ea-sidebar-bg:    #012970;
      --ea-sidebar-width: 300px;
      --ea-body-bg:       #f6f9ff;
      --ea-card-bg:       #fff;
      --ea-shadow:        0 2px 15px rgba(1,41,112,.08);
      --ea-radius:        5px;
    }

    /* ── Base ── */
    body { font-family: 'Poppins', 'Nunito', sans-serif; background: var(--ea-body-bg); color: #444; }

    /* ── Header ── */
    #header {
      background: #fff;
      box-shadow: 0 2px 20px rgba(1,41,112,.12);
      border-bottom: 3px solid var(--ea-primary);
    }
    #header .logo span {
      font-size: 1.1rem; font-weight: 700;
      color: var(--ea-primary); letter-spacing: -.5px;
    }
    /* All header nav links: dark text */
    .header-nav .nav-link,
    .header-nav .nav-link i,
    .header-nav .nav-icon,
    .header-nav .nav-profile span,
    .toggle-sidebar-btn {
      color: #012970 !important;
      font-weight: 500;
    }
    .header-nav .nav-link:hover,
    .header-nav .nav-icon:hover { color: var(--ea-primary) !important; }
    /* Search bar */
    .search-bar .search-form input {
      color: #444;
      background: #f6f9ff;
      border: 1px solid #e0e7ff;
      border-radius: 20px;
      font-size: .86rem;
    }
    .search-bar .search-form input:focus { outline: none; border-color: var(--ea-primary); }
    .search-bar .search-form button { color: var(--ea-primary); }
    /* Live search dropdown */
    .search-bar { position: relative; }
    .live-search-results {
      position: absolute; top: 100%; left: 0; right: 0;
      background: #fff; border: 1px solid #e0e7ff; border-radius: 10px;
      box-shadow: 0 6px 24px rgba(0,0,0,.12); max-height: 380px; overflow-y: auto;
      z-index: 9999; display: none;
    }
    .live-search-results .search-cat { font-size:.7rem; font-weight:700; color:#888; text-transform:uppercase; padding:8px 14px 4px; }
    .live-search-results a.search-item {
      display:flex; align-items:center; gap:10px; padding:7px 14px; color:#333;
      text-decoration:none; font-size:.85rem; transition:background .15s;
    }
    .live-search-results a.search-item:hover, .live-search-results a.search-item.active { background:#f0f4ff; }
    .live-search-results a.search-item i { font-size:1.1rem; width:22px; text-align:center; }
    .live-search-results .search-item-title { font-weight:500; }
    .live-search-results .search-item-sub  { font-size:.75rem; color:#888; }
    /* Profile avatar circle */
    .nav-profile .nav-profile-img {
      width: 34px; height: 34px;
      border-radius: 50%;
      border: 2px solid #e0e7ff;
    }
    .nav-profile .profile-initials {
      width: 34px; height: 34px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--ea-primary), var(--ea-primary-light));
      color: #fff;
      font-size: .78rem; font-weight: 700;
      display: inline-flex; align-items: center; justify-content: center;
      border: 2px solid #e0e7ff;
      flex-shrink: 0;
    }
    /* Dropdown menu */
    .dropdown-menu { border: none; box-shadow: 0 5px 30px rgba(1,41,112,.12); border-radius: 8px; }
    .dropdown-item { font-size: .84rem; color: #444; padding: .55rem 1.25rem; }
    .dropdown-item:hover { background: #f6f9ff; color: var(--ea-primary); }
    .dropdown-header h6 { font-size: .9rem; font-weight: 700; color: #012970; margin: 0; }

    /* ── Sidebar ── */
    #sidebar { background: var(--ea-sidebar-bg); }
    /* Override NiceAdmin's white background on collapsed sidebar links */
    .sidebar-nav .nav-link,
    .sidebar-nav .nav-link.collapsed {
      color: rgba(255,255,255,.75) !important;
      background: transparent !important;
      border-radius: 0;
      padding: 10px 15px;
      font-size: .875rem;
      font-weight: 500;
      transition: all .2s;
    }
    .sidebar-nav .nav-link i,
    .sidebar-nav .nav-link.collapsed i { color: rgba(255,255,255,.6) !important; }
    .sidebar-nav .nav-link:hover,
    .sidebar-nav .nav-link.collapsed:hover { background: rgba(255,255,255,.1) !important; color: #fff !important; }
    .sidebar-nav .nav-link:hover i,
    .sidebar-nav .nav-link.collapsed:hover i { color: #fff !important; }
    .sidebar-nav .nav-link:not(.collapsed),
    .sidebar-nav .nav-link.active { background: rgba(255,255,255,.15) !important; color: #fff !important; }
    .sidebar-nav .nav-link i,
    .sidebar-nav .nav-content a i { color: inherit !important; }
    .sidebar-nav .nav-content a {
      color: rgba(255,255,255,.65) !important;
      font-size: .83rem;
      padding: 6px 15px 6px 38px;
      display: block;
      transition: .2s;
    }
    .sidebar-nav .nav-content a:hover,
    .sidebar-nav .nav-content a.active { color: #fff !important; }
    .sidebar-nav .nav-content a i { font-size: 6px; }
    li.nav-heading {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255,255,255,.35) !important;
      padding: 18px 15px 6px;
    }

    /* ── Cards ── */
    .card { border: none; border-radius: var(--ea-radius); box-shadow: var(--ea-shadow); }
    .card-title { font-size: .95rem; font-weight: 600; color: #012970; }
    .card-header { background: #fff; border-bottom: 1px solid #f0f4ff; padding: 1rem 1.25rem; }
    .card-header .card-title { margin-bottom: 0; }

    /* ── Gradient Stat Cards ── */
    .ea-stat { color: #fff; border-radius: var(--ea-radius); position: relative; overflow: hidden; padding: 1.4rem 1.5rem; box-shadow: var(--ea-shadow); height: 100%; min-height: 130px; display: flex; flex-direction: column; justify-content: center; }
    .ea-stat.blue   { background: linear-gradient(135deg, #4154f1 0%, #717ff5 100%); }
    .ea-stat.green  { background: linear-gradient(135deg, #198754 0%, #22c55e 100%); }
    .ea-stat.orange { background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); }
    .ea-stat.teal   { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); }
    .ea-stat.purple { background: linear-gradient(135deg, #6f42c1 0%, #a78bfa 100%); }
    .ea-stat .ea-stat-icon { position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); font-size: 3.5rem; opacity: .18; }
    .ea-stat .ea-stat-label { font-size: .7rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; opacity: .9; margin-bottom: .35rem; color: #fff; }
    .ea-stat .ea-stat-value { font-size: 1.9rem; font-weight: 700; line-height: 1.1; color: #fff; }
    .ea-stat .ea-stat-sub { font-size: .75rem; opacity: .85; margin-top: .25rem; color: #fff; }
    .ea-stat .ea-stat-badge { display: inline-block; font-size: .72rem; background: rgba(255,255,255,.25); border-radius: 20px; padding: 1px 8px; }
    .ea-stat .ea-fee-row { display: flex; justify-content: space-between; align-items: baseline; gap: .5rem; margin-top: .15rem; }
    .ea-stat .ea-fee-row .ea-fee-item { text-align: center; }
    .ea-stat .ea-fee-row .ea-fee-amt { font-size: 1.15rem; font-weight: 800; color: #fff; line-height: 1.2; }
    .ea-stat .ea-fee-row .ea-fee-lbl { font-size: .6rem; text-transform: uppercase; letter-spacing: .5px; opacity: .8; color: #fff; }

    /* ── Old info-card compat ── */
    .info-card .card-icon { width: 52px; height: 52px; background: rgba(var(--ea-primary-rgb),.12); color: var(--ea-primary); font-size: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .info-card.revenue-card .card-icon  { background: rgba(25,135,84,.12); color: #198754; }
    .info-card.customers-card .card-icon{ background: rgba(13,202,240,.12); color: #0dcaf0; }
    .info-card.sales-card .card-icon    { background: rgba(255,193,7,.15);  color: #fd7e14; }

    /* ── Page Title ── */
    .pagetitle h1 { font-size: 1.3rem; font-weight: 700; color: #012970; margin-bottom: 2px; }
    .breadcrumb { font-size: .78rem; }
    .breadcrumb-item a { color: var(--ea-primary); text-decoration: none; }
    .breadcrumb-item.active { color: #6c757d; }
    .breadcrumb-item+.breadcrumb-item::before { color: #adb5bd; }

    /* ── Tables ── */
    .table thead th {
      background: #f6f9ff;
      font-size: .75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .5px;
      color: #012970;
      border-bottom: 2px solid #e9ecef;
      padding: .8rem .75rem;
    }
    .table tbody td { font-size: .86rem; vertical-align: middle; border-color: #f0f4ff; padding: .7rem .75rem; }
    .table-hover tbody tr:hover { background-color: #f6f9ff; }

    /* ── Nav Tabs ── */
    .nav-tabs { border-bottom: 2px solid #e9ecef; gap: .25rem; }
    .nav-tabs .nav-link {
      border: none;
      border-bottom: 2px solid transparent;
      border-radius: 0;
      color: #6c757d;
      font-size: .875rem;
      font-weight: 500;
      padding: .6rem 1.25rem;
      margin-bottom: -2px;
      transition: color .15s, border-color .15s;
    }
    .nav-tabs .nav-link:hover { color: var(--ea-primary); border-bottom-color: rgba(var(--ea-primary-rgb),.4); background: none; }
    .nav-tabs .nav-link.active { color: var(--ea-primary); border-bottom-color: var(--ea-primary); background: transparent; font-weight: 600; }

    /* ── Buttons ── */
    .btn { border-radius: var(--ea-radius); font-size: .85rem; font-weight: 500; }
    .btn-primary { background: var(--ea-primary); border-color: var(--ea-primary); }
    .btn-primary:hover, .btn-primary:focus { background: #3345ef; border-color: #3345ef; box-shadow: 0 4px 12px rgba(var(--ea-primary-rgb),.35); }
    .btn-outline-primary { color: var(--ea-primary); border-color: var(--ea-primary); }
    .btn-outline-primary:hover { background: var(--ea-primary); border-color: var(--ea-primary); }
    .btn-sm { padding: .3rem .65rem; font-size: .78rem; }

    /* ── Forms ── */
    .form-control, .form-select {
      border-radius: var(--ea-radius);
      border: 1px solid #dee2e6;
      font-size: .875rem;
      padding: .5rem .85rem;
      font-family: 'Poppins', sans-serif;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--ea-primary);
      box-shadow: 0 0 0 .2rem rgba(var(--ea-primary-rgb),.15);
    }
    .form-label { font-size: .85rem; font-weight: 500; color: #212529; margin-bottom: .35rem; }
    .col-form-label { font-size: .85rem; font-weight: 500; color: #212529; }
    .invalid-feedback { font-size: .8rem; }
    .form-section-title {
      font-size: .8rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: .8px; color: var(--ea-primary);
      border-bottom: 2px solid #f0f4ff;
      padding-bottom: .5rem; margin-bottom: 1.25rem;
    }

    /* ── Badges ── */
    .badge { font-size: .7rem; font-weight: 600; border-radius: 4px; padding: .3em .6em; }
    .bg-purple { background-color: #6f42c1 !important; }

    /* ── Alert ── */
    .alert { border-radius: var(--ea-radius); font-size: .86rem; }

    /* ── Footer ── */
    #footer { font-size: .8rem; color: #6c757d; }

    /* ── Toastr ── */
    #toast-container { top: 70px !important; }
    .toast-top-right { top: 70px !important; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #c5cde8; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--ea-primary); }

    /* ── Select2 override ── */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
      border: 1px solid #dee2e6; border-radius: var(--ea-radius); height: 38px; display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 38px; padding-left: 12px; color: #444;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 38px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
      border-color: var(--ea-primary); box-shadow: 0 0 0 .2rem rgba(var(--ea-primary-rgb),.15);
    }
    .select2-dropdown { border-color: #dee2e6; border-radius: var(--ea-radius); z-index: 9999; }
    .select2-results__option--highlighted[aria-selected] { background: var(--ea-primary) !important; }
    .select2-search--dropdown .select2-search__field { border: 1px solid #dee2e6; border-radius: var(--ea-radius); padding: 6px 10px; }

    /* ── Card-header title with left accent bar ── */
    .card-header {
      border-left: 4px solid var(--ea-primary);
    }

    /* ── Status badge pills ── */
    .badge-paid    { background: rgba(25,135,84,.12);  color: #198754; border: 1px solid rgba(25,135,84,.3); }
    .badge-unpaid  { background: rgba(220,53,69,.1);   color: #dc3545; border: 1px solid rgba(220,53,69,.3); }
    .badge-pending { background: rgba(255,193,7,.15);  color: #b45309; border: 1px solid rgba(255,193,7,.5); }
  </style>
  @yield("css")
  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Jun 2025 with Bootstrap v5.3.8
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ route('home') }}" class="logo d-flex align-items-center">
        <img src="{{ school_logo() }}" alt="">
         <span class="d-none d-lg-block">{{ setting('school_name', 'School') }}</span> 
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="GET" action="{{ route('global.search') }}">
        <input type="text" name="q" id="globalSearchInput" value="{{ request('q') }}" placeholder="Search students, vouchers, invoices..." title="Enter search keyword" autocomplete="off">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
      <div class="live-search-results" id="liveSearchResults"></div>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        {{-- ═══════ Notification Bell ═══════ --}}
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" id="notificationBell">
            <i class="bi bi-bell"></i>
            <span class="badge bg-danger badge-number" id="notifCount" style="display:none;">0</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="min-width:340px;">
            <li class="dropdown-header d-flex justify-content-between align-items-center">
              <span>Notifications <span class="badge rounded-pill bg-primary ms-1" id="notifCountHeader">0</span></span>
              <div>
                <a href="#" id="markAllReadBtn" class="text-primary small me-2"><i class="bi bi-check-all"></i> Read All</a>
                <a href="{{ route('notifications.my') }}" class="badge rounded-pill bg-primary p-2">View All</a>
              </div>
            </li>
            <li><hr class="dropdown-divider"></li>

            <div id="notifDropdownList">
              <li class="notification-item text-center py-3">
                <small class="text-muted">Loading...</small>
              </li>
            </div>

            <li><hr class="dropdown-divider"></li>
            <li class="dropdown-footer text-center">
              <a href="{{ route('notifications.my') }}">View all notifications</a>
            </li>
          </ul>
        </li>{{-- End Notification Bell --}}

        {{-- ═══════ User Profile Dropdown ═══════ --}}
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            @php
              $initials = collect(explode(' ', Auth::user()->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
              $avatarColors = ['#4154f1','#2eca6a','#ff771d','#e74c3c','#9b59b6','#1abc9c','#f39c12','#3498db'];
              $avatarBg = $avatarColors[Auth::user()->id % count($avatarColors)];
            @endphp
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:34px;height:34px;font-size:13px;background:{{ $avatarBg }};">
              {{ $initials }}
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="min-width:220px;">
            <li class="dropdown-header">
              <h6>{{ Auth::user()->name }}</h6>
              <span class="text-muted small">
                @if(Auth::user()->roles->count())
                  {{ Auth::user()->roles->first()->display_name }}
                @else
                  {{ ucfirst(Auth::user()->role ?? 'User') }}
                @endif
              </span>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.index') }}">
                <i class="bi bi-person me-2"></i> My Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.change-password') }}">
                <i class="bi bi-key me-2"></i> Change Password
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('notifications.my') }}">
                <i class="bi bi-bell me-2"></i> My Notifications
                <span class="badge bg-danger ms-auto" id="notifCountProfile" style="display:none;">0</span>
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('appearance.index') }}">
                <i class="bi bi-palette me-2"></i> Appearance
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
          </ul>
        </li>{{-- End Profile Dropdown --}}

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      @foreach($sidebarMenu as $item)
        @if(($item['type'] ?? null) === 'heading')
          {{-- Section Heading --}}
          <li class="nav-heading">{{ $item['label'] }}</li>

        @elseif(isset($item['children']))
          {{-- Dropdown Menu --}}
          @php
            $childRoutes = collect($item['children'])->pluck('route')->filter()->toArray();
            $isOpen = false;
            // Check exact child routes
            foreach ($childRoutes as $cr) {
              if (request()->routeIs($cr) || request()->routeIs($cr . '.*')) {
                $isOpen = true;
                break;
              }
            }
            // Also check route prefixes (e.g. student.show should open Students menu)
            if (!$isOpen) {
              $prefixes = collect($childRoutes)->map(fn($r) => explode('.', $r)[0])->unique();
              foreach ($prefixes as $px) {
                if (request()->routeIs($px . '.*')) {
                  $isOpen = true;
                  break;
                }
              }
            }
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $isOpen ? '' : 'collapsed' }}" data-bs-target="#{{ $item['id'] }}" data-bs-toggle="collapse" href="#">
              <i class="{{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="{{ $item['id'] }}" class="nav-content collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
              @foreach($item['children'] as $child)
                <li>
                  <a href="{{ route($child['route']) }}" class="{{ request()->routeIs($child['route']) || request()->routeIs($child['route'] . '.*') ? 'active' : '' }}">
                    <i class="bi bi-circle"></i><span>{{ $child['label'] }}</span>
                  </a>
                </li>
              @endforeach
            </ul>
          </li>

        @else
          {{-- Single Link --}}
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*') ? '' : 'collapsed' }}" href="{{ route($item['route']) }}">
              <i class="{{ $item['icon'] }}"></i>
              <span>{{ $item['label'] }}</span>
            </a>
          </li>
        @endif
      @endforeach

    </ul>

  </aside><!-- End Sidebar-->

  @yield("content")

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>{{ setting('school_name', 'School Management System') }}</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="#">{{ setting('school_name', 'School Management System') }}</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
 
  <!-- Vendor JS Files -->
  <script src="{{asset("assets/vendor/apexcharts/apexcharts.min.js")}}"></script>
  <script src="{{asset("assets/vendor/bootstrap/js/bootstrap.bundle.min.js")}}"></script>
  <script src="{{asset("assets/vendor/chart.js/chart.min.js")}}"></script>
  <script src="{{asset("assets/vendor/echarts/echarts.min.js")}}"></script>
  <script src="{{asset("assets/vendor/quill/quill.min.js")}}"></script>
  <script src="{{asset("assets/vendor/simple-datatables/simple-datatables.js")}}"></script>
  <script src="{{asset("assets/vendor/tinymce/tinymce.min.js")}}"></script>
  <script src="{{asset("assets/vendor/php-email-form/validate.js")}}"></script>

  <!-- Template Main JS File -->
  <script src="{{asset("assets/js/main.js")}}"></script>
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>
    toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "4000" };

    @if(session('success'))  toastr.success("{{ addslashes(session('success')) }}"); @endif
    @if(session('error'))    toastr.error("{{ addslashes(session('error')) }}"); @endif
    @if(session('warning'))  toastr.warning("{{ addslashes(session('warning')) }}"); @endif
    @if(session('info'))     toastr.info("{{ addslashes(session('info')) }}"); @endif
    @if($errors->any())
      @foreach($errors->all() as $error)
        toastr.error("{{ addslashes($error) }}");
      @endforeach
    @endif

    function validateNumber(input) {
      if (!/^-?[0-9]+$/.test(input.value)) {
        alert("Please enter an integer.");
        input.value = "";
      }
    }

    /* ═══════ Notification Bell AJAX ═══════ */
    function loadNotifications() {
        fetch("{{ route('notifications.navbar-data') }}")
            .then(r => r.json())
            .then(data => {
                // Update badge counts
                const cnt = data.unread_count;
                ['notifCount','notifCountHeader','notifCountProfile'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.textContent = cnt;
                        el.style.display = cnt > 0 ? '' : 'none';
                    }
                });

                // Render dropdown list
                const list = document.getElementById('notifDropdownList');
                if (!data.notifications.length) {
                    list.innerHTML = '<li class="notification-item text-center py-3"><small class="text-muted">No notifications</small></li>';
                    return;
                }

                list.innerHTML = data.notifications.map(n => `
                    <li>
                        <a class="notification-item d-flex align-items-start gap-2 px-3 py-2 text-decoration-none ${n.read ? '' : 'bg-light'}"
                           href="${n.link || '#'}" onclick="markNotifRead(${n.id})">
                            <i class="${n.type_icon} mt-1"></i>
                            <div class="flex-grow-1">
                                <h4 class="mb-0" style="font-size:.85rem;font-weight:600;color:#012970;">${n.title}</h4>
                                <p class="mb-0 text-muted" style="font-size:.78rem;">${n.message}</p>
                                <p class="mb-0" style="font-size:.72rem;color:#899bbd;">
                                    <i class="bi bi-clock me-1"></i>${n.time} &middot; ${n.sender}
                                </p>
                            </div>
                            ${!n.read ? '<span class="badge bg-primary rounded-pill" style="font-size:.6rem;height:fit-content;">NEW</span>' : ''}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                `).join('');
            })
            .catch(() => {});
    }

    function markNotifRead(id) {
        fetch("/notifications/" + id + "/mark-read", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
        }).then(() => loadNotifications());
    }

    document.getElementById('markAllReadBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        fetch("{{ route('notifications.mark-all-read') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" }
        }).then(() => loadNotifications());
    });

    // Load on page ready & refresh every 30s
    loadNotifications();
    setInterval(loadNotifications, 30000);
  </script>

  {{-- ═══════ Live Search Autocomplete ═══════ --}}
  <script>
  (function(){
    const input   = document.getElementById('globalSearchInput');
    const box     = document.getElementById('liveSearchResults');
    if(!input || !box) return;

    let timer = null, activeIdx = -1;

    input.addEventListener('input', function(){
      clearTimeout(timer);
      const q = this.value.trim();
      if(q.length < 2){ box.innerHTML=''; box.style.display='none'; return; }
      timer = setTimeout(()=> fetchResults(q), 300);
    });

    input.addEventListener('keydown', function(e){
      const items = box.querySelectorAll('a.search-item');
      if(!items.length) return;
      if(e.key==='ArrowDown'){ e.preventDefault(); activeIdx = Math.min(activeIdx+1, items.length-1); highlight(items); }
      else if(e.key==='ArrowUp'){ e.preventDefault(); activeIdx = Math.max(activeIdx-1, 0); highlight(items); }
      else if(e.key==='Enter' && activeIdx>=0){ e.preventDefault(); items[activeIdx].click(); }
      else if(e.key==='Escape'){ box.style.display='none'; activeIdx=-1; }
    });

    document.addEventListener('click', function(e){
      if(!e.target.closest('.search-bar')) { box.style.display='none'; activeIdx=-1; }
    });

    function highlight(items){
      items.forEach((el,i)=> el.classList.toggle('active', i===activeIdx));
      if(items[activeIdx]) items[activeIdx].scrollIntoView({block:'nearest'});
    }

    function fetchResults(q){
      fetch("{{ route('global.search.suggest') }}?q=" + encodeURIComponent(q), {
        headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(data => {
        activeIdx = -1;
        if(!data.length){ box.innerHTML='<div class="p-3 text-center text-muted" style="font-size:.85rem">No results found</div>'; box.style.display='block'; return; }
        let html='', lastCat='';
        data.forEach(item => {
          if(item.cat !== lastCat){ html += '<div class="search-cat">'+item.cat+'</div>'; lastCat = item.cat; }
          html += '<a class="search-item" href="'+item.url+'">'
                + '<i class="'+item.icon+'"></i>'
                + '<div><div class="search-item-title">'+escHtml(item.title)+'</div>'
                + (item.sub ? '<div class="search-item-sub">'+escHtml(item.sub)+'</div>' : '')
                + '</div></a>';
        });
        box.innerHTML = html;
        box.style.display = 'block';
      })
      .catch(()=>{ box.style.display='none'; });
    }

    function escHtml(s){
      const d = document.createElement('div'); d.textContent = s; return d.innerHTML;
    }
  })();
  </script>

  {{-- Global Select2: make every <select> searchable --}}
  <script>
    $(document).ready(function(){
        $('select').not('.no-select2').each(function(){
            var $el = $(this);
            if ($el.data('select2')) return;           // already initialised
            var opts = { width: '100%', placeholder: $el.find('option[value=""]').text() || 'Select…', allowClear: !$el.prop('required') };
            var $modal = $el.closest('.modal');
            if ($modal.length) opts.dropdownParent = $modal; // fix z-index inside modals
            $el.select2(opts);
        });

        // Re-init when a Bootstrap modal is shown (for dynamically loaded selects)
        $(document).on('shown.bs.modal', function(e){
            $(e.target).find('select').not('.no-select2').each(function(){
                var $el = $(this);
                if ($el.data('select2')) return;
                $el.select2({ width: '100%', placeholder: $el.find('option[value=""]').text() || 'Select…', allowClear: !$el.prop('required'), dropdownParent: $(e.target) });
            });
        });
    });
  </script>

  <!-- Skin System JS -->
  <script src="{{asset("js/skin-system.js")}}"></script>

  @yield("script")
  @yield("scripts")
</body>

</html>