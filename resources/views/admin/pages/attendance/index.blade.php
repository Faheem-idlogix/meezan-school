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
            {{-- <h5 class="card-title">Table with stripped rows</h5> --}}
           

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
                      <button type="button" value="1" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-warning">Leave</button>
                    </td>
                  @elseif ($attendanceData[$student->id]['status'] == 3)
                    <td >
                      <button type="button" value="1" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-danger">Absent</button>
                    </td>
                  @else
                    <td >
                      <button type="button" value="1" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-danger">Absent</button>
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

        $(document).on('click', '#attendance-button', function(e) {
          e.preventDefault();
          var clickedButton = $(this);
          var attendance = $(this).val();
          var studentId = $(this).data('id');
          var selectedClassId = $('#class-room-select').val();
          var date = $('#date').val();
          var data = {
            studentId: studentId,
            selectedClassId: selectedClassId,
            date: date,
            attendance : attendance,
            _token: '{{ csrf_token() }}'
          };
          
          $.ajax({
                url: '/attendance_store', // Replace with your route
                type: 'POST',
                data: data,
                success: function (data) {
                if(attendance == 0){
                      clickedButton.removeClass('btn-outline-secondary');
                      clickedButton.addClass('btn-danger');
                      clickedButton.val('1');
                      clickedButton.text('Absent');
                    }
                    if(attendance == 1){
                      clickedButton.removeClass('btn-danger');
                      clickedButton.addClass('btn-success');
                      clickedButton.val('2');
                      clickedButton.text('Present');
                    }
                    if(attendance == 2){
                      clickedButton.removeClass('btn-success');
                      clickedButton.addClass('btn-warning');
                      clickedButton.val('3');
                      clickedButton.text('Leave');
                    }
                    if(attendance == 3){
                      clickedButton.removeClass('btn-warning');
                      clickedButton.addClass('btn-danger');
                      clickedButton.val('1');
                      clickedButton.text('Absent');
                    }
                },
                error: function (error) {
                    console.error('Error fetching students:', error);
                }
            });
        });

    });
</script>
@endsection
