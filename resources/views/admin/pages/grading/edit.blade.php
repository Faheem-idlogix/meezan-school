@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Edit Grading System</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('grading-systems.index') }}">Grading Systems</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body pt-4">
                <form action="{{ route('grading-systems.update', $gradingSystem->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $gradingSystem->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end gap-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" {{ old('is_default', $gradingSystem->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label">Set as Default</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $gradingSystem->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $gradingSystem->description) }}</textarea>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-list-ol me-1"></i>Grade Rules</h6>
                    <div id="gradeRulesContainer">
                        @foreach($gradingSystem->gradeRules->sortBy('sort_order') as $i => $rule)
                        <div class="row grade-rule-row mb-2 align-items-end">
                            <div class="col-md-2"><label class="form-label">Grade *</label><input type="text" name="rules[{{ $i }}][grade]" class="form-control" value="{{ $rule->grade }}" required></div>
                            <div class="col-md-2"><label class="form-label">Label</label><input type="text" name="rules[{{ $i }}][grade_label]" class="form-control" value="{{ $rule->grade_label }}"></div>
                            <div class="col-md-2"><label class="form-label">Min % *</label><input type="number" name="rules[{{ $i }}][min_percentage]" step="0.01" class="form-control" value="{{ $rule->min_percentage }}" required></div>
                            <div class="col-md-2"><label class="form-label">Max % *</label><input type="number" name="rules[{{ $i }}][max_percentage]" step="0.01" class="form-control" value="{{ $rule->max_percentage }}" required></div>
                            <div class="col-md-2"><label class="form-label">Grade Point</label><input type="number" name="rules[{{ $i }}][grade_point]" step="0.01" class="form-control" value="{{ $rule->grade_point }}"></div>
                            <div class="col-md-2"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-outline-danger btn-sm d-block remove-rule" onclick="this.closest('.grade-rule-row').remove()"><i class="bi bi-trash"></i> Remove</button></div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRuleBtn"><i class="bi bi-plus-circle me-1"></i>Add Grade Rule</button>

                    <div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle me-1"></i>Updating will replace all existing grade rules with the ones shown above.</div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update</button>
                        <a href="{{ route('grading-systems.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

@push('scripts')
<script>
let ruleIndex = {{ $gradingSystem->gradeRules->count() }};
document.getElementById('addRuleBtn').addEventListener('click', function() {
    const container = document.getElementById('gradeRulesContainer');
    const html = `<div class="row grade-rule-row mb-2 align-items-end">
        <div class="col-md-2"><input type="text" name="rules[${ruleIndex}][grade]" class="form-control" placeholder="Grade" required></div>
        <div class="col-md-2"><input type="text" name="rules[${ruleIndex}][grade_label]" class="form-control" placeholder="Label"></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][min_percentage]" step="0.01" class="form-control" placeholder="Min %" required></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][max_percentage]" step="0.01" class="form-control" placeholder="Max %" required></div>
        <div class="col-md-2"><input type="number" name="rules[${ruleIndex}][grade_point]" step="0.01" class="form-control" placeholder="GP"></div>
        <div class="col-md-2"><button type="button" class="btn btn-outline-danger btn-sm d-block" onclick="this.closest('.grade-rule-row').remove()"><i class="bi bi-trash"></i> Remove</button></div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    ruleIndex++;
});
</script>
@endpush
@endsection