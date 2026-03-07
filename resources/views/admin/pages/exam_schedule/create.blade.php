@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Create Exam Schedule</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('exam-schedules.index') }}">Exam Schedule</a></li><li class="breadcrumb-item active">Create</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('exam-schedules.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Exam <span class="text-danger">*</span></label>
                            <select name="exam_id" class="form-select @error('exam_id') is-invalid @enderror" required>
                                <option value="">-- Select Exam --</option>
                                @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->exam_name }}</option>
                                @endforeach
                            </select>
                            @error('exam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_room_id" class="form-select @error('class_room_id') is-invalid @enderror" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classRooms as $class)
                                <option value="{{ $class->id }}" {{ old('class_room_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                            @error('class_room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Room / Hall</label>
                            <input type="text" name="room" class="form-control" value="{{ old('room') }}" placeholder="e.g. Hall A">
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-calendar-event me-1"></i>Schedule Entries</h6>
                    <div id="scheduleContainer">
                        <div class="row schedule-row mb-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Subject *</label>
                                <select name="entries[0][subject_id]" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($subjects as $sub)<option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">Date *</label><input type="date" name="entries[0][exam_date]" class="form-control" required></div>
                            <div class="col-md-1"><label class="form-label">Start *</label><input type="time" name="entries[0][start_time]" class="form-control" required></div>
                            <div class="col-md-1"><label class="form-label">End *</label><input type="time" name="entries[0][end_time]" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Total Marks *</label><input type="number" name="entries[0][total_marks]" class="form-control" value="100" required></div>
                            <div class="col-md-2"><label class="form-label">Pass Marks *</label><input type="number" name="entries[0][passing_marks]" class="form-control" value="33" required></div>
                            <div class="col-md-2"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="this.closest('.schedule-row').remove()"><i class="bi bi-trash"></i></button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addEntryBtn"><i class="bi bi-plus-circle me-1"></i>Add Subject</button>

                    <div class="col-md-12 mt-3 mb-3">
                        <label class="form-label">Instructions (for all)</label>
                        <textarea name="instructions" class="form-control" rows="2">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Schedule</button>
                        <a href="{{ route('exam-schedules.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

@push('scripts')
<script>
let entryIndex = 1;
const subjects = @json($subjects);
document.getElementById('addEntryBtn').addEventListener('click', function() {
    let options = '<option value="">Select</option>';
    subjects.forEach(s => { options += `<option value="${s.id}">${s.subject_name}</option>`; });
    const html = `<div class="row schedule-row mb-2 align-items-end">
        <div class="col-md-2"><select name="entries[${entryIndex}][subject_id]" class="form-select" required>${options}</select></div>
        <div class="col-md-2"><input type="date" name="entries[${entryIndex}][exam_date]" class="form-control" required></div>
        <div class="col-md-1"><input type="time" name="entries[${entryIndex}][start_time]" class="form-control" required></div>
        <div class="col-md-1"><input type="time" name="entries[${entryIndex}][end_time]" class="form-control" required></div>
        <div class="col-md-2"><input type="number" name="entries[${entryIndex}][total_marks]" class="form-control" value="100" required></div>
        <div class="col-md-2"><input type="number" name="entries[${entryIndex}][passing_marks]" class="form-control" value="33" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="this.closest('.schedule-row').remove()"><i class="bi bi-trash"></i></button></div>
    </div>`;
    document.getElementById('scheduleContainer').insertAdjacentHTML('beforeend', html);
    entryIndex++;
});
</script>
@endpush
@endsection