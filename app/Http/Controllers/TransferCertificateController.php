<?php

namespace App\Http\Controllers;

use App\Models\TransferCertificate;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferCertificateController extends Controller
{
    /**
     * List all TCs.
     */
    public function index()
    {
        $certificates = TransferCertificate::with('student.classroom', 'issuedByUser')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pages.transfer_certificate.index', compact('certificates'));
    }

    /**
     * Create form.
     */
    public function create()
    {
        $students = Student::with('classroom')->orderBy('student_name')->get();
        return view('admin.pages.transfer_certificate.create', compact('students'));
    }

    /**
     * Store a new TC.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'issue_date'   => 'required|date',
            'leaving_date' => 'nullable|date',
            'reason'       => 'nullable|string',
            'conduct'      => 'nullable|string',
            'remarks'      => 'nullable|string',
        ]);

        $data = $request->all();
        $data['tc_number'] = TransferCertificate::generateTcNumber();
        $data['status'] = 'draft';
        $data['issued_by'] = auth()->id();

        $tc = TransferCertificate::create($data);

        return redirect()->route('transfer-certificate.show', $tc)->with('success', 'Transfer Certificate created as draft.');
    }

    /**
     * Show TC detail.
     */
    public function show(TransferCertificate $transferCertificate)
    {
        $transferCertificate->load('student.classroom', 'issuedByUser');
        return view('admin.pages.transfer_certificate.show', compact('transferCertificate'));
    }

    /**
     * Issue (finalize) a TC.
     */
    public function issue(TransferCertificate $transferCertificate)
    {
        $transferCertificate->update(['status' => 'issued']);

        // Optionally mark student as inactive
        if ($transferCertificate->student) {
            $transferCertificate->student->update(['student_status' => 'left']);
        }

        return redirect()->route('transfer-certificate.show', $transferCertificate)->with('success', 'TC issued successfully. Student marked as left.');
    }

    /**
     * Generate PDF.
     */
    public function pdf(TransferCertificate $transferCertificate)
    {
        $transferCertificate->load('student.classroom', 'issuedByUser');

        $pdf = Pdf::loadView('admin.pages.transfer_certificate.pdf', compact('transferCertificate'));
        return $pdf->stream('TC-' . $transferCertificate->tc_number . '.pdf');
    }

    /**
     * Delete.
     */
    public function destroy(TransferCertificate $transferCertificate)
    {
        $transferCertificate->delete();
        return redirect()->route('transfer-certificate.index')->with('success', 'Transfer Certificate deleted.');
    }
}
