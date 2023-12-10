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
     
      @if ($attendanceData[$student->id]['status'] == 1)
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
        <button type="button" value="1" data-id="{{ $student->id }}" id="attendance-button" class="btn btn-danger">Absent</button>
      </td>
    @endif
                         
    </tr>
    @endforeach

   
  </tbody>
</table>