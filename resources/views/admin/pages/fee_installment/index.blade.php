@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Fee Installment Plans</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Installment Plans</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-primary">{{ $plans->count() }}</h3><small class="text-muted">Total Plans</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-success">{{ $plans->where('status', 'active')->count() }}</h3><small class="text-muted">Active</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-info">{{ $plans->where('status', 'completed')->count() }}</h3><small class="text-muted">Completed</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-danger">{{ $plans->where('status', 'defaulted')->count() }}</h3><small class="text-muted">Defaulted</small></div></div></div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Installment Plans</h5>
                    <a href="{{ route('fee-installments.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Create Plan</a>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Student</th><th>Plan Name</th><th>Total Amount</th><th>Installments</th><th>Progress</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $key => $plan)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $plan->student->student_name ?? '—' }}</td>
                            <td>{{ $plan->plan_name }}</td>
                            <td>Rs. {{ number_format($plan->total_amount) }}</td>
                            <td>{{ $plan->number_of_installments }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $plan->progress_percentage }}%">{{ round($plan->progress_percentage) }}%</div>
                                </div>
                            </td>
                            <td>
                                @php $statusColors = ['active' => 'success', 'completed' => 'info', 'defaulted' => 'danger', 'cancelled' => 'secondary']; @endphp
                                <span class="badge bg-{{ $statusColors[$plan->status] ?? 'secondary' }}">{{ ucfirst($plan->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('fee-installments.show', $plan->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <form action="{{ route('fee-installments.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete this plan?')">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
@endsection