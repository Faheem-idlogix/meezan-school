@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Fee Item</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('fee-structures.index') }}">Fee Structure</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('fee-structures.update', $feeStructure->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_room_id" class="form-select @error('class_room_id') is-invalid @enderror" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classRooms as $class)
                                <option value="{{ $class->id }}" {{ old('class_room_id', $feeStructure->class_room_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                            @error('class_room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Session <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-select @error('session_id') is-invalid @enderror" required>
                                <option value="">-- Select Session --</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ old('session_id', $feeStructure->session_id) == $session->id ? 'selected' : '' }}>{{ $session->session_name ?? $session->name }}</option>
                                @endforeach
                            </select>
                            @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Category <span class="text-danger">*</span></label>
                            <select name="fee_category" class="form-select @error('fee_category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach(\App\Models\FeeStructure::feeCategories() as $cat)
                                <option value="{{ $cat }}" {{ old('fee_category', $feeStructure->fee_category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('fee_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fee Name <span class="text-danger">*</span></label>
                            <input type="text" name="fee_name" class="form-control @error('fee_name') is-invalid @enderror" value="{{ old('fee_name', $feeStructure->fee_name) }}" required>
                            @error('fee_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $feeStructure->amount) }}" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" class="form-select" required>
                                @foreach(['monthly','quarterly','semi_annual','annual','one_time'] as $freq)
                                <option value="{{ $freq }}" {{ old('frequency', $feeStructure->frequency) == $freq ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $freq)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end gap-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_mandatory" value="1" class="form-check-input" {{ old('is_mandatory', $feeStructure->is_mandatory) ? 'checked' : '' }}>
                                <label class="form-check-label">Mandatory</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $feeStructure->description) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update</button>
                        <a href="{{ route('fee-structures.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection