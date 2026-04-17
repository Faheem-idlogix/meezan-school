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
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
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

                @forelse($grouped as $key => $entries)
                @php
                    $first = $entries->first();
                    $examId = $first->exam_id;
                    $classRoomId = $first->class_room_id;
                @endphp
                <div class="card border mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <div>
                            <strong>{{ $first->exam->name ?? '—' }}</strong>
                            <span class="text-muted mx-2">|</span>
                            <span>{{ $first->classRoom->class_name ?? '—' }}</span>
                            <span class="badge bg-primary ms-2">{{ $entries->count() }} subjects</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('exam-schedules.edit-group', ['exam_id' => $examId, 'class_room_id' => $classRoomId]) }}" class="btn btn-sm btn-outline-primary" title="Edit All"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                            <a href="{{ route('exam-schedules.print', ['exam_id' => $examId, 'class_room_id' => $classRoomId]) }}" class="btn btn-sm btn-outline-secondary" title="Print Date Sheet" target="_blank"><i class="bi bi-printer me-1"></i>Print</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Subject</th><th>Date</th><th>Time</th><th>Room</th><th>Total Marks</th><th>Pass Marks</th><th style="width:80px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $i => $schedule)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $schedule->subject->subject_name ?? '—' }}</td>
                                    <td>{{ $schedule->exam_date->format('d M Y') }}</td>
                                    <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                    <td>{{ $schedule->room ?? '—' }}</td>
                                    <td>{{ $schedule->total_marks }}</td>
                                    <td>{{ $schedule->passing_marks }}</td>
                                    <td>
                                        <form action="{{ route('exam-schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this entry?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">No exam schedules found.</div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection