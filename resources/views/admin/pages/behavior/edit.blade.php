@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Behavior Record</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('behavior.index') }}">Behavior</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
          <form action="{{ route('behavior.update', $behavior) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Student <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                  @foreach($students as $s)
                  <option value="{{ $s->id }}" @selected($behavior->student_id == $s->id)>{{ $s->student_name }} ({{ $s->classroom->class_name ?? '—' }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                  <option value="positive" @selected($behavior->type === 'positive')>Positive</option>
                  <option value="negative" @selected($behavior->type === 'negative')>Negative</option>
                  <option value="neutral" @selected($behavior->type === 'neutral')>Neutral</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="category" class="form-select" required>
                  @foreach($categories as $key => $label)
                  <option value="{{ $key }}" @selected($behavior->category === $key)>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ $behavior->title }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Points</label>
                <input type="number" name="points" class="form-control" value="{{ $behavior->points }}">
              </div>
              <div class="col-md-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="incident_date" class="form-control" value="{{ $behavior->incident_date->format('Y-m-d') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Action Taken</label>
                <select name="action_taken" class="form-select">
                  @foreach(['none','verbal_warning','written_warning','parent_meeting','suspension','other'] as $a)
                  <option value="{{ $a }}" @selected($behavior->action_taken === $a)>{{ ucfirst(str_replace('_',' ',$a)) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Action Details</label>
                <input type="text" name="action_details" class="form-control" value="{{ $behavior->action_details }}">
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $behavior->description }}</textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('behavior.index') }}" class="btn btn-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
</main>
@endsection
