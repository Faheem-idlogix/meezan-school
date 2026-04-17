@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Exam Schedule</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('exam-schedules.index') }}">Exam Schedule</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('exam-schedules.update-group') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam <span class="text-danger">*</span></label>
                            <select name="exam_id" class="form-select" required>
                                @foreach($exams as $e)
                                <option value="{{ $e->id }}" {{ $exam->id == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_room_id" class="form-select" required>
                                @foreach($classRooms as $class)
                                <option value="{{ $class->id }}" {{ $classRoom->id == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-calendar-event me-1"></i>Schedule Entries ({{ $schedules->count() }} subjects)</h6>

                    <div id="scheduleContainer">
                        @foreach($schedules as $i => $schedule)
                        <div class="row schedule-row mb-2 align-items-end border rounded p-2 mx-0">
                            <input type="hidden" name="entries[{{ $i }}][id]" value="{{ $schedule->id }}">
                            <div class="col-md-2">
                                @if($loop->first)<label class="form-label">Subject *</label>@endif
                                <select name="entries[{{ $i }}][subject_id]" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ $schedule->subject_id == $sub->id ? 'selected' : '' }}>{{ $sub->subject_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                @if($loop->first)<label class="form-label">Date *</label>@endif
                                <input type="date" name="entries[{{ $i }}][exam_date]" class="form-control" value="{{ $schedule->exam_date->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-1">
                                @if($loop->first)<label class="form-label">Start</label>@endif
                                <input type="time" name="entries[{{ $i }}][start_time]" class="form-control" value="{{ $schedule->start_time }}">
                            </div>
                            <div class="col-md-1">
                                @if($loop->first)<label class="form-label">End</label>@endif
                                <input type="time" name="entries[{{ $i }}][end_time]" class="form-control" value="{{ $schedule->end_time }}">
                            </div>
                            <div class="col-md-1">
                                @if($loop->first)<label class="form-label">Room</label>@endif
                                <input type="text" name="entries[{{ $i }}][room]" class="form-control" value="{{ $schedule->room }}">
                            </div>
                            <div class="col-md-2">
                                @if($loop->first)<label class="form-label">Total Marks</label>@endif
                                <input type="number" name="entries[{{ $i }}][total_marks]" class="form-control" value="{{ $schedule->total_marks }}" min="0">
                            </div>
                            <div class="col-md-2">
                                @if($loop->first)<label class="form-label">Pass Marks</label>@endif
                                <input type="number" name="entries[{{ $i }}][passing_marks]" class="form-control" value="{{ $schedule->passing_marks }}" min="0">
                            </div>
                            <div class="col-md-1">
                                @if($loop->first)<label class="form-label">&nbsp;</label>@endif
                                <button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="this.closest('.schedule-row').remove()"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addEntryBtn"><i class="bi bi-plus-circle me-1"></i>Add Subject</button>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update Schedule</button>
                        <a href="{{ route('exam-schedules.index', ['exam_id' => $exam->id]) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

@push('scripts')
<script>
let entryIndex = {{ $schedules->count() }};
const subjects = @json($subjects);
document.getElementById('addEntryBtn').addEventListener('click', function() {
    let options = '<option value="">Select</option>';
    subjects.forEach(s => { options += `<option value="${s.id}">${s.subject_name}</option>`; });
    const html = `<div class="row schedule-row mb-2 align-items-end border rounded p-2 mx-0">
        <input type="hidden" name="entries[${entryIndex}][id]" value="">
        <div class="col-md-2"><select name="entries[${entryIndex}][subject_id]" class="form-select" required>${options}</select></div>
        <div class="col-md-2"><input type="date" name="entries[${entryIndex}][exam_date]" class="form-control" required></div>
        <div class="col-md-1"><input type="time" name="entries[${entryIndex}][start_time]" class="form-control"></div>
        <div class="col-md-1"><input type="time" name="entries[${entryIndex}][end_time]" class="form-control"></div>
        <div class="col-md-1"><input type="text" name="entries[${entryIndex}][room]" class="form-control"></div>
        <div class="col-md-2"><input type="number" name="entries[${entryIndex}][total_marks]" class="form-control" value="100" min="0"></div>
        <div class="col-md-2"><input type="number" name="entries[${entryIndex}][passing_marks]" class="form-control" value="33" min="0"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="this.closest('.schedule-row').remove()"><i class="bi bi-trash"></i></button></div>
    </div>`;
    document.getElementById('scheduleContainer').insertAdjacentHTML('beforeend', html);
    entryIndex++;
});
</script>
@endpush
@endsection
