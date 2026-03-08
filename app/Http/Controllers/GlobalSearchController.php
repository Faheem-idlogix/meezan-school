<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentFee;
use App\Models\ClassFeeVoucher;
use App\Models\ClassRoom;
use App\Models\Notice;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * AJAX live-search: returns JSON for the navbar dropdown.
     */
    public function suggest(Request $request)
    {
        $q = trim($request->input('q', ''));
        $items = [];

        if (strlen($q) < 2) {
            return response()->json($items);
        }

        // Students
        Student::where(function ($query) use ($q) {
                $query->where('student_name', 'like', "%{$q}%")
                      ->orWhere('father_name', 'like', "%{$q}%")
                      ->orWhere('student_id_no', 'like', "%{$q}%");
            })->limit(5)->get()
            ->each(function ($s) use (&$items) {
                $items[] = [
                    'title' => $s->student_name,
                    'sub'   => 'ID# ' . ($s->student_id_no ?? 'N/A') . ' — ' . ($s->father_name ?? ''),
                    'url'   => route('student.show', $s->id),
                    'icon'  => 'bi bi-person-fill text-primary',
                    'cat'   => 'Students',
                ];
            });

        // Teachers
        Teacher::where(function ($query) use ($q) {
                $query->where('teacher_name', 'like', "%{$q}%")
                      ->orWhere('teacher_email', 'like', "%{$q}%")
                      ->orWhere('contact_no', 'like', "%{$q}%");
            })->limit(5)->get()
            ->each(function ($t) use (&$items) {
                $items[] = [
                    'title' => $t->teacher_name,
                    'sub'   => $t->teacher_email ?? $t->contact_no ?? '',
                    'url'   => route('teacher.show', $t->id),
                    'icon'  => 'bi bi-person-workspace text-success',
                    'cat'   => 'Teachers',
                ];
            });

        // Fee Vouchers
        StudentFee::where(function ($query) use ($q) {
                $query->where('voucher_no', 'like', "%{$q}%")
                      ->orWhere('fee_month', 'like', "%{$q}%")
                      ->orWhereHas('student', fn($qb) => $qb->where('student_name', 'like', "%{$q}%"));
            })->with('student')->limit(5)->get()
            ->each(function ($f) use (&$items) {
                $items[] = [
                    'title' => 'Voucher #' . $f->voucher_no,
                    'sub'   => ($f->student->student_name ?? 'N/A') . ' — Rs.' . number_format($f->total_fee ?? 0),
                    'url'   => route('student_fee_edit', $f->student_fee_id),
                    'icon'  => 'bi bi-receipt text-warning',
                    'cat'   => 'Vouchers',
                ];
            });

        // Classes
        ClassRoom::where(function ($query) use ($q) {
                $query->where('class_name', 'like', "%{$q}%")
                      ->orWhere('section_name', 'like', "%{$q}%");
            })->limit(3)->get()
            ->each(function ($c) use (&$items) {
                $items[] = [
                    'title' => $c->class_name . ' - ' . $c->section_name,
                    'sub'   => '',
                    'url'   => route('class.show', $c->id),
                    'icon'  => 'bi bi-journal-text text-info',
                    'cat'   => 'Classes',
                ];
            });

        return response()->json($items);
    }

    /**
     * Full search results page.
     */
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $results = [];

        if (strlen($q) >= 2) {
            // Students
            $students = Student::where(function ($query) use ($q) {
                    $query->where('student_name', 'like', "%{$q}%")
                          ->orWhere('father_name', 'like', "%{$q}%")
                          ->orWhere('student_id_no', 'like', "%{$q}%");
                })->limit(15)->get();
            if ($students->count()) {
                $results['Students'] = $students->map(fn($s) => [
                    'title' => $s->student_name,
                    'sub'   => 'ID# ' . ($s->student_id_no ?? 'N/A') . ' — Father: ' . ($s->father_name ?? ''),
                    'url'   => route('student.show', $s->id),
                    'icon'  => 'bi bi-person-fill text-primary',
                ]);
            }

            // Teachers
            $teachers = Teacher::where(function ($query) use ($q) {
                    $query->where('teacher_name', 'like', "%{$q}%")
                          ->orWhere('teacher_email', 'like', "%{$q}%")
                          ->orWhere('contact_no', 'like', "%{$q}%");
                })->limit(10)->get();
            if ($teachers->count()) {
                $results['Teachers'] = $teachers->map(fn($t) => [
                    'title' => $t->teacher_name,
                    'sub'   => $t->teacher_email ?? $t->contact_no ?? '',
                    'url'   => route('teacher.show', $t->id),
                    'icon'  => 'bi bi-person-workspace text-success',
                ]);
            }

            // Fee Vouchers
            $fees = StudentFee::where(function ($query) use ($q) {
                    $query->where('voucher_no', 'like', "%{$q}%")
                          ->orWhere('fee_month', 'like', "%{$q}%")
                          ->orWhereHas('student', fn($qb) => $qb->where('student_name', 'like', "%{$q}%"));
                })->with('student')->limit(15)->get();
            if ($fees->count()) {
                $results['Fee Vouchers'] = $fees->map(fn($f) => [
                    'title' => 'Voucher #' . $f->voucher_no,
                    'sub'   => ($f->student->student_name ?? 'N/A') . ' — ' . $f->fee_month . ' — Rs.' . number_format($f->total_fee ?? 0),
                    'url'   => route('student_fee_edit', $f->student_fee_id),
                    'icon'  => 'bi bi-receipt text-warning',
                ]);
            }

            // Monthly Invoices
            $invoices = ClassFeeVoucher::where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('month', 'like', "%{$q}%");
                })->limit(10)->get();
            if ($invoices->count()) {
                $results['Monthly Invoices'] = $invoices->map(fn($v) => [
                    'title' => $v->name,
                    'sub'   => 'Month: ' . $v->month,
                    'url'   => route('class_fee', $v->class_fee_voucher_id),
                    'icon'  => 'bi bi-file-earmark-text text-info',
                ]);
            }

            // Classes
            $classes = ClassRoom::where(function ($query) use ($q) {
                    $query->where('class_name', 'like', "%{$q}%")
                          ->orWhere('section_name', 'like', "%{$q}%");
                })->limit(10)->get();
            if ($classes->count()) {
                $results['Classes'] = $classes->map(fn($c) => [
                    'title' => $c->class_name . ' - ' . $c->section_name,
                    'sub'   => '',
                    'url'   => route('class.show', $c->id),
                    'icon'  => 'bi bi-journal-text text-purple',
                ]);
            }

            // Notices
            $notices = Notice::where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                          ->orWhere('content', 'like', "%{$q}%");
                })->limit(10)->get();
            if ($notices->count()) {
                $results['Notices'] = $notices->map(fn($n) => [
                    'title' => $n->title,
                    'sub'   => \Illuminate\Support\Str::limit(strip_tags($n->content), 60),
                    'url'   => route('notice.show', $n->id),
                    'icon'  => 'bi bi-megaphone-fill text-danger',
                ]);
            }
        }

        return view('admin.pages.search.index', compact('q', 'results'));
    }
}
