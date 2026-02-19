<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    private int $schoolId = 1;

    public function index(Request $request)
    {
        $type   = $request->type ?? 'all';   // all | teacher | student
        $status = $request->status ?? 'all';

        $query = LeaveRequest::with('leavable', 'approvedBy')
                             ->where('school_id', $this->schoolId)
                             ->latest();

        if ($type === 'teacher') $query->where('leavable_type', Teacher::class);
        if ($type === 'student') $query->where('leavable_type', Student::class);
        if ($status !== 'all')   $query->where('status', $status);

        $leaves   = $query->paginate(25);
        $pending  = LeaveRequest::where('school_id', $this->schoolId)->where('status', 'pending')->count();

        return view('admin.pages.leave.index', compact('leaves', 'type', 'status', 'pending'));
    }

    public function create()
    {
        $teachers = Teacher::all();
        $students = Student::all();
        return view('admin.pages.leave.create', compact('teachers', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'leavable_type' => 'required|in:teacher,student',
            'leavable_id'   => 'required|integer',
            'leave_type'    => 'required|in:sick,casual,annual,emergency,maternity,other',
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'reason'        => 'required|string',
        ]);

        $modelClass = $data['leavable_type'] === 'teacher' ? Teacher::class : Student::class;

        LeaveRequest::create([
            'school_id'    => $this->schoolId,
            'leavable_type'=> $modelClass,
            'leavable_id'  => $data['leavable_id'],
            'leave_type'   => $data['leave_type'],
            'from_date'    => $data['from_date'],
            'to_date'      => $data['to_date'],
            'total_days'   => \Carbon\Carbon::parse($data['from_date'])->diffInDays($data['to_date']) + 1,
            'reason'       => $data['reason'],
            'status'       => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request submitted.');
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Leave rejected.');
    }

    public function destroy(LeaveRequest $leave)
    {
        $leave->delete();
        return back()->with('success', 'Leave request deleted.');
    }
}
