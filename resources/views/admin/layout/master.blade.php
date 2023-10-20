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
        <a class="nav-link" href="{{ route('home') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('student.index') }}">
              <i class="bi bi-circle"></i><span>All Students</span>
            </a>
          </li>
          
          <li>
            <a href="{{ route('student.create') }}">
              <i class="bi bi-circle"></i><span>Add  Student</span>
            </a>
          </li>
         
          
         
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Classes</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('class.index') }}">
              <i class="bi bi-circle"></i><span>All Classes</span>
            </a>
          </li>
        
          {{-- <li>
            <a href="{{ route('subject.index') }}">
              <i class="bi bi-circle"></i><span>Subjects</span>
            </a>
          </li> --}}
          <li>
            <a href="{{ route('session.index') }}">
              <i class="bi bi-circle"></i><span>Session</span>
            </a>
          </li>
          
        </ul>
      </li><!-- End Forms Nav -->

      {{-- <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i><span>Teachers</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>All Members</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Add Member</span>
            </a>
          </li>
        </ul>
      </li> --}}
      <!-- End Tables Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi  bi-gear"></i><span>Invoices</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('fee_voucher_create') }}">
              <i class="bi bi-circle"></i><span>Create Monthly Invoice</span>
            </a>
          </li>
          <li>
            <a href="{{ route('fee_voucher') }}">
              <i class="bi bi-circle"></i><span>Fee Invoices</span>
            </a>
          </li>
        
        </ul>
      </li><!-- End Charts Nav -->

      {{-- <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#icon-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-image"></i><span>Accounts</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="icon-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>All Images</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Add image</span>
            </a>
          </li>
        </ul>
      </li><!-- End Icons Nav --> --}}
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#icon-nav5" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-check"></i><span>Exam List</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="icon-nav5" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>Add user</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="bi bi-circle"></i><span>All user</span>
            </a>
          </li>
        </ul>
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
<script>
    function validateNumber(input) {
    var value = input.value;
    if (!/^-?[0-9]+$/.test(value)) {
      alert("Please enter an integer.");
      input.value = "";
    }
  }
  </script>
  @yield("script")
</body>

</html>