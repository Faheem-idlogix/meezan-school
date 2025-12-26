@extends('admin.layout.master')
@section('content')

<main id="main" class="main">

    <!-- Page Title -->
    <div class="row pagetitle align-items-center">
        <div class="col-lg-10">
            <h1>Attendance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            Attendance Report
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('attendance_report') }}" method="GET">
                            <div class="row g-3 align-items-end">

                                <!-- Start Date -->
                                <div class="col-md-3">
                                    <label for="first_date" required class="form-label fw-semibold">
                                        Start Date
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="first_date" 
                                           name="first_date" 
                                           required>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-3">
                                    <label for="last_date" required class="form-label fw-semibold">
                                        End Date
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="last_date" 
                                           name="last_date" 
                                           required>
                                </div>

                                <div class="col-md-3">
                                    <label for="class_id" class="form-label fw-semibold">
                                        Select Class
                                    </label>
                                    <select class="form-select" 
                                            id="class_id" 
                                            name="class_id" 
                                            required>
                                        <option value="" disabled selected>
                                            -- Select Class --
                                        </option>
                                        @foreach($class_room as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->class_name }} - {{ $class->section_name }}
                                            </option>       
                                        @endforeach
                                    </select>
                                </div>       
                                <!-- Button -->
                                <div class="col-md-3 d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-bar-chart-line me-1"></i>
                                        Generate Report
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
@endsection
