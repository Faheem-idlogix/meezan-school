@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Report Card Configuration</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Report Card Config</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('report-cards.store-config') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h6 class="mb-3"><i class="bi bi-gear me-1"></i>Basic Settings</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Config Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $config->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class (Optional)</label>
                            <select name="class_room_id" class="form-select">
                                <option value="">-- All Classes --</option>
                                @foreach($classRooms as $class)
                                <option value="{{ $class->id }}" {{ old('class_room_id', $config->class_room_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grading System</label>
                            <select name="grading_system_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach($gradingSystems as $gs)
                                <option value="{{ $gs->id }}" {{ old('grading_system_id', $config->grading_system_id ?? '') == $gs->id ? 'selected' : '' }}>{{ $gs->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-building me-1"></i>School Info on Report Card</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">School Name</label><input type="text" name="school_name" class="form-control" value="{{ old('school_name', $config->school_name ?? '') }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">School Phone</label><input type="text" name="school_phone" class="form-control" value="{{ old('school_phone', $config->school_phone ?? '') }}"></div>
                        <div class="col-md-4 mb-3"><label class="form-label">School Logo</label><input type="file" name="school_logo" class="form-control" accept="image/*"></div>
                        <div class="col-md-12 mb-3"><label class="form-label">School Address</label><input type="text" name="school_address" class="form-control" value="{{ old('school_address', $config->school_address ?? '') }}"></div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-toggle-on me-1"></i>Display Options</h6>
                    <div class="row">
                        @php
                        $showOptions = [
                            'show_grade' => 'Show Grade',
                            'show_gpa' => 'Show GPA',
                            'show_position' => 'Show Position',
                            'show_percentage' => 'Show Percentage',
                            'show_remarks' => 'Show Remarks',
                            'show_attendance' => 'Show Attendance',
                            'show_behavior' => 'Show Behavior',
                        ];
                        @endphp
                        @foreach($showOptions as $field => $label)
                        <div class="col-md-3 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="{{ $field }}" value="1" class="form-check-input" {{ old($field, $config->$field ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-pen me-1"></i>Signatures & Notes</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Principal Signature Name</label><input type="text" name="principal_signature" class="form-control" value="{{ old('principal_signature', $config->principal_signature ?? '') }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Class Teacher Signature Name</label><input type="text" name="class_teacher_signature" class="form-control" value="{{ old('class_teacher_signature', $config->class_teacher_signature ?? '') }}"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Header Note</label><textarea name="header_note" class="form-control" rows="2">{{ old('header_note', $config->header_note ?? '') }}</textarea></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Footer Note</label><textarea name="footer_note" class="form-control" rows="2">{{ old('footer_note', $config->footer_note ?? '') }}</textarea></div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_default" value="1" class="form-check-input" {{ old('is_default', $config->is_default ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Set as Default Configuration</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection