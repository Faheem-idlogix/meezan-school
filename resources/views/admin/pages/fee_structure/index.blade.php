@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Fee Structure</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Fee Structure</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-primary">{{ $feeStructures->count() }}</h3><small class="text-muted">Total Fee Items</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-success">{{ $feeStructures->where('is_active', true)->count() }}</h3><small class="text-muted">Active</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-info">{{ $feeStructures->where('is_mandatory', true)->count() }}</h3><small class="text-muted">Mandatory</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-warning">Rs. {{ number_format($feeStructures->sum('amount')) }}</h3><small class="text-muted">Total Amount</small></div></div></div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Fee Structure List</h5>
                    <a href="{{ route('fee-structures.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Fee Item</a>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Class</th><th>Category</th><th>Fee Name</th><th>Amount</th><th>Frequency</th><th>Mandatory</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feeStructures as $key => $fee)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $fee->classRoom->class_name ?? '—' }}</td>
                            <td>{{ $fee->fee_category }}</td>
                            <td>{{ $fee->fee_name }}</td>
                            <td>Rs. {{ number_format($fee->amount, 2) }}</td>
                            <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $fee->frequency)) }}</span></td>
                            <td>{!! $fee->is_mandatory ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                            <td>{!! $fee->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('fee-structures.edit', $fee->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('fee-structures.destroy', $fee->id) }}" method="POST" onsubmit="return confirm('Delete this fee item?')">@csrf @method('DELETE')
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