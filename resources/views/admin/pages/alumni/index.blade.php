@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Alumni Directory</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Alumni</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <div class="row mb-3">
        <div class="col-md-3"><div class="card"><div class="card-body text-center py-3"><h3 class="text-primary">{{ $alumni->count() }}</h3><small class="text-muted">Total Alumni</small></div></div></div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Alumni List</h5>
                    <a href="{{ route('alumni.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Alumni</a>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Father Name</th><th>Batch Year</th><th>Last Class</th><th>Passing Year</th><th>Contact</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni as $key => $record)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $record->student_name }}</td>
                            <td>{{ $record->father_name }}</td>
                            <td>{{ $record->batch_year }}</td>
                            <td>{{ $record->last_class }}</td>
                            <td>{{ $record->passing_year }}</td>
                            <td>{{ $record->contact_no }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('alumni.edit', $record->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('alumni.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Delete this alumni record?')">@csrf @method('DELETE')
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