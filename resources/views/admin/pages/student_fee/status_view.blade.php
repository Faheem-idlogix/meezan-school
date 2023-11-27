<!-- status_view.blade.php -->
<td id="status-{{ $student->student_fee_id }}" class="fee-status" style="color: white">
    @if ($student->status == 'paid')
        <span class="badge bg-success">{{ $student->status }}</span>
    @elseif ($student->status == 'unpaid')
        <span class="badge bg-danger">{{ $student->status }}</span>
    @elseif ($student->status == 'pending')
        <span class="badge bg-warning">{{ $student->status }}</span>
    @endif
    {{ $student->status }}
</td>
