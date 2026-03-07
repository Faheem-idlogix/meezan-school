@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Create Grading System</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('grading-systems.index') }}">Grading Systems</a></li><li class="breadcrumb-item active">Create</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('grading-systems.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Standard Grading" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end gap-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label">Set as Default</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-list-ol me-1"></i>Grade Rules</h6>
                    <div id="gradeRulesContainer">
                        <div class="row grade-rule-row mb-2 align-items-end">
                            <div class="col-md-2"><label class="form-label">Grade *</label><input type="text" name="rules[0][grade]" class="form-control" placeholder="A+" required></div>
                            <div class="col-md-2"><label class="form-label">Label</label><input type="text" name="rules[0][grade_label]" class="form-control" placeholder="Outstanding"></div>
                            <div class="col-md-2"><label class="form-label">Min % *</label><input type="number" name="rules[0][min_percentage]" step="0.01" class="form-control" placeholder="90" required></div>
                            <div class="col-md-2"><label class="form-label">Max % *</label><input type="number" name="rules[0][max_percentage]" step="0.01" class="form-control" placeholder="100" required></div>
                            <div class="col-md-2"><label class="form-label">Grade Point</label><input type="number" name="rules[0][grade_point]" step="0.01" class="form-control" placeholder="4.0"></div>
                            <div class="col-md-2"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm d-block remove-rule" onclick="this.closest('.grade-rule-row').remove()"><i class="bi bi-trash"></i> Remove</button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRuleBtn"><i class="bi bi-plus-circle me-1"></i>Add Grade Rule</button>

                    <hr class="mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Grading System</button>
                        <a href="{{ route('grading-systems.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

@push('scripts')
<script>
let ruleIndex = 1;
document.getElementById('addRuleBtn').addEventListener('click', function() {
    const container = document.getElementById('gradeRulesContainer');
    const html = `<div class="row grade-rule-row mb-2 align-items-end">
        <div class="col-md-2"><input type="text" name="rules[${ruleIndex}][grade]" class="form-control" placeholder="Grade" required></div>
        <div class="col-md-2"><input type="text" name="rules[${ruleIndex}][grade_label]" class="form-control" placeholder="Label"></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][min_percentage]" step="0.01" class="form-control" placeholder="Min %" required></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][max_percentage]" step="0.01" class="form-control" placeholder="Max %" required></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][grade_point]" step="0.01" class="form-control" placeholder="GP"></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm d-block remove-rule" onclick="this.closest('.grade-rule-row').remove()"><i class="bi bi-trash"></i> Remove</button></div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    ruleIndex++;
});
</script>
@endpush
@endsection