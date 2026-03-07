@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Fee Discount</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('fee-discounts.index') }}">Fee Discounts</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('fee-discounts.update', $discount->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $discount->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select name="discount_type" class="form-select" required>
                                <option value="percentage" {{ old('discount_type', $discount->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (Rs.)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', $discount->discount_value) }}" required>
                            @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Applicable To <span class="text-danger">*</span></label>
                            <select name="applicable_to" class="form-select" required>
                                @foreach(['all' => 'All Students', 'sibling' => 'Sibling Discount', 'merit' => 'Merit Based', 'need_based' => 'Need Based', 'staff_child' => 'Staff Child'] as $val => $label)
                                <option value="{{ $val }}" {{ old('applicable_to', $discount->applicable_to) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Class (Optional)</label>
                            <select name="class_room_id" class="form-select">
                                <option value="">-- All Classes --</option>
                                @foreach($classRooms as $class)
                                <option value="{{ $class->id }}" {{ old('class_room_id', $discount->class_room_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Valid From</label>
                            <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', $discount->valid_from?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $discount->valid_until?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $discount->description) }}</textarea>
                        </div>
                    </div>

                    {{-- Assigned Students Section --}}
                    <hr>
                    <h6 class="mb-3">Assigned Students ({{ $discount->students->count() }})</h6>
                    @if($discount->students->count() > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead><tr><th>Student</th><th>Father</th><th>Class</th><th>Remove</th></tr></thead>
                            <tbody>
                                @foreach($discount->students as $student)
                                <tr>
                                    <td>{{ $student->student_name }}</td>
                                    <td>{{ $student->father_name }}</td>
                                    <td>{{ $student->classroom->class_name ?? '—' }}</td>
                                    <td>
                                        <form action="{{ route('fee-discounts.remove-student', $discount->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this student?')">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No students assigned yet.</p>
                    @endif

                    {{-- Assign New Student --}}
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('fee-discounts.assign-student', $discount->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <select name="student_id" class="form-select" required>
                                    <option value="">-- Select Student to Assign --</option>
                                    @foreach($students ?? [] as $student)
                                    <option value="{{ $student->id }}">{{ $student->student_name }} - {{ $student->father_name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap"><i class="bi bi-plus-circle me-1"></i>Assign</button>
                            </form>
                        </div>
                    </div>
                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update</button>
                        <a href="{{ route('fee-discounts.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection