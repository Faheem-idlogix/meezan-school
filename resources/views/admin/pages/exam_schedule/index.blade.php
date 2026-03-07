@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Exam Schedule / Date Sheet</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Exam Schedule</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Exam Schedules</h5>
                    <a href="{{ route('exam-schedules.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Create Schedule</a>
                </div>

                {{-- Filter --}}
                <form method="GET" class="row mb-3">
                    <div class="col-md-4">
                        <select name="exam_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Filter by Exam --</option>
                            @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->exam_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="class_room_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Filter by Class --</option>
                            @foreach($classRooms as $class)
                            <option value="{{ $class->id }}" {{ request('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Exam</th><th>Class</th><th>Subject</th><th>Date</th><th>Time</th><th>Room</th><th>Total Marks</th><th>Pass Marks</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $key => $schedule)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $schedule->exam->exam_name ?? '—' }}</td>
                            <td>{{ $schedule->classRoom->class_name ?? '—' }}</td>
                            <td>{{ $schedule->subject->subject_name ?? '—' }}</td>
                            <td>{{ $schedule->exam_date->format('d M Y') }}</td>
                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td>{{ $schedule->room ?? '—' }}</td>
                            <td>{{ $schedule->total_marks }}</td>
                            <td>{{ $schedule->passing_marks }}</td>
                            <td>
                                <form action="{{ route('exam-schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Delete this schedule?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
@endsection