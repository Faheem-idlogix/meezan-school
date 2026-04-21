<?php

namespace App\Http\Controllers;

use App\Models\AdmissionEnquiry;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeInstallment;
use App\Models\FeeInstallmentPlan;
use App\Models\Notice;
use App\Models\StudentBehavior;
use App\Models\Teacher;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = auth()->user()->role ?? 'admin';

        if ($role === 'teacher') {
            return $this->teacherDashboard();
        }

        if ($role === 'accountant') {
            return $this->accountantDashboard();
        }

        // Default admin dashboard
        return $this->adminDashboard();
    }

    /**
     * Resolve a date-range from the ?period / ?from / ?to query string.
     */
    private function resolvePeriod(): array
    {
        $period = request('period', 'this_month');

        switch ($period) {
            case 'last_month':
                $from  = now()->subMonth()->startOfMonth();
                $to    = now()->subMonth()->endOfMonth();
                $label = $from->format('F Y');
                break;
            case '6_months':
                $from  = now()->subMonths(5)->startOfMonth();
                $to    = now()->endOfMonth();
                $label = $from->format('M Y') . ' – ' . $to->format('M Y');
                break;
            case 'last_year':
                $from  = now()->subYear()->startOfYear();
                $to    = now()->subYear()->endOfYear();
                $label = $from->format('Y');
                break;
            case 'this_year':
                $from  = now()->startOfYear();
                $to    = now()->endOfMonth();
                $label = $from->format('Y');
                break;
            case 'custom':
                $from  = request('from') ? Carbon::parse(request('from'))->startOfDay() : now()->startOfMonth();
                $to    = request('to') ? Carbon::parse(request('to'))->endOfDay() : now()->endOfDay();
                $label = $from->format('d M Y') . ' – ' . $to->format('d M Y');
                break;
            default: // this_month
                $period = 'this_month';
                $from   = now()->startOfMonth();
                $to     = now()->endOfMonth();
                $label  = now()->format('F Y');
                break;
        }

        return [$from, $to, $period, $label];
    }

    /**
     * Build an array of "F Y" month-strings that fall within the date range.
     */
    private function feeMonthsInRange(Carbon $from, Carbon $to): array
    {
        $months = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $months[] = $cursor->format('F Y');
            $cursor->addMonth();
        }
        return $months;
    }

    private function adminDashboard()
    {
        [$dateFrom, $dateTo, $periodKey, $periodLabel] = $this->resolvePeriod();
        $feeMonths = $this->feeMonthsInRange($dateFrom, $dateTo);

        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $classrooms    = ClassRoom::count();

        // ── Fee stats (filtered by period months, exclude deleted students) ──
        $feeQuery = StudentFee::whereIn('fee_month', $feeMonths)
                        ->whereHas('class_fee_voucher')
                        ->whereHas('student');

        $totalFee       = (clone $feeQuery)->sum('total_fee');
        $feeReceived    = (clone $feeQuery)->sum('received_payment_fee');
        $feeOutstanding = $totalFee - $feeReceived;
        $students       = StudentFee::with(['student.classroom'])
                    ->whereIn('fee_month', $feeMonths)
                    ->whereHas('class_fee_voucher')
                    ->whereHas('student')
                    ->orderByDesc('created_at')
                    ->paginate(15, ['*'], 'fee_page')
                    ->withQueryString();

        // ── Voucher-based finance stats (filtered by period via voucher_date OR created_at) ──
        $financeAll = Voucher::where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('voucher_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                  ->orWhere(function ($q2) use ($dateFrom, $dateTo) {
                      $q2->whereNull('voucher_date')
                         ->whereBetween('created_at', [$dateFrom, $dateTo]);
                  });
            })
            ->selectRaw("
                SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as total_expense,
                COUNT(*) as total_vouchers
            ")->first();

        $totalIncome   = $financeAll->total_income ?? 0;
        $totalExpense  = $financeAll->total_expense ?? 0;
        $totalVouchers = $financeAll->total_vouchers ?? 0;
        $profitLoss    = $totalIncome - $totalExpense;

        $monthIncome   = $totalIncome;
        $monthExpense  = $totalExpense;
        $monthVouchers = $totalVouchers;

        // ── Monthly chart data (build month buckets within selected range) ──
        $monthlyChart = collect();
        $cursor       = $dateFrom->copy()->startOfMonth();
        $chartEnd     = $dateTo->copy()->endOfMonth();

        while ($cursor->lte($chartEnd)) {
            $mStart = $cursor->copy()->startOfMonth();
            $mEnd   = $cursor->copy()->endOfMonth();

            $row = Voucher::where(function ($q) use ($mStart, $mEnd) {
                    $q->whereBetween('voucher_date', [$mStart->toDateString(), $mEnd->toDateString()])
                      ->orWhere(function ($q2) use ($mStart, $mEnd) {
                          $q2->whereNull('voucher_date')
                             ->whereBetween('created_at', [$mStart, $mEnd]);
                      });
                })
                ->selectRaw("
                    SUM(CASE WHEN type='income'  THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
                ")->first();

            $feeLabel = $cursor->format('F Y');
            $feeRow   = StudentFee::where('fee_month', $feeLabel)
                ->whereHas('student')
                ->selectRaw('SUM(received_payment_fee) as collected, SUM(total_fee) as billed')
                ->first();

            $monthlyChart->push([
                'label'     => $cursor->format('M Y'),
                'income'    => (float)($row->income ?? 0),
                'expense'   => (float)($row->expense ?? 0),
                'collected' => (float)($feeRow->collected ?? 0),
                'billed'    => (float)($feeRow->billed ?? 0),
            ]);

            $cursor->addMonth();
        }

        // ── Recent expenses (within period) ──
        $recentExpenses = Voucher::where('type', 'expense')
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('voucher_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                  ->orWhere(function ($q2) use ($dateFrom, $dateTo) {
                      $q2->whereNull('voucher_date')
                         ->whereBetween('created_at', [$dateFrom, $dateTo]);
                  });
            })
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $totalExams    = Exam::count();
        $recentNotices = Notice::where('is_active', 1)
                            ->orderByDesc('created_at')
                            ->take(5)
                            ->get();

        // ── Attendance Stats (within period, not just today) ──
        $attendanceRaw = Attendance::where(function ($q) use ($dateFrom, $dateTo) {
                // date column is string, try multiple formats
                $q->whereBetween('date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
                  ->orWhereBetween('date', [$dateFrom->format('d-m-Y'), $dateTo->format('d-m-Y')]);
            })
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN attendance = '1' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance = '3' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN attendance = '2' THEN 1 ELSE 0 END) as on_leave,
                SUM(CASE WHEN attendance = '0' THEN 1 ELSE 0 END) as late
            ")->first();

        $attendanceToday = [
            'total'   => (int)($attendanceRaw->total ?? 0),
            'present' => (int)($attendanceRaw->present ?? 0),
            'absent'  => (int)($attendanceRaw->absent ?? 0),
            'leave'   => (int)($attendanceRaw->on_leave ?? 0),
            'late'    => (int)($attendanceRaw->late ?? 0),
            'rate'    => $attendanceRaw->total > 0
                ? round(($attendanceRaw->present / $attendanceRaw->total) * 100, 1)
                : 0,
        ];
        $attendanceUnmarked = $totalStudents - $attendanceToday['total'];

        // ── Class-wise attendance breakdown (within period) ──
        $classAttendance = Attendance::where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
                  ->orWhereBetween('date', [$dateFrom->format('d-m-Y'), $dateTo->format('d-m-Y')]);
            })
            ->join('class_rooms', function ($join) {
                $join->on('attendances.class_room_id', '=', 'class_rooms.id')
                     ->whereNull('class_rooms.deleted_at');
            })
            ->selectRaw("
                class_rooms.class_name,
                COUNT(*) as total,
                SUM(CASE WHEN attendance = '1' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendance = '3' THEN 1 ELSE 0 END) as absent
            ")
            ->groupBy('class_rooms.id', 'class_rooms.class_name')
            ->get();

        // ══════════ SMART DASHBOARD — New Module Stats ══════════

        // Admission stats (safe — check table exists)
        $admissionStats = ['total' => 0, 'enquiry' => 0, 'test_scheduled' => 0, 'approved' => 0, 'enrolled' => 0, 'rejected' => 0];
        if (Schema::hasTable('admission_enquiries')) {
            $admissionStats = [
                'total'          => AdmissionEnquiry::count(),
                'enquiry'        => AdmissionEnquiry::where('status', 'enquiry')->count(),
                'test_scheduled' => AdmissionEnquiry::where('status', 'test_scheduled')->count(),
                'approved'       => AdmissionEnquiry::where('status', 'approved')->count(),
                'enrolled'       => AdmissionEnquiry::where('status', 'enrolled')->count(),
                'rejected'       => AdmissionEnquiry::where('status', 'rejected')->count(),
            ];
        }

        // Behavior stats
        $behaviorStats = ['positive' => 0, 'negative' => 0, 'neutral' => 0];
        if (Schema::hasTable('student_behaviors')) {
            $behaviorStats = [
                'positive' => StudentBehavior::where('type', 'positive')->count(),
                'negative' => StudentBehavior::where('type', 'negative')->count(),
                'neutral'  => StudentBehavior::where('type', 'neutral')->count(),
            ];
        }

        // Fee defaulters — students with overdue installments
        $feeDefaulters = collect();
        $overdueInstallments = 0;
        if (Schema::hasTable('fee_installments')) {
            $overdueInstallments = FeeInstallment::where('status', 'overdue')
                ->orWhere(function($q) { $q->where('status', 'pending')->where('due_date', '<', now()); })
                ->count();
            $feeDefaulters = FeeInstallmentPlan::whereHas('installments', function($q) {
                    $q->where('status', 'overdue')
                      ->orWhere(function($q2) { $q2->where('status', 'pending')->where('due_date', '<', now()); });
                })
                ->with(['student', 'installments'])
                ->take(10)
                ->get();
        }

        // Exam analytics — latest exam performance
        $examAnalytics = ['avg_percentage' => 0, 'total_results' => 0, 'pass_count' => 0, 'fail_count' => 0];
        $latestExam = Exam::latest()->first();
        if ($latestExam) {
            $results = ExamResult::where('exam_id', $latestExam->id)->get();
            $examAnalytics['total_results'] = $results->count();
            if ($results->count() > 0) {
                $examAnalytics['avg_percentage'] = round($results->avg(function($r) {
                    return ($r->total_marks ?? 100) > 0 ? ($r->obtain_marks / ($r->total_marks ?? 100)) * 100 : 0;
                }), 1);
                $examAnalytics['pass_count'] = $results->filter(function($r) {
                    return ($r->total_marks ?? 100) > 0 && ($r->obtain_marks / ($r->total_marks ?? 100)) >= 0.33;
                })->count();
                $examAnalytics['fail_count'] = $examAnalytics['total_results'] - $examAnalytics['pass_count'];
            }
        }

        return view('admin.dashboard.dashboard', compact(
            'totalStudents', 'totalTeachers', 'classrooms',
            'totalFee', 'feeReceived', 'feeOutstanding', 'students',
            'totalIncome', 'totalExpense', 'totalVouchers', 'profitLoss',
            'monthIncome', 'monthExpense', 'monthVouchers',
            'monthlyChart', 'recentExpenses',
            'totalExams', 'recentNotices',
            'attendanceToday', 'attendanceUnmarked', 'classAttendance',
            'periodKey', 'periodLabel', 'dateFrom', 'dateTo',
            'admissionStats', 'behaviorStats', 'feeDefaulters', 'overdueInstallments',
            'examAnalytics', 'latestExam'
        ));
    }

    private function teacherDashboard()
    {
        $totalStudents = Student::count();
        $totalExams    = Exam::count();
        $classrooms    = ClassRoom::count();
        $recentNotices = Notice::where('is_active', 1)
                            ->orderByDesc('created_at')
                            ->take(5)
                            ->get();

        return view('admin.dashboard.teacher_dashboard', compact(
            'totalStudents', 'totalExams', 'classrooms', 'recentNotices'
        ));
    }

    private function accountantDashboard()
    {
        $currentMonth = date('F Y');
        $totalFee     = StudentFee::where('fee_month', $currentMonth)
                            ->whereHas('class_fee_voucher')
                            ->whereHas('student')
                            ->sum('total_fee');
        $students     = StudentFee::with('student')
                            ->where('fee_month', $currentMonth)
                            ->whereHas('class_fee_voucher')
                            ->whereHas('student')
                            ->get();
        $totalStudents = Student::count();

        return view('admin.dashboard.accountant_dashboard', compact(
            'totalFee', 'students', 'totalStudents'
        ));
    }
}
