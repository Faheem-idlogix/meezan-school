@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
            <h1>Create Exam Result</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Exam Result</li>
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

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Enter Exam Results for Students</h5>

                        <form id="examResultForm" action="{{ route('exam_result.store') }}" method="post">
                            @csrf

                            <!-- Step 1: Exam Selection -->
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="examSelect" class="col-form-label">Select Exam <span class="text-danger">*</span></label>
                                    <select name="exam_id" id="examSelect" class="form-select" required>
                                        <option value="" disabled selected>Choose an exam</option>
                                        @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}">{{ $exam->name }}</option>
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
                                        <option value="" disabled selected>Choose a class</option>
                                        @foreach ($classRooms as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 3: Student Selection (Hidden until class is selected) -->
                            <div id="studentSection" class="row mb-3" style="display: none;">
                                <div class="col-lg-6">
                                    <label for="studentSelect" class="col-form-label">Select Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="studentSelect" class="form-select" required>
                                        <option value="" disabled selected>Choose a student</option>
                                    </select>
                                    @error('student_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 4: Subjects Table (Hidden until class is selected) -->
                            <div id="subjectsSection" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Subject Name</th>
                                                <th style="width: 30%;">Total Marks <span class="text-danger">*</span></th>
                                                <th style="width: 30%;">Obtained Marks <span class="text-danger">*</span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="subjectsTableBody">
                                            <!-- Subjects will be populated via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                                @error('marks')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        Save All Results
                                    </button>
                                    <button type="reset" class="btn btn-secondary">Clear Form</button>
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
    const examSelect = document.getElementById('examSelect');
    const classSelect = document.getElementById('classSelect');
    const studentSelect = document.getElementById('studentSelect');
    const studentSection = document.getElementById('studentSection');
    const subjectsSection = document.getElementById('subjectsSection');
    const subjectsTableBody = document.getElementById('subjectsTableBody');
    const submitBtn = document.getElementById('submitBtn');
    const examResultForm = document.getElementById('examResultForm');

    // Event: When class is selected
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        if (!classId) {
            studentSection.style.display = 'none';
            subjectsSection.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }

        // Fetch students and subjects via AJAX
        fetchClassData(classId);
    });

    /**
     * Fetch students and subjects for selected class
     */
    function fetchClassData(classId) {
        const url = `/exam_result/ajax/class-data/${classId}`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch class data');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    populateStudents(data.students);
                    populateSubjects(data.subjects);
                    studentSection.style.display = 'block';
                    subjectsSection.style.display = 'block';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading class data. Please try again.');
            });
    }

    /**
     * Populate student dropdown
     */
    function populateStudents(students) {
        studentSelect.innerHTML = '<option value="" disabled selected>Choose a student</option>';
        
        if (students.length === 0) {
            studentSelect.innerHTML += '<option disabled>No students in this class</option>';
            return;
        }

        students.forEach(student => {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = student.student_name;
            studentSelect.appendChild(option);
        });
    }

    /**
     * Populate subjects table
     */
    function populateSubjects(subjects) {
        subjectsTableBody.innerHTML = '';

        if (subjects.length === 0) {
            subjectsTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No subjects assigned to this class</td></tr>';
            submitBtn.disabled = true;
            return;
        }

        subjects.forEach(subject => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${subject.name}</strong>
                    <input type="hidden" name="marks[${subject.id}][subject_id]" value="${subject.id}">
                </td>
                <td>
                    <input 
                        type="number" 
                        name="marks[${subject.id}][total_marks]" 
                        class="form-control total-marks-input" 
                        placeholder="0"
                        min="0"
                        step="0.5"
                        required
                    >
                </td>
                <td>
                    <input 
                        type="number" 
                        name="marks[${subject.id}][obtained_marks]" 
                        class="form-control obtained-marks-input" 
                        placeholder="0"
                        min="0"
                        step="0.5"
                        required
                    >
                </td>
            `;
            subjectsTableBody.appendChild(row);
        });

        // Enable submit button
        submitBtn.disabled = false;
    }

    /**
     * Form validation before submit
     */
    examResultForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate that exam is selected
        if (!examSelect.value) {
            alert('Please select an exam');
            return;
        }

        // Validate that class is selected
        if (!classSelect.value) {
            alert('Please select a class');
            return;
        }

        // Validate that student is selected
        if (!studentSelect.value) {
            alert('Please select a student');
            return;
        }

        // Validate that all subject marks are filled
        const inputs = subjectsTableBody.querySelectorAll('input[type="number"]');
        for (let input of inputs) {
            if (!input.value || input.value < 0) {
                alert('Please fill in all marks fields with valid values');
                return;
            }
        }

        // Validate obtained marks <= total marks
        const rows = subjectsTableBody.querySelectorAll('tr');
        for (let row of rows) {
            const totalMarksInput = row.querySelector('.total-marks-input');
            const obtainedMarksInput = row.querySelector('.obtained-marks-input');
            
            const totalMarks = parseFloat(totalMarksInput.value) || 0;
            const obtainedMarks = parseFloat(obtainedMarksInput.value) || 0;

            if (obtainedMarks > totalMarks) {
                alert('Obtained marks cannot be greater than total marks');
                return;
            }
        }

        // Form is valid, submit
        this.submit();
    });
});
</script>

@endsection
