@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Behavior Record</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('behavior.index') }}">Behavior</a></li>
          <li class="breadcrumb-item active">Add Record</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
          <form action="{{ route('behavior.store') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Student <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                  <option value="">Select Student</option>
                  @foreach($students as $s)
                  <option value="{{ $s->id }}">{{ $s->student_name }} ({{ $s->classroom->class_name ?? '—' }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                  <option value="positive">Positive</option>
                  <option value="negative">Negative</option>
                  <option value="neutral">Neutral</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                  @foreach($categories as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Points</label>
                <input type="number" name="points" class="form-control" value="0">
              </div>
              <div class="col-md-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="incident_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Action Taken</label>
                <select name="action_taken" class="form-select">
                  <option value="none">None</option>
                  <option value="verbal_warning">Verbal Warning</option>
                  <option value="written_warning">Written Warning</option>
                  <option value="parent_meeting">Parent Meeting</option>
                  <option value="suspension">Suspension</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Action Details</label>
                <input type="text" name="action_details" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Record</button>
                <a href="{{ route('behavior.index') }}" class="btn btn-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
</main>
@endsection
