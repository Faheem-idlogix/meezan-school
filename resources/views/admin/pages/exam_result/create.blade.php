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
                                    <select name="exam_id" id="examSelect" class="form-select no-select2" required>
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
                                    <select name="class_id" id="classSelect" class="form-select no-select2" required>
                                        <option value="" disabled selected>Choose a class</option>
                                        @foreach ($classRooms as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}{{ $class->section_name ? ' - ' . $class->section_name : '' }}</option>
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
                                    <select name="student_id" id="studentSelect" class="form-select no-select2" required>
                                        <option value="" disabled selected>Choose a student</option>
                                    </select>
                                    @error('student_id')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Step 4: Subjects Table (Hidden until class is selected) -->
                            <div id="subjectsSection" style="display: none;">
                                <div id="ajaxAlert" class="alert alert-info d-none" role="alert"></div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 35%;">Subject Name</th>
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
                                        <i class="bi bi-check-lg me-1"></i>Save All Results
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary" id="resetBtn"><i class="bi bi-x-lg me-1"></i>Clear Form</button>
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
    const ajaxAlert = document.getElementById('ajaxAlert');

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
        const url = `{{ url('exam_result/ajax/class-data') }}/${classId}`;
        
        // Show loading state
        submitBtn.disabled = true;
        subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading subjects...</td></tr>';
        subjectsSection.style.display = 'block';
        ajaxAlert.classList.add('d-none');

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401 || response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Server error (HTTP ' + response.status + '). Please try again.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    populateStudents(data.students);
                    populateSubjects(data.subjects);
                    studentSection.style.display = 'block';
                    subjectsSection.style.display = 'block';
                } else {
                    showAlert('danger', data.message || 'Failed to load class data.');
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                showAlert('danger', error.message || 'Error loading class data. Please try again.');
                subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle me-2"></i>' + (error.message || 'Error loading data') + '</td></tr>';
            });
    }

    function showAlert(type, message) {
        ajaxAlert.className = 'alert alert-' + type;
        ajaxAlert.innerHTML = message;
        ajaxAlert.classList.remove('d-none');
    }

    /**
     * Populate student dropdown
     */
    function populateStudents(students) {
        studentSelect.innerHTML = '<option value="" disabled selected>Choose a student</option>';
        
        if (!students || students.length === 0) {
            studentSelect.innerHTML += '<option disabled>No active students in this class</option>';
            showAlert('warning', 'No active students found in this class. Please check student status.');
            return;
        }

        students.forEach(function(student) {
            const option = document.createElement('option');
            option.value = student.id;
            option.textContent = student.student_name;
            studentSelect.appendChild(option);
        });
    }

    /**
     * Populate subjects table with input fields for marks
     */
    function populateSubjects(subjects) {
        subjectsTableBody.innerHTML = '';

        if (!subjects || subjects.length === 0) {
            subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No subjects assigned to this class</td></tr>';
            submitBtn.disabled = true;
            showAlert('warning', 'No subjects are assigned to this class. Please assign subjects first.');
            return;
        }

        var srNo = 1;
        subjects.forEach(function(subject) {
            var row = document.createElement('tr');
            row.innerHTML = 
                '<td class="text-center">' + srNo + '</td>' +
                '<td>' +
                    '<strong>' + subject.name + '</strong>' +
                    '<input type="hidden" name="marks[' + subject.id + '][subject_id]" value="' + subject.id + '">' +
                '</td>' +
                '<td>' +
                    '<input type="number" name="marks[' + subject.id + '][total_marks]" ' +
                        'class="form-control total-marks-input" placeholder="e.g. 100" min="0" max="1000" step="0.5" required>' +
                '</td>' +
                '<td>' +
                    '<input type="number" name="marks[' + subject.id + '][obtained_marks]" ' +
                        'class="form-control obtained-marks-input" placeholder="e.g. 85" min="0" max="1000" step="0.5" required>' +
                '</td>';
            subjectsTableBody.appendChild(row);
            srNo++;
        });

        // Add real-time validation on obtained marks
        subjectsTableBody.querySelectorAll('.obtained-marks-input').forEach(function(input) {
            input.addEventListener('input', function() {
                var row = this.closest('tr');
                var totalInput = row.querySelector('.total-marks-input');
                var total = parseFloat(totalInput.value) || 0;
                var obtained = parseFloat(this.value) || 0;
                if (total > 0 && obtained > total) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
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
            examSelect.focus();
            return;
        }

        // Validate that class is selected
        if (!classSelect.value) {
            alert('Please select a class');
            classSelect.focus();
            return;
        }

        // Validate that student is selected
        if (!studentSelect.value) {
            alert('Please select a student');
            studentSelect.focus();
            return;
        }

        // Validate that all subject marks are filled
        var inputs = subjectsTableBody.querySelectorAll('input[type="number"]');
        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].value || parseFloat(inputs[i].value) < 0) {
                alert('Please fill in all marks fields with valid values');
                inputs[i].focus();
                return;
            }
        }

        // Validate obtained marks <= total marks
        var rows = subjectsTableBody.querySelectorAll('tr');
        for (var j = 0; j < rows.length; j++) {
            var totalMarksInput = rows[j].querySelector('.total-marks-input');
            var obtainedMarksInput = rows[j].querySelector('.obtained-marks-input');
            
            if (!totalMarksInput || !obtainedMarksInput) continue;
            
            var totalMarks = parseFloat(totalMarksInput.value) || 0;
            var obtainedMarks = parseFloat(obtainedMarksInput.value) || 0;

            if (obtainedMarks > totalMarks) {
                alert('Obtained marks cannot be greater than total marks for: ' + rows[j].querySelector('strong').textContent);
                obtainedMarksInput.focus();
                return;
            }
        }

        // Disable submit button to prevent double-click
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        // Form is valid, submit
        this.submit();
    });

    // Handle form reset
    document.getElementById('resetBtn').addEventListener('click', function() {
        studentSection.style.display = 'none';
        subjectsSection.style.display = 'none';
        subjectsTableBody.innerHTML = '';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save All Results';
        ajaxAlert.classList.add('d-none');
    });
});
</script>

@endsection
