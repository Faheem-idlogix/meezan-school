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

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Attendance Report ({{ $first_date }} to {{ $last_date }})</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Present Days</th>
                                    <th>Leave Days</th>
                                    <th>Absent Days</th>
                                    <th>Attendance %</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($attendance as $key => $record)

                                @php
                                    $present = $record['present'];
                                    $leave   = $record['leave'];
                                    $absent  = $record['absent'];
                                    $total = $present + $leave + $absent;
                                    $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                                @endphp

                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $record['student']->student_name ?? 'N/A' }}</td>
                                    <td>{{ $record['classRoom']->class_name .'-'. $record['classRoom']->section_name ?? 'N/A' }}</td>

                                    <td><span class="badge bg-success">{{ $present }}</span></td>
                                    <td><span class="badge bg-warning text-dark">{{ $leave }}</span></td>
                                    <td><span class="badge bg-danger">{{ $absent }}</span></td>

                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $percentage }}%">
                                                {{ $percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No attendance records found
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>
@endsection
