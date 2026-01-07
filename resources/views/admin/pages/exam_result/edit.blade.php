@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
            <h1>Edit Exam Result</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exam_result.index') }}">Exam Result</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div class="col-lg-2" style="text-align: right;">
            <a href="{{ route('exam_result.index') }}" class="btn btn-primary">All Exam Results</a>
        </div>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Error:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    {{ session('error') }}
                </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Exam Result</h5>

                        <form id="examResultForm" action="{{ route('exam_result.update', $examResult->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <!-- Step 1: Exam Selection -->
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="examSelect" class="col-form-label">Select Exam <span class="text-danger">*</span></label>
                                    <select name="exam_id" id="examSelect" class="form-select" required>
                                        <option value="" disabled>Choose an exam</option>
                                        @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ $examResult->exam_id == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 2: Class Selection -->
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="classSelect" class="col-form-label">Select Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="classSelect" class="form-select" required>
                                        <option value="" disabled>Choose a class</option>
                                        @foreach ($classRooms as $class)
                                        <option value="{{ $class->id }}" {{ $examResult->class_id == $class->id ? 'selected' : '' }}>
                                            {{ $class->class_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 3: Student Selection -->
                            <div id="studentSection" class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="studentSelect" class="col-form-label">Student <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $examResult->student->student_name }}" readonly>
                                    <input type="hidden" name="student_id" value="{{ $examResult->student_id }}">
                                    <small class="text-muted">Student cannot be changed while editing</small>
                                </div>
                            </div>

                            <!-- Step 4: Subject Selection -->
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="subjectSelect" class="col-form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $examResult->subject->subject_name }}" readonly>
                                    <input type="hidden" name="subject_id" value="{{ $examResult->subject_id }}">
                                    <small class="text-muted">Subject cannot be changed while editing</small>
                                </div>
                            </div>

                            <!-- Step 5: Marks Input -->
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="totalMarks" class="col-form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        name="total_marks" 
                                        id="totalMarks"
                                        value="{{ old('total_marks', $examResult->total_marks) }}"
                                        class="form-control" 
                                        placeholder="Enter Total Marks"
                                        min="0"
                                        step="0.5"
                                        required
                                    >
                                    @error('total_marks')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label for="obtainedMarks" class="col-form-label">Obtained Marks <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        name="obtained_marks" 
                                        id="obtainedMarks"
                                        value="{{ old('obtained_marks', $examResult->obtained_marks) }}"
                                        class="form-control" 
                                        placeholder="Enter Obtained Marks"
                                        min="0"
                                        step="0.5"
                                        required
                                    >
                                    @error('obtained_marks')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        Update Result
                                    </button>
                                    <a href="{{ route('exam_result.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const examResultForm = document.getElementById('examResultForm');
    const totalMarks = document.getElementById('totalMarks');
    const obtainedMarks = document.getElementById('obtainedMarks');

    /**
     * Form validation before submit
     */
    examResultForm.addEventListener('submit', function(e) {
        // Validate obtained marks <= total marks
        const total = parseFloat(totalMarks.value) || 0;
        const obtained = parseFloat(obtainedMarks.value) || 0;

        if (obtained > total) {
            e.preventDefault();
            alert('Obtained marks cannot be greater than total marks');
            return false;
        }

        // Form is valid, allow submission
        return true;
    });

    // Real-time validation feedback
    obtainedMarks.addEventListener('input', function() {
        const total = parseFloat(totalMarks.value) || 0;
        const obtained = parseFloat(this.value) || 0;

        if (obtained > total && total > 0) {
            this.classList.add('is-invalid');
            if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Obtained marks cannot exceed total marks';
                this.parentNode.appendChild(feedback);
            }
        } else {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }
    });
});
</script>

@endsection
