@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Add Alumni</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('alumni.index') }}">Alumni</a></li><li class="breadcrumb-item active">Add</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('alumni.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student (Optional)</label>
                            <select name="student_id" class="form-select">
                                <option value="">-- Select Student --</option>
                                @foreach($students ?? [] as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->student_name }} - {{ $student->father_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="student_name" class="form-control @error('student_name') is-invalid @enderror" value="{{ old('student_name') }}" required>
                            @error('student_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control @error('father_name') is-invalid @enderror" value="{{ old('father_name') }}" required>
                            @error('father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact No</label>
                            <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batch Year</label>
                            <input type="text" name="batch_year" class="form-control" value="{{ old('batch_year') }}" placeholder="e.g. 2020-2025">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Class</label>
                            <input type="text" name="last_class" class="form-control" value="{{ old('last_class') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Year</label>
                            <input type="text" name="passing_year" class="form-control" value="{{ old('passing_year') }}" placeholder="e.g. 2025">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Institution</label>
                            <input type="text" name="current_institution" class="form-control" value="{{ old('current_institution') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Achievements</label>
                            <textarea name="achievements" class="form-control" rows="3">{{ old('achievements') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save</button>
                        <a href="{{ route('alumni.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection