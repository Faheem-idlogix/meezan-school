<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Sheet - {{ $exam->name }} - {{ $classRoom->class_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; color: #333; }

        .page-wrapper { max-width: 900px; margin: 30px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }

        /* Header */
        .school-header { background: linear-gradient(135deg, #1a5276, #2980b9); color: #fff; padding: 28px 32px; display: flex; align-items: center; gap: 20px; }
        .school-header img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; background: #fff; padding: 4px; }
        .school-header .info { flex: 1; }
        .school-header .info h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 4px; }
        .school-header .info p { font-size: .85rem; opacity: .9; }

        /* Exam Badge */
        .exam-badge { text-align: center; padding: 18px 32px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; }
        .exam-badge h2 { font-size: 1.15rem; font-weight: 600; color: #1a5276; margin-bottom: 4px; }
        .exam-badge span { font-size: .9rem; color: #6c757d; }

        /* Table */
        .table-wrap { padding: 24px 32px 32px; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        thead th { background: #1a5276; color: #fff; padding: 10px 14px; text-align: left; font-weight: 600; }
        thead th:first-child { border-radius: 6px 0 0 0; }
        thead th:last-child { border-radius: 0 6px 0 0; }
        tbody tr { border-bottom: 1px solid #eee; transition: background .15s; }
        tbody tr:hover { background: #f1f7fd; }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 10px 14px; }
        tbody td:first-child { font-weight: 600; color: #888; text-align: center; }

        .empty-msg { text-align: center; padding: 40px; color: #aaa; font-size: .95rem; }

        /* Footer */
        .print-footer { text-align: center; padding: 14px; font-size: .75rem; color: #aaa; border-top: 1px solid #eee; }

        /* Toolbar (no-print) */
        .toolbar { max-width: 900px; margin: 20px auto 0; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .toolbar a, .toolbar button { display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px; border-radius: 8px; font-size: .875rem; font-weight: 500; text-decoration: none; cursor: pointer; border: none; transition: all .2s; }
        .btn-print { background: #1a5276; color: #fff; }
        .btn-print:hover { background: #154360; }
        .btn-back { background: #e9ecef; color: #495057; }
        .btn-back:hover { background: #dee2e6; }

        /* Column Picker */
        .col-picker { max-width: 900px; margin: 12px auto 0; background: #fff; border-radius: 10px; padding: 12px 18px; box-shadow: 0 2px 10px rgba(0,0,0,.06); display: flex; flex-wrap: wrap; gap: 6px 16px; align-items: center; }
        .col-picker .picker-label { font-size: .8rem; font-weight: 600; color: #1a5276; margin-right: 8px; white-space: nowrap; }
        .col-picker label { display: inline-flex; align-items: center; gap: 5px; font-size: .82rem; color: #495057; cursor: pointer; user-select: none; padding: 4px 10px; border-radius: 6px; transition: background .15s; }
        .col-picker label:hover { background: #f0f4f8; }
        .col-picker input[type="checkbox"] { accent-color: #1a5276; width: 15px; height: 15px; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .col-picker { display: none !important; }
            .page-wrapper { margin: 0; box-shadow: none; border-radius: 0; }
            .school-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">&#128438; Print</button>
        <a class="btn-back" href="{{ route('exam-schedules.index', ['exam_id' => $exam->id, 'class_room_id' => $classRoom->id]) }}">&#8592; Back</a>
    </div>

    <div class="col-picker">
        <span class="picker-label">Show Columns:</span>
        <label><input type="checkbox" data-col="0" checked> #</label>
        <label><input type="checkbox" data-col="1" checked> Subject</label>
        <label><input type="checkbox" data-col="2" checked> Date</label>
        <label><input type="checkbox" data-col="3" checked> Start Time</label>
        <label><input type="checkbox" data-col="4" checked> End Time</label>
        <label><input type="checkbox" data-col="5" checked> Room</label>
        <label><input type="checkbox" data-col="6" checked> Total Marks</label>
        <label><input type="checkbox" data-col="7" checked> Pass Marks</label>
    </div>

    <div class="page-wrapper">
        {{-- School Header --}}
        <div class="school-header">
            <img src="{{ school_logo() }}" alt="Logo">
            <div class="info">
                <h1>{{ setting('school_name', 'School Name') }}</h1>
                @if(setting('school_address'))<p>{{ setting('school_address') }}@if(setting('school_phone')) &nbsp;|&nbsp; {{ setting('school_phone') }}@endif</p>@endif
            </div>
        </div>

        {{-- Exam Info --}}
        <div class="exam-badge">
            <h2>{{ $exam->name }}</h2>
            <span>Class: {{ $classRoom->class_name }}</span>
        </div>

        {{-- Schedule Table --}}
        <div class="table-wrap">
            @if($schedules->isEmpty())
                <p class="empty-msg">No schedule entries found for this exam and class.</p>
            @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Room</th>
                        <th>Total Marks</th>
                        <th>Pass Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $key => $schedule)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $schedule->subject->subject_name ?? '—' }}</td>
                        <td>{{ $schedule->exam_date->format('d M Y') }}</td>
                        <td>{{ $schedule->start_time ?? '—' }}</td>
                        <td>{{ $schedule->end_time ?? '—' }}</td>
                        <td>{{ $schedule->room ?? '—' }}</td>
                        <td>{{ $schedule->total_marks ?? '—' }}</td>
                        <td>{{ $schedule->passing_marks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="print-footer">Generated on {{ now()->format('d M Y, h:i A') }}</div>
    </div>

    <script>
    document.querySelectorAll('.col-picker input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const col = this.dataset.col;
            const show = this.checked;
            document.querySelectorAll(`table th:nth-child(${+col + 1}), table td:nth-child(${+col + 1})`).forEach(cell => {
                cell.style.display = show ? '' : 'none';
            });
        });
    });
    </script>
</body>
</html>
