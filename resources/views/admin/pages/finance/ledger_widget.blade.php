<div class="row mb-3">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-cash-coin text-success me-1"></i>Income</h6>
                <div class="fs-5 fw-bold">{{ number_format($ledgerTotals->income ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-receipt text-danger me-1"></i>Expense</h6>
                <div class="fs-5 fw-bold">{{ number_format($ledgerTotals->expense ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-graph-up-arrow text-primary me-1"></i>Profit / Loss</h6>
                <div class="fs-5 fw-bold">{{ number_format(($ledgerTotals->income ?? 0) - ($ledgerTotals->expense ?? 0)) }}</div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Reference</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vouchers as $voucher)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $voucher->voucher_date ? $voucher->voucher_date->format('d M Y') : $voucher->created_at->format('d M Y') }}</td>
                <td>
                    <span class="badge bg-{{ $voucher->type == 'income' ? 'success' : 'danger' }}">{{ ucfirst($voucher->type) }}</span>
                </td>
                <td>{{ $voucher->category }}</td>
                <td>{{ $voucher->description }}</td>
                <td class="fw-bold">{{ number_format($voucher->amount) }}</td>
                <td>{{ ucfirst($voucher->payment_mode) }}</td>
                <td>{{ $voucher->reference_no }}</td>
                <td>
                    <form action="{{ route('finance.voucher.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Delete this voucher?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-2">
        {{ $vouchers->links() }}
    </div>
</div>
