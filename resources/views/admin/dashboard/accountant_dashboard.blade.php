@extends('admin.layout.master')
@section('title', 'Accounts Dashboard')

@section('content')
<div class="pagetitle mb-4">
    <h1 class="fw-bold">Accounts Dashboard 💰</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Accounts</li>
        </ol>
    </nav>
</div>

<section class="section">
    {{-- Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-xxl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(25,135,84,.1);">
                        <i class="bi bi-cash-stack text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">This Month's Fee</div>
                        <div class="fw-bold fs-4">PKR {{ number_format($totalFee) }}</div>
                        <div class="text-muted small">{{ date('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(13,110,253,.1);">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Students</div>
                        <div class="fw-bold fs-4">{{ $totalStudents }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(220,53,69,.1);">
                        <i class="bi bi-receipt text-danger fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Fee Entries</div>
                        <div class="fw-bold fs-4">{{ $students->count() }}</div>
                        <div class="text-muted small">This month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('fee_voucher') }}" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff fs-3"></i>
                                <span class="small fw-semibold">Fee Vouchers</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('create_student_fee') }}" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-plus-circle fs-3"></i>
                                <span class="small fw-semibold">Add Fee</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('student.index') }}" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-people fs-3"></i>
                                <span class="small fw-semibold">Students</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('class.index') }}" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-building fs-3"></i>
                                <span class="small fw-semibold">Classes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fee Entries Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>Fee Entries — {{ date('F Y') }}
                    </h6>
                    <span class="badge bg-primary px-3">{{ $students->count() }} records</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Fee Month</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $i => $fee)
                                <tr>
                                    <td class="text-muted small">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $fee->student->student_name ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ $fee->fee_month }}</td>
                                    <td class="fw-semibold text-success">PKR {{ number_format($fee->total_fee) }}</td>
                                    <td>
                                        @if($fee->status == 1)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                                        No fee entries this month
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
