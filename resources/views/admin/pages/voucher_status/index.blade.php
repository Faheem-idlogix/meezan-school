@extends('admin.layout.master')
@section('title', 'Voucher Status Overview')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-receipt-cutoff me-2"></i>Voucher Status Overview</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                <li class="breadcrumb-item active">Voucher Status</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        {{-- Overall Count Cards --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="ea-stat blue">
                    <div class="ea-stat-icon"><i class="bi bi-receipt"></i></div>
                    <div class="ea-stat-label">Total Vouchers</div>
                    <div class="ea-stat-value">{{ number_format($totalAll) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat green">
                    <div class="ea-stat-icon"><i class="bi bi-check-circle"></i></div>
                    <div class="ea-stat-label">Paid</div>
                    <div class="ea-stat-value">{{ number_format($totalPaid) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat" style="background:linear-gradient(135deg,#dc3545,#e74c3c);">
                    <div class="ea-stat-icon"><i class="bi bi-x-circle"></i></div>
                    <div class="ea-stat-label">Unpaid</div>
                    <div class="ea-stat-value">{{ number_format($totalUnpaid) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat orange">
                    <div class="ea-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="ea-stat-label">Pending</div>
                    <div class="ea-stat-value">{{ number_format($totalPending) }}</div>
                </div>
            </div>
        </div>

        {{-- Filtered Amount Summary --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small">Filtered Records</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($filteredCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small">Total Billed</div>
                        <div class="fs-4 fw-bold text-dark">Rs. {{ number_format($filteredTotal) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small">Total Received</div>
                        <div class="fs-4 fw-bold text-success">Rs. {{ number_format($filteredReceived) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3 text-center">
                        <div class="text-muted small">Outstanding Balance</div>
                        <div class="fs-4 fw-bold text-danger">Rs. {{ number_format($filteredBalance) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paid vs Unpaid Amount Bar --}}
        @if($filteredTotal > 0)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-success fw-bold">Paid: Rs. {{ number_format($filteredPaidAmt) }}</span>
                    <span class="text-danger fw-bold">Unpaid: Rs. {{ number_format($filteredUnpaidAmt) }}</span>
                </div>
                @php $paidPct = round(($filteredPaidAmt / $filteredTotal) * 100); @endphp
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ $paidPct }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ 100 - $paidPct }}%"></div>
                </div>
                <div class="text-center small text-muted mt-1">Collection Rate: {{ $paidPct }}%</div>
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('voucher-status.index') }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Class</label>
                            <select name="class_room_id" class="form-select form-select-sm">
                                <option value="">All Classes</option>
                                @foreach($classrooms as $cr)
                                    <option value="{{ $cr->id }}" {{ request('class_room_id') == $cr->id ? 'selected' : '' }}>
                                        {{ $cr->class_name }} - {{ $cr->section_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Fee Month</label>
                            <select name="fee_month" class="form-select form-select-sm">
                                <option value="">All Months</option>
                                @foreach($months as $m)
                                    <option value="{{ $m }}" {{ request('fee_month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">From Date</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">To Date</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Voucher# or Student">
                        </div>
                        <div class="col-md-12 mt-2 d-flex gap-2">
                            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
                            <a href="{{ route('voucher-status.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                            <a href="{{ route('voucher-status.export', request()->query()) }}" class="btn btn-success btn-sm ms-auto"><i class="bi bi-download me-1"></i> Export CSV</a>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Voucher Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">All Vouchers <span class="badge bg-primary ms-1">{{ $vouchers->count() }}</span></h5>

                <table class="table datatable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Voucher No</th>
                            <th scope="col">Student</th>
                            <th scope="col">Father Name</th>
                            <th scope="col">Class</th>
                            <th scope="col">Fee Month</th>
                            <th scope="col">Issue Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Total Fee</th>
                            <th scope="col">Received</th>
                            <th scope="col">Balance</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sr = 1; @endphp
                        @foreach($vouchers as $v)
                            @php
                                $status = $v->status ?? 'pending';
                                $badgeClass = match($status) {
                                    'paid'    => 'badge-paid',
                                    'unpaid'  => 'badge-unpaid',
                                    default   => 'badge-pending',
                                };
                                $balance = ($v->total_fee ?? 0) - ($v->received_payment_fee ?? 0);
                            @endphp
                            <tr>
                                <th>{{ $sr++ }}</th>
                                <td><strong>{{ $v->voucher_no }}</strong></td>
                                <td>{{ $v->student->student_name ?? 'N/A' }}</td>
                                <td>{{ $v->student->father_name ?? '-' }}</td>
                                <td>
                                    @if($v->class_fee_voucher && $v->class_fee_voucher->classroom)
                                        {{ $v->class_fee_voucher->classroom->class_name }} - {{ $v->class_fee_voucher->classroom->section_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $v->fee_month }}</td>
                                <td>{{ $v->getRawOriginal('issue_date') }}</td>
                                <td>{{ $v->getRawOriginal('submit_date') }}</td>
                                <td><strong>Rs. {{ number_format($v->total_fee ?? 0) }}</strong></td>
                                <td class="text-success">Rs. {{ number_format($v->received_payment_fee ?? 0) }}</td>
                                <td class="{{ $balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">Rs. {{ number_format($balance) }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                                <td>
                                    <a href="{{ route('student_fee_edit', $v->student_fee_id) }}" class="btn btn-sm btn-outline-primary" title="View / Edit">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

@push('styles')
<style>
@media print {
    .pagetitle nav, form, .btn, .card-footer, .sidebar, #header { display: none !important; }
    .main { margin: 0 !important; padding: 0 !important; }
    .ea-stat { break-inside: avoid; }
}
</style>
@endpush
@endsection
