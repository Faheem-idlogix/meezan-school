@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>TC: {{ $transferCertificate->tc_number }}</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('transfer-certificate.index') }}">TCs</a></li><li class="breadcrumb-item active">{{ $transferCertificate->tc_number }}</li></ol></nav>
      </div>
      <div class="d-flex gap-2">
        @if($transferCertificate->status === 'draft')
        <form action="{{ route('transfer-certificate.issue', $transferCertificate) }}" method="POST" class="d-inline" onsubmit="return confirm('Issue this TC? Student will be marked as Left.')">
          @csrf
          <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Issue TC</button>
        </form>
        @endif
        <a href="{{ route('transfer-certificate.pdf', $transferCertificate) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-file-pdf me-1"></i>Download PDF</a>
      </div>
    </div>
    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
          <div class="row g-3">
            <div class="col-md-6"><strong>TC Number:</strong> {{ $transferCertificate->tc_number }}</div>
            <div class="col-md-6"><strong>Status:</strong> {!! $transferCertificate->status_badge !!}</div>
            <div class="col-md-6"><strong>Student:</strong> {{ $transferCertificate->student->student_name ?? '—' }}</div>
            <div class="col-md-6"><strong>Class:</strong> {{ $transferCertificate->student->classroom->class_name ?? '—' }}</div>
            <div class="col-md-6"><strong>Father:</strong> {{ $transferCertificate->student->father_name ?? '—' }}</div>
            <div class="col-md-6"><strong>Issue Date:</strong> {{ $transferCertificate->issue_date->format('d M Y') }}</div>
            <div class="col-md-6"><strong>Leaving Date:</strong> {{ $transferCertificate->leaving_date ? $transferCertificate->leaving_date->format('d M Y') : '—' }}</div>
            <div class="col-md-6"><strong>Reason:</strong> {{ $transferCertificate->reason ?? '—' }}</div>
            <div class="col-md-6"><strong>Conduct:</strong> {{ $transferCertificate->conduct ?? '—' }}</div>
            <div class="col-md-6"><strong>Issued By:</strong> {{ $transferCertificate->issuedByUser->name ?? '—' }}</div>
            <div class="col-12"><strong>Remarks:</strong> {{ $transferCertificate->remarks ?? '—' }}</div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
