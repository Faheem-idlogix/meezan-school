<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Meezan School System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="{{asset("WSTheme/WsImg/WSFavicon.png")}}" />
  <link href="{{asset("resources/css/app.css")}}" rel="stylesheet">
  <!-- <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon"> -->
  {{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}

  <!-- Include Toastr CSS and JS files -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>



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
  <script src="{{asset("assets/vendor/jquery/jquery-3.6.4.min.js")}}"></script>
  <link href="{{asset("assets/vendor/select2/dist/css/select2.min.css")}}" rel="stylesheet" />
   <script src="{{asset("assets/vendor/select2/dist/js/select2.min.js")}}"></script>



  <!-- Template Main CSS File -->
  <link href="{{asset("assets/css/style.css")}}"  rel="stylesheet">
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
    .select2-container--default .select2-selection--single {
      border: 1px solid #dee2e6; border-radius: var(--ea-radius); height: 38px; display: flex; align-items: center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
      border-color: var(--ea-primary); box-shadow: 0 0 0 .2rem rgba(var(--ea-primary-rgb),.15);
    }

    /* ── Fee Action Buttons — creative pill icon buttons ── */
    .fee-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 30px; height: 30px;
      border-radius: 50%;
      border: none;
      cursor: pointer;
      font-size: .82rem;
      transition: all .2s ease;
      position: relative;
    }
    .fee-btn-cancel {
      background: rgba(220,53,69,.1);
      color: #dc3545;
    }
    .fee-btn-cancel:hover {
      background: #dc3545;
      color: #fff;
      box-shadow: 0 4px 12px rgba(220,53,69,.4);
      transform: scale(1.15);
    }
    .fee-btn-partial {
      background: rgba(65,84,241,.1);
      color: var(--ea-primary);
    }
    .fee-btn-partial:hover {
      background: var(--ea-primary);
      color: #fff;
      box-shadow: 0 4px 12px rgba(65,84,241,.4);
      transform: scale(1.15);
    }
    .fee-btn-full {
      background: rgba(25,135,84,.1);
      color: #198754;
    }
    .fee-btn-full:hover {
      background: linear-gradient(135deg,#198754,#22c55e);
      color: #fff;
      box-shadow: 0 4px 12px rgba(25,135,84,.4);
      transform: scale(1.15);
    }

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
  * Template Name: NiceAdmin - v2.2.2
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
        <img src="{{asset("img/logo/school_logo.ico")}}" alt="">
         <span class="d-none d-lg-block">Meezan School</span> 
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        {{-- <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

           

          </ul>

        </li> 

        

        </li><!-- End Messages Nav --> --}}

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->name }}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{ Auth::user()->name }}</h6>
              
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <!-- <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li> -->
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item" href="{{ route('logout') }}"
              onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>
              {{ __('Sign Out') }}
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
          </form>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? '' : 'collapsed' }}" href="{{ route('home') }}">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>

      {{-- ═══════════ ACADEMIC ═══════════ --}}
      <li class="nav-heading">Academic</li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-students" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people-fill"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-students" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('student.index') }}"><i class="bi bi-circle"></i><span>All Students</span></a></li>
          <li><a href="{{ route('student.create') }}"><i class="bi bi-circle"></i><span>Add Student</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-teachers" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-workspace"></i><span>Teachers</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-teachers" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('teacher.index') }}"><i class="bi bi-circle"></i><span>All Teachers</span></a></li>
          <li><a href="{{ route('teacher.create') }}"><i class="bi bi-circle"></i><span>Add Teacher</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-classes" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Classes & Subjects</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-classes" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('class.index') }}"><i class="bi bi-circle"></i><span>All Classes</span></a></li>
          <li><a href="{{ route('class.create') }}"><i class="bi bi-circle"></i><span>Add Class</span></a></li>
          <li><a href="{{ route('subject.index') }}"><i class="bi bi-circle"></i><span>Subjects</span></a></li>
          <li><a href="{{ route('class_subject.index') }}"><i class="bi bi-circle"></i><span>Class Subjects</span></a></li>
          <li><a href="{{ route('session.index') }}"><i class="bi bi-circle"></i><span>Sessions</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-timetable" data-bs-toggle="collapse" href="#">
          <i class="bi bi-calendar3"></i><span>Timetable</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-timetable" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('timetable.index') }}"><i class="bi bi-circle"></i><span>View Timetable</span></a></li>
          <li><a href="{{ route('timetable.create') }}"><i class="bi bi-circle"></i><span>Add Period</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-attendance" data-bs-toggle="collapse" href="#">
          <i class="bi bi-calendar-check-fill"></i><span>Attendance</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-attendance" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('attendance') }}"><i class="bi bi-circle"></i><span>Mark Attendance</span></a></li>
          <li><a href="{{ route('get_attendance_report') }}"><i class="bi bi-circle"></i><span>Attendance Report</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-leave" data-bs-toggle="collapse" href="#">
          <i class="bi bi-calendar-x-fill"></i><span>Leave Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-leave" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('leave.index') }}"><i class="bi bi-circle"></i><span>All Leaves</span></a></li>
          <li><a href="{{ route('leave.create') }}"><i class="bi bi-circle"></i><span>Add Leave Request</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-exams" data-bs-toggle="collapse" href="#">
          <i class="bi bi-pencil-square"></i><span>Exams & Results</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-exams" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('exam.index') }}"><i class="bi bi-circle"></i><span>Exam List</span></a></li>
          <li><a href="{{ route('exam.create') }}"><i class="bi bi-circle"></i><span>Add Exam</span></a></li>
          <li><a href="{{ route('exam_result.index') }}"><i class="bi bi-circle"></i><span>Results</span></a></li>
          <li><a href="{{ route('result_card') }}"><i class="bi bi-circle"></i><span>Result Card</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-diary" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-bookmark-fill"></i><span>Daily Diary</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-diary" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('diary.index') }}"><i class="bi bi-circle"></i><span>View Diary</span></a></li>
          <li><a href="{{ route('diary.create') }}"><i class="bi bi-circle"></i><span>New Entry</span></a></li>
        </ul>
      </li>

      {{-- ═══════════ FINANCE ═══════════ --}}

      <li class="nav-heading">Finance</li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('finance.*') ? '' : 'collapsed' }}" href="{{ route('finance.index') }}">
          <i class="bi bi-graph-up-arrow"></i><span>Finance Hub</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-finance" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cash-coin"></i><span>Fee Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-finance" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('fee_voucher_create') }}"><i class="bi bi-circle"></i><span>Create Monthly Invoice</span></a></li>
          <li><a href="{{ route('fee_voucher') }}"><i class="bi bi-circle"></i><span>Monthly Invoices</span></a></li>
          <li><a href="{{ route('create_student_fee') }}"><i class="bi bi-circle"></i><span>Student Voucher</span></a></li>
          <li><a href="{{ route('voucher.index') }}"><i class="bi bi-circle"></i><span>Journal Vouchers</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-payroll" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cash-stack"></i><span>Payroll</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-payroll" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('payroll.index') }}"><i class="bi bi-circle"></i><span>Monthly Payroll</span></a></li>
          <li><a href="{{ route('payroll.create') }}"><i class="bi bi-circle"></i><span>Generate Payroll</span></a></li>
          <li><a href="{{ route('payroll.advances') }}"><i class="bi bi-circle"></i><span>Salary Advances</span></a></li>
        </ul>
      </li>

      {{-- ═══════════ COMMUNICATION ═══════════ --}}
      <li class="nav-heading">Communication</li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('whatsapp.*') ? '' : 'collapsed' }}" href="{{ route('whatsapp.index') }}">
          <i class="bi bi-whatsapp"></i><span>WhatsApp Hub</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-notices" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone-fill"></i><span>Notices</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-notices" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('notice.index') }}"><i class="bi bi-circle"></i><span>All Notices</span></a></li>
          <li><a href="{{ route('notice.create') }}"><i class="bi bi-circle"></i><span>New Notice</span></a></li>
        </ul>
      </li>

      {{-- ═══════════ REPORTS & DOCS ═══════════ --}}
      <li class="nav-heading">Reports & Docs</li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-reports" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart-line-fill"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-reports" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('reports.index') }}"><i class="bi bi-circle"></i><span>Reports Hub</span></a></li>
          <li><a href="{{ route('reports.finance') }}"><i class="bi bi-circle"></i><span>Finance Report</span></a></li>
          <li><a href="{{ route('reports.fees') }}"><i class="bi bi-circle"></i><span>Fee Collection</span></a></li>
          <li><a href="{{ route('reports.attendance') }}"><i class="bi bi-circle"></i><span>Attendance Report</span></a></li>
          <li><a href="{{ route('reports.students') }}"><i class="bi bi-circle"></i><span>Student Report</span></a></li>
          <li><a href="{{ route('reports.exams') }}"><i class="bi bi-circle"></i><span>Exam Report</span></a></li>
          <li><a href="{{ route('reports.archived') }}"><i class="bi bi-circle"></i><span>Archived Records</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('documentation') ? '' : 'collapsed' }}" href="{{ route('documentation') }}">
          <i class="bi bi-book-fill"></i><span>Documentation</span>
        </a>
      </li>

      {{-- ═══════════ ADMINISTRATION ═══════════ --}}
      <li class="nav-heading">Administration</li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-users" data-bs-toggle="collapse" href="#">
          <i class="bi bi-shield-lock-fill"></i><span>User Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-users" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('users.index') }}"><i class="bi bi-circle"></i><span>All Users</span></a></li>
          <li><a href="{{ route('users.create') }}"><i class="bi bi-circle"></i><span>Add User</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('settings.*') ? '' : 'collapsed' }}" href="{{ route('settings.index') }}">
          <i class="bi bi-gear-fill"></i>
          <span>Settings & WhatsApp</span>
        </a>
      </li>

      @if(Auth::user()->role === 'super_admin')
      <li class="nav-heading">Super Admin</li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#nav-superadmin" data-bs-toggle="collapse" href="#">
          <i class="bi bi-buildings"></i><span>Schools</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="nav-superadmin" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="{{ route('super_admin.dashboard') }}"><i class="bi bi-circle"></i><span>Super Dashboard</span></a></li>
          <li><a href="{{ route('super_admin.schools') }}"><i class="bi bi-circle"></i><span>Manage Schools</span></a></li>
          <li><a href="{{ route('super_admin.schools.create') }}"><i class="bi bi-circle"></i><span>Add School</span></a></li>
          <li><a href="{{ route('super_admin.plans') }}"><i class="bi bi-circle"></i><span>Plans</span></a></li>
        </ul>
      </li>
      @endif

    </ul>

  </aside><!-- End Sidebar-->
          <span>Settings & WhatsApp</span>
        </a>
      </li>

    </ul>

  </aside><!-- End Sidebar-->

  @yield("content")

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Meezan School System</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="#">Meezan School System</a>
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
  </script>
  @yield("script")
  @yield("scripts")
</body>

</html>