@extends('admin.layout.master')
@section('css')
<style>
  /* Profile header */
  .profile-header{background:linear-gradient(135deg,#4154f1,#2c3eaa);border-radius:12px;color:#fff;padding:2rem;position:relative;overflow:hidden}
  .profile-header::after{content:'';position:absolute;top:-40%;right:-10%;width:300px;height:300px;background:rgba(255,255,255,.06);border-radius:50%}
  .profile-avatar{width:90px;height:90px;border-radius:50%;border:3px solid rgba(255,255,255,.3);object-fit:cover}
  .profile-avatar-placeholder{width:90px;height:90px;border-radius:50%;border:3px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;background:rgba(255,255,255,.15)}
  .profile-badge{display:inline-block;padding:.25rem .7rem;border-radius:20px;font-size:.72rem;font-weight:600}

  /* Info list */
  .info-list .info-item{display:flex;padding:.6rem 0;border-bottom:1px solid #f0f0f0}
  .info-list .info-item:last-child{border-bottom:none}
  .info-list .info-label{min-width:140px;font-size:.8rem;color:#6c757d;font-weight:600}
  .info-list .info-value{font-size:.85rem;font-weight:500;color:#012970}

  /* Stat pill */
  .stat-pill{text-align:center;padding:.7rem .5rem;border-radius:8px}
  .stat-pill .sp-val{font-size:1.3rem;font-weight:700}
  .stat-pill .sp-lbl{font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:#6c757d}

  /* Mini chart */
  .att-mini-chart{position:relative;height:180px;width:100%}

  /* Timeline */
  .timeline-item{position:relative;padding-left:28px;padding-bottom:1rem;border-left:2px solid #e9ecef}
  .timeline-item:last-child{border-left-color:transparent;padding-bottom:0}
  .timeline-item .tl-dot{position:absolute;left:-7px;top:3px;width:12px;height:12px;border-radius:50%;border:2px solid #fff}
  .timeline-item .tl-date{font-size:.72rem;color:#6c757d}
  .timeline-item .tl-text{font-size:.82rem;font-weight:500;color:#012970}
</style>
@endsection

@section('content')
<main id="main" class="main">

  <div class="pagetitle d-flex align-items-center justify-content-between">
    <div>
      <h1>Student Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('student.index') }}">Students</a></li>
          <li class="breadcrumb-item active">{{ $student->student_name }}</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('student.edit', $student) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill me-1"></i>Edit</a>
      <a href="{{ route('student.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
  </div>

  <section class="section">

    {{-- ══════════ PROFILE HEADER ══════════ --}}
    <div class="profile-header mb-4">
      <div class="d-flex align-items-center gap-3 position-relative" style="z-index:1">
        @if($student->student_image)
          <img src="{{ asset('img/students/'.$student->student_image) }}" class="profile-avatar" alt="{{ $student->student_name }}">
        @else
          <div class="profile-avatar-placeholder">{{ strtoupper(substr($student->student_name, 0, 1)) }}</div>
        @endif
        <div>
          <h3 class="mb-1">{{ $student->student_name }}</h3>
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="profile-badge" style="background:rgba(255,255,255,.2)">
              <i class="bi bi-building me-1"></i>{{ $student->classroom->class_name ?? 'N/A' }}
            </span>
            <span class="profile-badge" style="background:{{ $student->student_status === 'active' ? 'rgba(25,135,84,.8)' : 'rgba(253,126,20,.8)' }}">
              {{ ucfirst($student->student_status ?? 'N/A') }}
            </span>
            @if($student->gender)
            <span class="profile-badge" style="background:rgba(255,255,255,.15)">
              <i class="bi bi-{{ $student->gender === 'male' ? 'gender-male' : 'gender-female' }} me-1"></i>{{ ucfirst($student->gender) }}
            </span>
            @endif
          </div>
          <div class="mt-1 small" style="opacity:.8">
            <i class="bi bi-envelope me-1"></i>{{ $student->student_email ?? '—' }}
            @if($student->contact_no)
              <span class="ms-3"><i class="bi bi-telephone me-1"></i>{{ $student->contact_no }}</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ══════════ QUICK STATS ══════════ --}}
    <div class="row g-3 mb-4">
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(25,135,84,.08)">
          <div class="sp-val text-success">{{ $attendanceRate }}%</div>
          <div class="sp-lbl">Attendance Rate</div>
        </div>
      </div>
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(65,84,241,.08)">
          <div class="sp-val text-primary">{{ $presentCount }}</div>
          <div class="sp-lbl">Days Present</div>
        </div>
      </div>
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(220,53,69,.08)">
          <div class="sp-val text-danger">{{ $absentCount }}</div>
          <div class="sp-lbl">Days Absent</div>
        </div>
      </div>
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(25,135,84,.08)">
          <div class="sp-val text-success">{{ number_format($paidFees) }}</div>
          <div class="sp-lbl">Fee Paid</div>
        </div>
      </div>
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(253,126,20,.08)">
          <div class="sp-val text-warning">{{ number_format($pendingFees) }}</div>
          <div class="sp-lbl">Fee Pending</div>
        </div>
      </div>
      <div class="col-xl col-md-4 col-6">
        <div class="stat-pill" style="background:rgba(111,66,193,.08)">
          <div class="sp-val" style="color:#6f42c1">{{ $examGroups->count() }}</div>
          <div class="sp-lbl">Exams Taken</div>
        </div>
      </div>
    </div>

    <div class="row g-3">

      {{-- ══════════ LEFT COL — PERSONAL + ATTENDANCE ══════════ --}}
      <div class="col-lg-5">

        {{-- Personal Information --}}
        <div class="card mb-3">
          <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Personal Information</h5></div>
          <div class="card-body info-list">
            <div class="info-item"><span class="info-label">Full Name</span><span class="info-value">{{ $student->student_name }}</span></div>
            <div class="info-item"><span class="info-label">Father's Name</span><span class="info-value">{{ $student->father_name ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">CNIC</span><span class="info-value">{{ $student->student_cnic ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Date of Birth</span><span class="info-value">{{ $student->student_dob ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Gender</span><span class="info-value">{{ ucfirst($student->gender ?? '—') }}</span></div>
            <div class="info-item"><span class="info-label">Contact</span><span class="info-value">{{ $student->contact_no ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">WhatsApp</span><span class="info-value">{{ $student->whatsapp_number ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Email</span><span class="info-value">{{ $student->student_email ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Admission Date</span><span class="info-value">{{ $student->student_admission_date ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Class</span><span class="info-value">{{ $student->classroom->class_name ?? '—' }}</span></div>
            <div class="info-item"><span class="info-label">Status</span>
              <span class="info-value">
                <span class="badge {{ $student->student_status === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($student->student_status ?? 'N/A') }}</span>
              </span>
            </div>
          </div>
        </div>

        {{-- Attendance Chart --}}
        <div class="card mb-3">
          <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-clipboard-data me-2 text-success"></i>Attendance Trend (6 Months)</h5></div>
          <div class="card-body">
            <div class="row g-2 mb-3">
              <div class="col-3">
                <div class="text-center"><div class="fw-bold text-success">{{ $presentCount }}</div><div class="text-muted" style="font-size:.7rem">Present</div></div>
              </div>
              <div class="col-3">
                <div class="text-center"><div class="fw-bold text-danger">{{ $absentCount }}</div><div class="text-muted" style="font-size:.7rem">Absent</div></div>
              </div>
              <div class="col-3">
                <div class="text-center"><div class="fw-bold text-warning">{{ $leaveCount }}</div><div class="text-muted" style="font-size:.7rem">Leave</div></div>
              </div>
              <div class="col-3">
                <div class="text-center"><div class="fw-bold" style="color:#0dcaf0">{{ $lateCount }}</div><div class="text-muted" style="font-size:.7rem">Late</div></div>
              </div>
            </div>
            <div class="att-mini-chart">
              <canvas id="attChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ RIGHT COL — RESULTS + FEES + TIMELINE ══════════ --}}
      <div class="col-lg-7">

        {{-- Exam Results --}}
        <div class="card mb-3">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Exam Results</h5>
            <span class="badge bg-primary">{{ $examGroups->count() }} exams</span>
          </div>
          <div class="card-body">
            @forelse($examGroups as $examName => $results)
              <div class="mb-3">
                <h6 class="fw-bold" style="font-size:.88rem;color:#012970">
                  <i class="bi bi-mortarboard me-1"></i>{{ $examName }}
                  @php
                    $totalMarks = $results->sum('total_marks');
                    $obtainedMarks = $results->sum('obtained_marks');
                    $pct = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;
                  @endphp
                  <span class="badge {{ $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} ms-2">{{ $pct }}%</span>
                </h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover mb-0" style="font-size:.82rem">
                    <thead class="table-light">
                      <tr><th>Subject</th><th class="text-center">Total</th><th class="text-center">Obtained</th><th class="text-center">%</th><th>Grade</th></tr>
                    </thead>
                    <tbody>
                      @foreach($results as $r)
                      @php
                        $sp = $r->total_marks > 0 ? round(($r->obtained_marks / $r->total_marks) * 100, 1) : 0;
                        $grade = $sp >= 90 ? 'A+' : ($sp >= 80 ? 'A' : ($sp >= 70 ? 'B' : ($sp >= 60 ? 'C' : ($sp >= 50 ? 'D' : 'F'))));
                      @endphp
                      <tr>
                        <td class="fw-semibold">{{ $r->subject->subject_name ?? '—' }}</td>
                        <td class="text-center">{{ $r->total_marks }}</td>
                        <td class="text-center">{{ $r->obtained_marks }}</td>
                        <td class="text-center">
                          <span class="badge {{ $sp >= 50 ? 'bg-success' : 'bg-danger' }}">{{ $sp }}%</span>
                        </td>
                        <td><span class="fw-bold {{ $sp >= 50 ? 'text-success' : 'text-danger' }}">{{ $grade }}</span></td>
                      </tr>
                      @endforeach
                      <tr class="table-active">
                        <td class="fw-bold">Total</td>
                        <td class="text-center fw-bold">{{ $totalMarks }}</td>
                        <td class="text-center fw-bold">{{ $obtainedMarks }}</td>
                        <td class="text-center fw-bold"><span class="badge bg-primary">{{ $pct }}%</span></td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            @empty
              <div class="text-center py-4 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2" style="color:#c5cde8"></i>
                <small>No exam results found</small>
              </div>
            @endforelse
          </div>
        </div>

        {{-- Fee History --}}
        <div class="card mb-3">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Fee History</h5>
            <div class="d-flex gap-2">
              <span class="badge bg-success">Paid: {{ number_format($paidFees) }}</span>
              <span class="badge bg-warning text-dark">Pending: {{ number_format($pendingFees) }}</span>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0" style="font-size:.82rem">
                <thead class="table-light">
                  <tr><th>Month</th><th>Total Fee</th><th>Received</th><th>Status</th><th>Voucher #</th></tr>
                </thead>
                <tbody>
                  @forelse($student->fees as $fee)
                  <tr>
                    <td class="fw-semibold">{{ $fee->fee_month ?? '—' }}</td>
                    <td>{{ number_format($fee->total_fee) }}</td>
                    <td>{{ number_format($fee->received_payment_fee ?? 0) }}</td>
                    <td>
                      @if($fee->status == 'paid')
                        <span class="badge bg-success">Paid</span>
                      @elseif($fee->status == 'unpaid')
                        <span class="badge bg-danger">Unpaid</span>
                      @elseif($fee->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($fee->status ?? '—') }}</span>
                      @endif
                    </td>
                    <td class="text-muted">{{ $fee->voucher_no ?? '—' }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="5" class="text-center text-muted py-3">No fee records found</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Attendance Log (recent 30) --}}
        <div class="card mb-3">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0"><i class="bi bi-calendar2-check me-2 text-info"></i>Recent Attendance</h5>
            <span class="badge bg-info text-dark">{{ $totalAttendance }} total records</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive" style="max-height:320px;overflow-y:auto">
              <table class="table table-sm table-hover mb-0" style="font-size:.82rem">
                <thead class="table-light" style="position:sticky;top:0;z-index:1">
                  <tr><th>#</th><th>Date</th><th>Status</th><th>Class</th></tr>
                </thead>
                <tbody>
                  @forelse($student->attendance->take(30) as $i => $att)
                  <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $att->date }}</td>
                    <td>
                      @php
                        $statusMap = ['0' => 'Unmarked', '1' => 'Present', '2' => 'Leave', '3' => 'Absent'];
                        $statusLabel = $statusMap[$att->attendance] ?? $att->attendance;
                        $statusClass = match((string)$att->attendance) {
                            '1' => 'bg-success',
                            '3' => 'bg-danger',
                            '2' => 'bg-warning text-dark',
                            default => 'bg-info text-dark',
                        };
                      @endphp
                      <span class="badge {{ $statusClass }}">
                        {{ $statusLabel }}
                      </span>
                    </td>
                    <td class="text-muted">{{ $att->classRoom->class_name ?? '—' }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">No attendance records found</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

  </section>
</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
  const data = @json($monthlyAtt);
  const ctx = document.getElementById('attChart');
  if(!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(r => r.label),
      datasets: [
        { label: 'Present', data: data.map(r => r.present), backgroundColor: 'rgba(25,135,84,.7)', borderRadius: 4, barPercentage: 0.6 },
        { label: 'Absent',  data: data.map(r => r.absent),  backgroundColor: 'rgba(220,53,69,.7)', borderRadius: 4, barPercentage: 0.6 },
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position:'bottom', labels:{ usePointStyle:true, padding:10, font:{size:11} } } },
      scales: {
        y: { beginAtZero:true, ticks:{ stepSize:1, font:{size:10} }, grid:{color:'rgba(0,0,0,.04)'} },
        x: { ticks:{ font:{size:10} }, grid:{display:false} }
      }
    }
  });
})();
</script>
@endsection
