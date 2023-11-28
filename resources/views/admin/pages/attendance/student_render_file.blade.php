<table id="student-select" class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Total present</th>
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
      <td>28</td>
      <td><button type="button" value="1" data-id="{{$student->id}}" id="attendance-button" class="btn btn-success">Present</button></td>
    </tr>
    @endforeach

   
  </tbody>
</table>