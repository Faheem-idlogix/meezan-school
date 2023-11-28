@extends('admin.layout.master')
@section('css')
<style>
  .bi-x-circle{
  color: red;
  margin-left: 10px;
}
.bi-check-circle{
  color: green;
  margin-left: 10px;
}
.bi-check-circle-fill{
  color: green;
  margin-left: 10px;
}
  </style>
@endsection
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">
            <div id="flash-messages-container" class="toast">
              <!-- Flash messages will be dynamically inserted here -->
          </div>

               <!-- Customers Card -->
               <div class="col-xxl-4 col-xl-12">

                <div class="card info-card customers-card">
  
                  <div class="card-body">
                    <h5 class="card-title">Students</h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <h6>{{$totalStudents}}</h6>  
                      </div>
                    </div>
  
                  </div>
                </div>
  
              </div><!-- End Customers Card -->

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

          

                <div class="card-body">
                  <h5 class="card-title">Classes</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-journal-text"></i>
                    </div>
                    <div class="ps-3">
                      <h6>{{$classrooms}}</h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="card-body">
                  <h5 class="card-title">Total Fee</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ps-3">
                      <h6>{{$totalFee}}</h6>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

         

            <!-- Reports -->
           <!-- End Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

          

                <div class="card-body">
                  <h5 class="card-title">Students Fee Report <span>| This Month</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Class</th>
                        <th scope="col">Fee</th>
                        <th scope="col">Status</th>
                        <th scope="col">Recieved Fee</th>
                        <th scope="col">Enter Amount</th>
                        <th scope="col">Action</th>

                      </tr>
                    </thead>
                    @php
                      $sr_no = 1;
                    @endphp
                    <tbody>
                      @foreach ($students as $student)
                      <tr id="row-{{ $student->student_fee_id }}">
                        <th scope="row"><a href="#">{{$sr_no++}}</a></th>
                        <td>{{$student->student->student_name ?? ''}}</td>
                        <td>{{$student->student->classroom->class_name ?? ''}}</td>
                        <td>{{$student->total_fee}}</td>
                        <td class="fee-status"style="color: white">
                           @if ($student->status == 'paid')
                          <span class="badge bg-success">{{ $student->status }}</span>
                          @elseif ($student->status == 'unpaid')
                              <span class="badge bg-danger">{{ $student->status }}</span>
                              @elseif ($student->status == 'pending')
                              <span class="badge bg-warning">{{ $student->status }}</span>
                          @endif
                          {{$student->status}}
                        </td>
                        <td class="recieved_fee">{{$student->received_payment_fee ?? '' }}</td>
                       <td> <input type="text" name="recieved_amount_fee" class="recieved-amount" data-student-id="{{$student->student_fee_id}}" oninput="validateNumber(this)"></td>
                        <td>
                          <div class="btn-group">
                        <a  id='edit-fee' data-id="{{$student->student_fee_id}}"> <i class="bi bi-x-circle edit-fee"></i></a>
                        <a  id='add-fee' data-id="{{$student->student_fee_id}}"> <i class="bi bi-check-circle add-fee"></i></a>
                        <a  id='submit-fee' data-id="{{$student->student_fee_id}}"><i class="bi bi-check-circle-fill submit-fee"></i></a>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                     
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

          

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        {{-- <div class="col-lg-4">

          <!-- Recent Activity -->
         <!-- End Recent Activity -->

          <!-- Budget Report -->
          <!-- End Budget Report -->

          <!-- Website Traffic -->
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body pb-0">
              <h5 class="card-title">Website Traffic <span>| Today</span></h5>

              <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#trafficChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '5%',
                      left: 'center'
                    },
                    series: [{
                      name: 'Access From',
                      type: 'pie',
                      radius: ['40%', '70%'],
                      avoidLabelOverlap: false,
                      label: {
                        show: false,
                        position: 'center'
                      },
                      emphasis: {
                        label: {
                          show: true,
                          fontSize: '18',
                          fontWeight: 'bold'
                        }
                      },
                      labelLine: {
                        show: false
                      },
                      data: [{
                          value: 1048,
                          name: 'Search Engine'
                        },
                        {
                          value: 735,
                          name: 'Direct'
                        },
                        {
                          value: 580,
                          name: 'Email'
                        },
                        {
                          value: 484,
                          name: 'Union Ads'
                        },
                        {
                          value: 300,
                          name: 'Video Ads'
                        }
                      ]
                    }]
                  });
                });
              </script>

            </div>
          </div><!-- End Website Traffic -->

          <!-- News & Updates Traffic -->
         <!-- End News & Updates -->

        </div><!-- End Right side columns --> --}}

      </div>
    </section>

  </main><!-- End #main -->
@endsection
@section('script')

<script>
      $(document).on('click', '#edit-fee', function(e) {
          e.preventDefault();
          var studentId = $(this).data('id');
          //var fee = $('.recieved-amount[data-student-id="' + studentId + '"]').val();  
          $.ajax({
              url: "{{ route('edit_fee') }}",
              type: 'post',
              data: { id: studentId, _token: '{{ csrf_token() }}' },
              success: function(response) {
                $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
              $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);
                  // Handle the success response, if needed
              },
              error: function(error) {
                  // Handle the error response, if needed
              }
          });
      });

      $(document).on('click', '#add-fee', function(e) {
          e.preventDefault();
          var studentId = $(this).data('id');
          var fee = $('.recieved-amount[data-student-id="' + studentId + '"]').val();  
               
          $.ajax({
              url: "{{ route('add_fee') }}",
              type: 'post',
              data: { id: studentId, fee: fee, _token: '{{ csrf_token() }}' },
              success: function(response) {
              //  console.log(response);
              $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
              $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);

           
              },
              error: function(error) {
                  // Handle the error response, if needed
              }
          });
      });

      $(document).on('click', '#submit-fee', function(e) {
          e.preventDefault();
          var studentId = $(this).data('id');
         // var fee = $('.recieved-amount[data-student-id="' + studentId + '"]').val();  
               
          $.ajax({
              url: "{{ route('add_full_fee') }}",
              type: 'post',
              data: { id: studentId, _token: '{{ csrf_token() }}' },
              success: function(response) {
              $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
              $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);
              },
              error: function(error) {
                  // Handle the error response, if needed
              }
          });
      });
</script>
@endsection