@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Create Transfer Certificate</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('transfer-certificate.index') }}">Transfer Certificates</a></li><li class="breadcrumb-item active">Create</li></ol></nav>
    </div>
    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
          <form action="{{ route('transfer-certificate.store') }}" method="POST">
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
                <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Leaving Date</label>
                <input type="date" name="leaving_date" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Reason</label>
                <select name="reason" class="form-select">
                  <option value="">Select</option>
                  <option value="Transfer">Transfer</option>
                  <option value="Migration">Migration</option>
                  <option value="Leaving">Leaving</option>
                  <option value="Completion">Completion of Studies</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Conduct</label>
                <select name="conduct" class="form-select">
                  <option value="Good">Good</option>
                  <option value="Very Good">Very Good</option>
                  <option value="Excellent">Excellent</option>
                  <option value="Satisfactory">Satisfactory</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Draft TC</button>
                <a href="{{ route('transfer-certificate.index') }}" class="btn btn-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
</main>
@endsection
