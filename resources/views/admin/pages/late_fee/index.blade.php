@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Late Fee Rules</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Late Fee Rules</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                    <h5 class="card-title mb-0">Late Fee Rules</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuleModal"><i class="bi bi-plus-circle me-1"></i>Add Rule</button>
                </div>
                <table class="table datatable table-hover">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>Class</th><th>Grace Days</th><th>Charge Type</th><th>Charge Amount</th><th>Max Late Fee</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $key => $rule)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $rule->name }}</td>
                            <td>{{ $rule->classRoom->class_name ?? 'All' }}</td>
                            <td>{{ $rule->grace_days }} days</td>
                            <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rule->charge_type)) }}</span></td>
                            <td>
                                @if($rule->charge_type == 'percentage')
                                    {{ $rule->charge_amount }}%
                                @else
                                    Rs. {{ number_format($rule->charge_amount) }}
                                @endif
                            </td>
                            <td>Rs. {{ number_format($rule->max_late_fee) }}</td>
                            <td>{!! $rule->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editRuleModal{{ $rule->id }}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                    <form action="{{ route('late-fee-rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this rule?')">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editRuleModal{{ $rule->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('late-fee-rules.update', $rule->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header"><h5 class="modal-title">Edit Late Fee Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ $rule->name }}" required></div>
                                            <div class="mb-3"><label class="form-label">Class</label>
                                                <select name="class_room_id" class="form-select">
                                                    <option value="">All Classes</option>
                                                    @foreach($classRooms as $class)<option value="{{ $class->id }}" {{ $rule->class_room_id == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>@endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3"><label class="form-label">Grace Days *</label><input type="number" name="grace_days" class="form-control" value="{{ $rule->grace_days }}" required></div>
                                            <div class="mb-3"><label class="form-label">Charge Type *</label>
                                                <select name="charge_type" class="form-select" required>
                                                    <option value="fixed" {{ $rule->charge_type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                    <option value="percentage" {{ $rule->charge_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                    <option value="per_day" {{ $rule->charge_type == 'per_day' ? 'selected' : '' }}>Per Day</option>
                                                </select>
                                            </div>
                                            <div class="mb-3"><label class="form-label">Charge Amount *</label><input type="number" name="charge_amount" step="0.01" class="form-control" value="{{ $rule->charge_amount }}" required></div>
                                            <div class="mb-3"><label class="form-label">Max Late Fee *</label><input type="number" name="max_late_fee" step="0.01" class="form-control" value="{{ $rule->max_late_fee }}" required></div>
                                            <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" {{ $rule->is_active ? 'checked' : '' }}><label class="form-check-label">Active</label></div>
                                        </div>
                                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Add Rule Modal --}}
    <div class="modal fade" id="addRuleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('late-fee-rules.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Add Late Fee Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Class</label>
                            <select name="class_room_id" class="form-select">
                                <option value="">All Classes</option>
                                @foreach($classRooms as $class)<option value="{{ $class->id }}">{{ $class->class_name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Grace Days *</label><input type="number" name="grace_days" class="form-control" value="7" required></div>
                        <div class="mb-3"><label class="form-label">Charge Type *</label>
                            <select name="charge_type" class="form-select" required>
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                                <option value="per_day">Per Day</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Charge Amount *</label><input type="number" name="charge_amount" step="0.01" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Max Late Fee *</label><input type="number" name="max_late_fee" step="0.01" class="form-control" value="500" required></div>
                        <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked><label class="form-check-label">Active</label></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection