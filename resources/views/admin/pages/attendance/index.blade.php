@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>Attendance</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Attendance</li>
        </ol>
      </nav>
    </div>
      <div class="col-lg-2">
        {{-- <a href="{{ route('class.create') }}" class="btn btn-primary">Add Class</a> --}}
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

       <div class="col-lg-12">
        <div class="card" >
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <label for="inputDate" class="col-form-label">Select Class<sup>*</sup></label>
                        <select class="form-select @error('class_room_id') is-invalid @enderror" value="{{ old('class_room_id') }}"  name="class_room_id" id="class-room-select"  aria-label="Default select example">
                          @error('class_room_id')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                           @enderror
                            @foreach($class_room as $classroom)
                              <option value="{{$classroom->id}}">{{$classroom->class_name .' '. $classroom->section_name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-lg-6">
                            <label for="inputText" class="col-form-label">Select Date<sup>*</sup></label>
                              <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                </div>
            </div>
        </div>
        <div class="card">
          <div class="card-body">
            {{-- Bulk action buttons --}}
            <div class="d-flex gap-2 mb-3 mt-2">
              <button type="button" id="mark-all-present" class="btn btn-success btn-sm"><i class="bi bi-check-all me-1"></i>Mark All Present</button>
              <button type="button" id="mark-all-absent" class="btn btn-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Mark All Absent</button>
              <div class="ms-auto">
                <span class="badge bg-success">Present = <i class="bi bi-check-circle"></i></span>
                <span class="badge bg-warning text-dark ms-1">Leave = <i class="bi bi-clock"></i></span>
                <span class="badge bg-danger ms-1">Absent = <i class="bi bi-x-circle"></i></span>
                <span class="badge bg-primary ms-1">Click to cycle</span>
              </div>
            </div>
           

            <!-- Table with stripped rows -->
            <table id="student-select" class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Class</th>
                  <th scope="col">Attendance</th>
                </tr>
              </thead>
              <tbody>
                @php
                    $sr_no = 1;
                @endphp
                @foreach ($students as $student)
                <tr id="row-{{ $student->id }}">
                  <th >{{$sr_no++}}</th>
                  <td>{{$student->student_name}}</td>
                  <td>{{$student->classroom->class_name.' '.$student->classroom->section_name}}</td>
                  {{-- <button type="button" value="1" data-id="{{$student->id}}" id="attendance-button" class="btn btn-danger">Absent</button> --}}
                  @if (isset($attendanceData[$student->id]))
                    @if ($attendanceData[$student->id]['status'] == 0)
                    <td >
                      <button type="button" value="0" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-primary">Mark Attendance</button>
                    </td>
                  @elseif ($attendanceData[$student->id]['status'] == 1)
                    <td >
                      <button type="button" value="1" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-success">Present</button>
                    </td>
                  @elseif ($attendanceData[$student->id]['status'] == 2)
                    <td >
                      <button type="button" value="2" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-warning">Leave</button>
                    </td>
                  @elseif ($attendanceData[$student->id]['status'] == 3)
                    <td >
                      <button type="button" value="3" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-danger">Absent</button>
                    </td>
                  @else
                    <td >
                      <button type="button" value="0" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-primary">Mark Attendance</button>
                    </td>
                  @endif
                @else
                  <td >
                    <button type="button" value="0" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-primary">Mark Attendance</button>
                  </td>
                @endif
                                     
                </tr>
                @endforeach

               
              </tbody>
            </table>
            <!-- End Table with stripped rows -->

          </div>
        </div>

      </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
@endsection
@section('script')
<script>
    $(document).ready(function () {
        // When class selection changes
        $('#class-room-select').change(function () {
            var classId = $(this).val();
            var date =  $('#date').val();           
            var data = {
            classId: classId,
            date: date
             };
            $.ajax({
                url: '/attendance', // Replace with your route
                type: 'GET',
                data: data,
                success: function (data) {
                    console.log(data);
                    // Update the student dropdown with the new data
                    $('#student-select').html(data.studentHtml);
                },
                error: function (error) {
                    console.error('Error fetching students:', error);
                }
            });
        });

        $('#date').change(function () {
            var date = $(this).val();
            var classId =  $('#class-room-select').val();  
            var data = {
            classId: classId,
            date: date
             };
            $.ajax({
                url: '/attendance', // Replace with your route
                type: 'GET',
                data: data,
                success: function (data) {
                    console.log(data);
                    // Update the student dropdown with the new data
                    $('#student-select').html(data.studentHtml);
                },
                error: function (error) {
                    console.error('Error fetching students:', error);
                }
            });
        });

        // ── Bulk actions ──
        $(document).on('click', '#mark-all-present', function(e) {
            e.preventDefault();
            $('#student-select tbody tr').each(function() {
                var btn = $(this).find('#attendance-button');
                if (btn.length && btn.val() != '1') {
                    markAttendance(btn, '1');
                }
            });
        });

        $(document).on('click', '#mark-all-absent', function(e) {
            e.preventDefault();
            $('#student-select tbody tr').each(function() {
                var btn = $(this).find('#attendance-button');
                if (btn.length && btn.val() != '3') {
                    markAttendance(btn, '3');
                }
            });
        });

        // ── Single attendance button click ──
        // Cycle: 0 (unmarked) → 1 (present) → 2 (leave) → 3 (absent) → 1 (present)
        $(document).on('click', '#attendance-button', function(e) {
          e.preventDefault();
          var clickedButton = $(this);
          var current = parseInt(clickedButton.val());
          var next;
          if (current === 0) next = 1;       // unmarked → present
          else if (current === 1) next = 2;  // present → leave
          else if (current === 2) next = 3;  // leave → absent
          else next = 1;                     // absent → present

          markAttendance(clickedButton, next);
        });

        function markAttendance(btn, newVal) {
          var studentId = btn.data('id');
          var selectedClassId = $('#class-room-select').val();
          var date = $('#date').val();
          var data = {
            studentId: studentId,
            selectedClassId: selectedClassId,
            date: date,
            attendance: newVal,
            _token: '{{ csrf_token() }}'
          };
          
          $.ajax({
                url: '/attendance_store',
                type: 'POST',
                data: data,
                success: function (data) {
                    // Update button to reflect the saved state
                    btn.val(newVal);
                    btn.removeClass('btn-primary btn-success btn-warning btn-danger');
                    if (newVal == 1) {
                        btn.addClass('btn-success');
                        btn.text('Present');
                    } else if (newVal == 2) {
                        btn.addClass('btn-warning');
                        btn.text('Leave');
                    } else if (newVal == 3) {
                        btn.addClass('btn-danger');
                        btn.text('Absent');
                    } else {
                        btn.addClass('btn-primary');
                        btn.text('Mark Attendance');
                    }
                },
                error: function (error) {
                    console.error('Error saving attendance:', error);
                }
            });
        }

    });
</script>
@endsection
