@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Fee Discounts & Scholarships</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Fee Discounts</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-primary">{{ $discounts->count() }}</h3><small class="text-muted">Total Discounts</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-success">{{ $discounts->where('is_active', true)->count() }}</h3><small class="text-muted">Active</small></div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-info">{{ $discounts->sum(function($d) { return $d->students->count(); }) }}</h3><small class="text-muted">Students Covered</small></div></div></div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Discount / Scholarship List</h5>
                    <a href="{{ route('fee-discounts.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Discount</a>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Type</th><th>Value</th><th>Applicable To</th><th>Class</th><th>Students</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discounts as $key => $discount)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $discount->name }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($discount->discount_type) }}</span></td>
                            <td>{{ $discount->discount_type == 'percentage' ? $discount->discount_value . '%' : 'Rs. ' . number_format($discount->discount_value) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $discount->applicable_to)) }}</td>
                            <td>{{ $discount->classRoom->class_name ?? 'All' }}</td>
                            <td><span class="badge bg-primary">{{ $discount->students->count() }}</span></td>
                            <td>{!! $discount->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('fee-discounts.edit', $discount->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('fee-discounts.destroy', $discount->id) }}" method="POST" onsubmit="return confirm('Delete this discount?')">@csrf @method('DELETE')
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