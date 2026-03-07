@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Grading Systems</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Grading Systems</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Grading Systems</h5>
                    <a href="{{ route('grading-systems.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Grading System</a>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Description</th><th>Grades</th><th>Default</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradingSystems as $key => $gs)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $gs->name }}</td>
                            <td>{{ Str::limit($gs->description, 50) }}</td>
                            <td><span class="badge bg-info">{{ $gs->gradeRules->count() }} grades</span></td>
                            <td>{!! $gs->is_default ? '<span class="badge bg-primary">Default</span>' : '—' !!}</td>
                            <td>{!! $gs->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('grading-systems.show', $gs->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('grading-systems.edit', $gs->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('grading-systems.destroy', $gs->id) }}" method="POST" onsubmit="return confirm('Delete this grading system?')">@csrf @method('DELETE')
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