<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Notice;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ── Reports Hub ── */
    public function index()
    {
        $stats = [
            'students'      => Student::count(),
            'teachers'      => Teacher::count(),
            'classes'        => ClassRoom::count(),
            'vouchers'       => Voucher::count(),
            'exams'          => Exam::count(),
            'trashed'        => Student::onlyTrashed()->count() + Teacher::onlyTrashed()->count() + Voucher::onlyTrashed()->count(),
        ];
        return view('admin.pages.reports.index', compact('stats'));
    }

    /* ── Finance Report ── */
    public function finance(Request $request)
    {
        [$from, $to, $periodKey, $periodLabel] = $this->resolvePeriod($request);

        $vouchers = Voucher::where(function ($q) use ($from, $to) {
                $q->whereBetween('voucher_date', [$from->toDateString(), $to->toDateString()])
                  ->orWhere(function ($q2) use ($from, $to) {
                      $q2->whereNull('voucher_date')
                         ->whereBetween('created_at', [$from, $to]);
                  });
            })->orderByDesc('created_at')->get();

        $totalIncome  = $vouchers->where('type', 'income')->sum('amount');
        $totalExpense = $vouchers->where('type', 'expense')->sum('amount');
        $profitLoss   = $totalIncome - $totalExpense;

        // Monthly breakdown
        $monthly = collect();
        $cursor  = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $mStart = $cursor->copy()->startOfMonth();
            $mEnd   = $cursor->copy()->endOfMonth();
            $mV = $vouchers->filter(function ($v) use ($mStart, $mEnd) {
                $d = $v->voucher_date ?? $v->created_at;
                return Carbon::parse($d)->between($mStart, $mEnd);
            });
            $monthly->push([
                'label'   => $cursor->format('M Y'),
                'income'  => $mV->where('type', 'income')->sum('amount'),
                'expense' => $mV->where('type', 'expense')->sum('amount'),
            ]);
            $cursor->addMonth();
        }

        // Category breakdown
        $byCategory = $vouchers->groupBy('category')->map(function ($g, $cat) {
            return [
                'category' => $cat ?: 'Uncategorized',
                'income'   => $g->where('type', 'income')->sum('amount'),
                'expense'  => $g->where('type', 'expense')->sum('amount'),
                'count'    => $g->count(),
            ];
        })->values();

        return view('admin.pages.reports.finance', compact(
            'vouchers','totalIncome','totalExpense','profitLoss',
            'monthly','byCategory','periodKey','periodLabel','from','to'
        ));
    }

    /* ── Attendance Report ── */
    public function attendance(Request $request)
    {
        [$from, $to, $periodKey, $periodLabel] = $this->resolvePeriod($request);
        $classFilter = $request->get('class_id');

        $query = Attendance::whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
        if ($classFilter) $query->where('class_room_id', $classFilter);

        $records = $query->with(['student', 'classRoom'])->orderByDesc('date')->get();

        $totalRecords = $records->count();
        $present = $records->filter(fn($r) => in_array($r->attendance, ['1', 1]))->count();
        $absent  = $records->filter(fn($r) => in_array($r->attendance, ['3', 3]))->count();
        $leave   = $records->filter(fn($r) => in_array($r->attendance, ['2', 2]))->count();
        $late    = $records->filter(fn($r) => in_array($r->attendance, ['0', 0]))->count();
        $rate    = $totalRecords > 0 ? round(($present / $totalRecords) * 100, 1) : 0;

        // Class-wise
        $classWise = $records->groupBy(fn($r) => $r->classRoom->class_name ?? 'Unknown')->map(function ($g, $cn) {
            $t = $g->count(); $p = $g->filter(fn($r) => in_array($r->attendance, ['1', 1]))->count();
            return ['class' => $cn, 'total' => $t, 'present' => $p,
                    'absent' => $g->filter(fn($r) => in_array($r->attendance, ['3', 3]))->count(),
                    'rate'   => $t > 0 ? round(($p/$t)*100,1) : 0];
        })->values();

        // Student-wise
        $studentWise = $records->groupBy('student_id')->map(function ($g) {
            $s = $g->first()->student; $t = $g->count();
            $p = $g->filter(fn($r) => in_array($r->attendance, ['1', 1]))->count();
            return ['student_id'=>$s->id??0,'student_name'=>$s->student_name??'Unknown',
                    'class_name'=>$s->classroom->class_name??'—','total'=>$t,'present'=>$p,
                    'absent'=>$g->filter(fn($r) => in_array($r->attendance, ['3', 3]))->count(),
                    'rate'=>$t>0?round(($p/$t)*100,1):0];
        })->sortByDesc('rate')->values();

        $classrooms = ClassRoom::orderBy('class_name')->get();

        return view('admin.pages.reports.attendance', compact(
            'records','totalRecords','present','absent','leave','late','rate',
            'classWise','studentWise','classrooms','classFilter',
            'periodKey','periodLabel','from','to'
        ));
    }

    /* ── Student Report ── */
    public function students(Request $request)
    {
        $classFilter = $request->get('class_id');
        $showTrashed = $request->boolean('show_trashed');

        $query = $showTrashed ? Student::withTrashed() : Student::query();
        $query->with('classroom');
        if ($classFilter) $query->where('class_room_id', $classFilter);

        $students   = $query->orderBy('student_name')->get();
        $active     = Student::count();
        $inactive   = Student::onlyTrashed()->count();
        $male       = Student::where('gender', 'male')->count();
        $female     = Student::where('gender', 'female')->count();
        $classrooms = ClassRoom::orderBy('class_name')->get();

        $classDist = Student::select('class_room_id', DB::raw('COUNT(*) as count'))
            ->groupBy('class_room_id')->with('classroom')->get()
            ->map(fn($r) => ['class'=>$r->classroom->class_name??'N/A','count'=>$r->count]);

        $trashedStudents = Student::onlyTrashed()->with('classroom')->orderByDesc('deleted_at')->get();

        return view('admin.pages.reports.students', compact(
            'students','active','inactive','male','female',
            'classrooms','classDist','trashedStudents','classFilter','showTrashed'
        ));
    }

    /* ── Exam Report ── */
    public function exams(Request $request)
    {
        $examFilter = $request->get('exam_id');

        $exams = Exam::orderByDesc('created_at')->get();
        $rQuery = ExamResult::with(['student','subject','exam','classRoom']);
        if ($examFilter) $rQuery->where('exam_id', $examFilter);
        $results = $rQuery->get();

        $examSummary = $results->groupBy(fn($r) => $r->exam->exam_name??'Unknown')->map(function ($g, $n) {
            $tm = $g->sum('total_marks'); $om = $g->sum('obtained_marks');
            return ['exam'=>$n,'students'=>$g->pluck('student_id')->unique()->count(),
                    'subjects'=>$g->pluck('subject_id')->unique()->count(),
                    'avg_pct'=>$tm>0?round(($om/$tm)*100,1):0,'total'=>$tm,'obtained'=>$om];
        })->values();

        $topPerformers = $results->groupBy('student_id')->map(function ($g) {
            $s = $g->first()->student; $t = $g->sum('total_marks'); $o = $g->sum('obtained_marks');
            return ['student_id'=>$s->id??0,'student_name'=>$s->student_name??'Unknown',
                    'total'=>$t,'obtained'=>$o,'pct'=>$t>0?round(($o/$t)*100,1):0];
        })->sortByDesc('pct')->take(10)->values();

        return view('admin.pages.reports.exams', compact(
            'exams','results','examSummary','topPerformers','examFilter'
        ));
    }

    /* ── Fee Collection Report ── */
    public function fees(Request $request)
    {
        [$from, $to, $periodKey, $periodLabel] = $this->resolvePeriod($request);
        $feeMonths = [];
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) { $feeMonths[] = $cursor->format('F Y'); $cursor->addMonth(); }

        $feeRecords = StudentFee::with(['student.classroom'])
            ->whereIn('fee_month', $feeMonths)->orderByDesc('created_at')->get();

        $totalBilled   = $feeRecords->sum('total_fee');
        $totalReceived = $feeRecords->sum('received_payment_fee');
        $outstanding   = $totalBilled - $totalReceived;
        $paidCount     = $feeRecords->where('status', 'paid')->count();
        $unpaidCount   = $feeRecords->where('status', 'unpaid')->count();
        $pendingCount  = $feeRecords->whereNotIn('status', ['paid','unpaid'])->count();

        $monthlyFee = collect();
        foreach ($feeMonths as $m) {
            $mr = $feeRecords->where('fee_month', $m);
            $monthlyFee->push(['month'=>$m,'billed'=>$mr->sum('total_fee'),
                'received'=>$mr->sum('received_payment_fee'),
                'paid'=>$mr->where('status','paid')->count(),
                'unpaid'=>$mr->where('status','unpaid')->count()]);
        }

        return view('admin.pages.reports.fees', compact(
            'feeRecords','totalBilled','totalReceived','outstanding',
            'paidCount','unpaidCount','pendingCount',
            'monthlyFee','periodKey','periodLabel','from','to'
        ));
    }

    /* ── Archived / Soft-Deleted Records ── */
    public function archived()
    {
        $students  = Student::onlyTrashed()->with('classroom')->orderByDesc('deleted_at')->get();
        $teachers  = Teacher::onlyTrashed()->orderByDesc('deleted_at')->get();
        $vouchers  = Voucher::onlyTrashed()->orderByDesc('deleted_at')->get();
        $results   = ExamResult::onlyTrashed()->with(['student','exam'])->orderByDesc('deleted_at')->get();
        $classrooms= ClassRoom::onlyTrashed()->orderByDesc('deleted_at')->get();
        $subjects  = Subject::onlyTrashed()->orderByDesc('deleted_at')->get();

        return view('admin.pages.reports.archived', compact(
            'students','teachers','vouchers','results','classrooms','subjects'
        ));
    }

    /* ── Documentation ── */
    public function documentation()
    {
        return view('admin.pages.reports.documentation');
    }

    /* ── Legacy result card ── */
    public function result_card()
    {
        return view('admin.report.result_card');
    }

    /* ── Helper ── */
    private function resolvePeriod(Request $request): array
    {
        $p = $request->get('period', 'this_month');
        switch ($p) {
            case 'last_month':
                $f = now()->subMonth()->startOfMonth(); $t = now()->subMonth()->endOfMonth();
                $l = $f->format('F Y'); break;
            case '6_months':
                $f = now()->subMonths(5)->startOfMonth(); $t = now()->endOfMonth();
                $l = $f->format('M Y').' – '.$t->format('M Y'); break;
            case 'last_year':
                $f = now()->subYear()->startOfYear(); $t = now()->subYear()->endOfYear();
                $l = $f->format('Y'); break;
            case 'this_year':
                $f = now()->startOfYear(); $t = now()->endOfMonth();
                $l = $f->format('Y'); break;
            case 'custom':
                $f = $request->get('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->startOfMonth();
                $t = $request->get('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay();
                $l = $f->format('d M Y').' – '.$t->format('d M Y'); break;
            default:
                $p = 'this_month'; $f = now()->startOfMonth(); $t = now()->endOfMonth();
                $l = now()->format('F Y'); break;
        }
        return [$f, $t, $p, $l];
    }
}
