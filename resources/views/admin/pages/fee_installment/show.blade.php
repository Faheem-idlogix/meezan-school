@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Installment Plan Details</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('fee-installments.index') }}">Installment Plans</a></li><li class="breadcrumb-item active">Details</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h5 class="text-muted mb-1">Student</h5><strong>{{ $plan->student->student_name ?? '—' }}</strong></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h5 class="text-muted mb-1">Total Amount</h5><strong class="text-primary">Rs. {{ number_format($plan->total_amount) }}</strong></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h5 class="text-muted mb-1">Paid</h5><strong class="text-success">Rs. {{ number_format($plan->paid_amount) }}</strong></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h5 class="text-muted mb-1">Remaining</h5><strong class="text-danger">Rs. {{ number_format($plan->remaining_amount) }}</strong></div></div></div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">{{ $plan->plan_name }}</h5>
                    @php $statusColors = ['active' => 'success', 'completed' => 'info', 'defaulted' => 'danger', 'cancelled' => 'secondary']; @endphp
                    <span class="badge bg-{{ $statusColors[$plan->status] ?? 'secondary' }} fs-6">{{ ucfirst($plan->status) }}</span>
                </div>

                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar bg-success" style="width: {{ $plan->progress_percentage }}%">{{ round($plan->progress_percentage) }}% Paid</div>
                </div>

                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Due Date</th><th>Amount</th><th>Paid Amount</th><th>Late Fee</th><th>Paid Date</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plan->installments->sortBy('installment_number') as $installment)
                        <tr>
                            <td>{{ $installment->installment_number }}</td>
                            <td>{{ $installment->due_date->format('d M Y') }}</td>
                            <td>Rs. {{ number_format($installment->amount) }}</td>
                            <td>{{ $installment->paid_amount ? 'Rs. ' . number_format($installment->paid_amount) : '—' }}</td>
                            <td>{{ $installment->late_fee ? 'Rs. ' . number_format($installment->late_fee) : '—' }}</td>
                            <td>{{ $installment->paid_date ? $installment->paid_date->format('d M Y') : '—' }}</td>
                            <td>{!! $installment->status_badge !!}</td>
                            <td>
                                @if(in_array($installment->status, ['pending', 'partial', 'overdue']))
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#payModal{{ $installment->id }}" title="Record Payment"><i class="bi bi-cash-coin"></i> Pay</button>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Payment Modal --}}
                        @if(in_array($installment->status, ['pending', 'partial', 'overdue']))
                        <div class="modal fade" id="payModal{{ $installment->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('fee-installments.record-payment', $installment->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Record Payment — Installment #{{ $installment->installment_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Due: Rs. {{ number_format($installment->amount) }} | Already Paid: Rs. {{ number_format($installment->paid_amount ?? 0) }}</label>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Amount Paying <span class="text-danger">*</span></label>
                                                <input type="number" name="paid_amount" step="0.01" class="form-control" value="{{ $installment->amount - ($installment->paid_amount ?? 0) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Late Fee (if any)</label>
                                                <input type="number" name="late_fee" step="0.01" class="form-control" value="0">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Payment Method</label>
                                                <select name="payment_method" class="form-select">
                                                    <option value="cash">Cash</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="online">Online</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Receipt Number</label>
                                                <input type="text" name="receipt_number" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Record Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                @if($plan->remarks)
                <div class="mt-3"><strong>Remarks:</strong> {{ $plan->remarks }}</div>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection