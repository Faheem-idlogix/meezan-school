@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1><i class="bi bi-receipt me-2 text-primary"></i>Payslip</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Payroll</a></li><li class="breadcrumb-item active">Payslip</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf me-1"></i>PDF</a>
      @if($payroll->status !== 'paid')
      <form action="{{ route('payroll.markPaid', $payroll) }}" method="POST" class="d-inline">
        @csrf @method('PUT')
        <input type="hidden" name="paid_date" value="{{ today()->toDateString() }}">
        <button class="btn btn-success btn-sm"><i class="bi bi-check2-all me-1"></i>Mark Paid</button>
      </form>
      @endif
    </div>
  </div>

  {{-- Payslip Card --}}
  <div class="card mx-auto" style="max-width:700px">
    <div class="card-body p-4">

      {{-- Header --}}
      <div class="text-center mb-4 pb-3 border-bottom">
        <h4 class="fw-bold text-primary mb-0">Meezan School System</h4>
        <p class="text-muted small mb-2">Salary Slip — {{ $payroll->month_name }} {{ $payroll->year }}</p>
        @if($payroll->status === 'paid')
          <span class="badge bg-success px-3 py-1">✓ PAID</span>
        @elseif($payroll->status === 'approved')
          <span class="badge bg-primary px-3 py-1">APPROVED</span>
        @else
          <span class="badge bg-secondary px-3 py-1">DRAFT</span>
        @endif
      </div>

      {{-- Employee Info --}}
      <div class="row mb-4">
        <div class="col-6">
          <table class="table table-borderless table-sm mb-0">
            <tr><td class="text-muted small">Employee</td><td><strong>{{ $payroll->teacher?->teacher_name }}</strong></td></tr>
            <tr><td class="text-muted small">Employee ID</td><td>{{ $payroll->teacher?->employee_id ?? '—' }}</td></tr>
            <tr><td class="text-muted small">Designation</td><td>{{ $payroll->teacher?->specialization ?? 'Teacher' }}</td></tr>
          </table>
        </div>
        <div class="col-6">
          <table class="table table-borderless table-sm mb-0">
            <tr><td class="text-muted small">Pay Period</td><td><strong>{{ $payroll->month_name }} {{ $payroll->year }}</strong></td></tr>
            <tr><td class="text-muted small">Working Days</td><td>{{ $payroll->working_days }}</td></tr>
            <tr><td class="text-muted small">Present Days</td><td>{{ $payroll->present_days }}</td></tr>
          </table>
        </div>
      </div>

      {{-- Earnings + Deductions Side by Side --}}
      <div class="row g-3 mb-3">
        <div class="col-6">
          <div class="p-3 rounded" style="background:#f0fff4;border:1px solid #c3f7d0">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-plus-circle me-1"></i>Earnings</h6>
            <table class="table table-sm table-borderless mb-0">
              <tr><td class="small">Basic Salary</td><td class="text-end">{{ number_format($payroll->basic_salary,0) }}</td></tr>
              @if($payroll->house_rent_allowance)<tr><td class="small">House Rent</td><td class="text-end">{{ number_format($payroll->house_rent_allowance,0) }}</td></tr>@endif
              @if($payroll->medical_allowance)<tr><td class="small">Medical</td><td class="text-end">{{ number_format($payroll->medical_allowance,0) }}</td></tr>@endif
              @if($payroll->transport_allowance)<tr><td class="small">Transport</td><td class="text-end">{{ number_format($payroll->transport_allowance,0) }}</td></tr>@endif
              @if($payroll->bonus)<tr><td class="small">Bonus</td><td class="text-end">{{ number_format($payroll->bonus,0) }}</td></tr>@endif
              @if($payroll->other_allowances)<tr><td class="small">Other</td><td class="text-end">{{ number_format($payroll->other_allowances,0) }}</td></tr>@endif
              <tr class="border-top"><td class="fw-bold small">Total</td><td class="text-end fw-bold text-success">{{ number_format($payroll->total_earnings,0) }}</td></tr>
            </table>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 rounded" style="background:#fff5f5;border:1px solid #ffc9c9">
            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-dash-circle me-1"></i>Deductions</h6>
            <table class="table table-sm table-borderless mb-0">
              @if($payroll->advance_deduction)<tr><td class="small">Advance</td><td class="text-end">{{ number_format($payroll->advance_deduction,0) }}</td></tr>@endif
              @if($payroll->absence_deduction)<tr><td class="small">Absence</td><td class="text-end">{{ number_format($payroll->absence_deduction,0) }}</td></tr>@endif
              @if($payroll->tax_deduction)<tr><td class="small">Tax</td><td class="text-end">{{ number_format($payroll->tax_deduction,0) }}</td></tr>@endif
              @if($payroll->other_deductions)<tr><td class="small">Other</td><td class="text-end">{{ number_format($payroll->other_deductions,0) }}</td></tr>@endif
              <tr class="border-top"><td class="fw-bold small">Total</td><td class="text-end fw-bold text-danger">{{ number_format($payroll->total_deductions,0) }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Net Salary --}}
      <div class="p-3 rounded text-center" style="background:linear-gradient(135deg,#4154f1,#717ff5);color:#fff">
        <div class="small opacity-75 mb-1">NET SALARY</div>
        <div class="fs-2 fw-bold">Rs. {{ number_format($payroll->net_salary,0) }}</div>
        @if($payroll->paid_date)<div class="small opacity-75 mt-1">Paid on {{ $payroll->paid_date->format('d M Y') }} via {{ ucfirst($payroll->payment_method ?? 'cash') }}</div>@endif
      </div>

      @if($payroll->notes)
      <p class="text-muted small mt-3 mb-0"><strong>Notes:</strong> {{ $payroll->notes }}</p>
      @endif

    </div>
  </div>
</main>
@endsection
