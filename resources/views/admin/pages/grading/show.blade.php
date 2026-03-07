@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Grading System Details</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('grading-systems.index') }}">Grading Systems</a></li><li class="breadcrumb-item active">{{ $gradingSystem->name }}</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">{{ $gradingSystem->name }}</h5>
                        <p class="text-muted mb-0">{{ $gradingSystem->description ?? 'No description' }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if($gradingSystem->is_default)<span class="badge bg-primary fs-6">Default</span>@endif
                        {!! $gradingSystem->is_active ? '<span class="badge bg-success fs-6">Active</span>' : '<span class="badge bg-secondary fs-6">Inactive</span>' !!}
                    </div>
                </div>

                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Grade</th><th>Label</th><th>Min %</th><th>Max %</th><th>Grade Point</th><th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradingSystem->gradeRules->sortBy('sort_order') as $key => $rule)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong class="fs-5">{{ $rule->grade }}</strong></td>
                            <td>{{ $rule->grade_label ?? '—' }}</td>
                            <td>{{ $rule->min_percentage }}%</td>
                            <td>{{ $rule->max_percentage }}%</td>
                            <td>{{ $rule->grade_point ?? '—' }}</td>
                            <td>{{ $rule->remarks ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('grading-systems.edit', $gradingSystem->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                    <a href="{{ route('grading-systems.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection