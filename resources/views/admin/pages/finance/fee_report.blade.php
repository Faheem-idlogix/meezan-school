<div class="row mb-3">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-cash-coin text-success me-1"></i>Total Fee</h6>
                <div class="fs-5 fw-bold">{{ number_format($feeSummary['total'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-wallet2 text-primary me-1"></i>Received</h6>
                <div class="fs-5 fw-bold">{{ number_format($feeSummary['received'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-exclamation-circle text-warning me-1"></i>Outstanding</h6>
                <div class="fs-5 fw-bold">{{ number_format($feeSummary['outstanding'] ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-people-fill text-info me-1"></i>Paid / Unpaid</h6>
                <div class="fs-6">{{ $feeSummary['paid_count'] ?? 0 }} <span class="text-success">Paid</span> / {{ $feeSummary['unpaid_count'] ?? 0 }} <span class="text-danger">Unpaid</span></div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Class</th>
                <th>Fee</th>
                <th>Status</th>
                <th>Received</th>
                <th>Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feeRecords as $fee)
            <tr>
                <td>{{ ($feeRecords->currentPage() - 1) * $feeRecords->perPage() + $loop->iteration }}</td>
                <td>{{ $fee->student->student_name ?? '' }}</td>
                <td>{{ $fee->student->classroom->class_name ?? '' }}</td>
                <td>{{ number_format($fee->total_fee) }}</td>
                <td>
                    @if($fee->status == 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($fee->status == 'unpaid')
                        <span class="badge bg-danger">Unpaid</span>
                    @elseif($fee->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-secondary">{{ $fee->status }}</span>
                    @endif
                </td>
                <td>{{ number_format($fee->received_payment_fee ?? 0) }}</td>
                <td>{{ number_format(($fee->total_fee ?? 0) - ($fee->received_payment_fee ?? 0)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{ $feeRecords->appends(['tab' => 'fee'])->links() }}
    </div>
</div>
