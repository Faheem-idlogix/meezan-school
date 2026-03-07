<?php

namespace App\Http\Controllers;

use App\Models\AdmissionEnquiry;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    /**
     * Display all admission enquiries.
     */
    public function index()
    {
        $enquiries = AdmissionEnquiry::with('classRoom', 'processedBy')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'          => $enquiries->count(),
            'enquiry'        => $enquiries->where('status', 'enquiry')->count(),
            'test_scheduled' => $enquiries->where('status', 'test_scheduled')->count(),
            'approved'       => $enquiries->where('status', 'approved')->count(),
            'enrolled'       => $enquiries->where('status', 'enrolled')->count(),
            'rejected'       => $enquiries->where('status', 'rejected')->count(),
        ];

        return view('admin.pages.admission.index', compact('enquiries', 'stats'));
    }

    /**
     * Show form for new enquiry.
     */
    public function create()
    {
        $classes = ClassRoom::all();
        return view('admin.pages.admission.create', compact('classes'));
    }

    /**
     * Store a new enquiry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $data = $request->all();
        $data['enquiry_date'] = $data['enquiry_date'] ?? now()->toDateString();
        $data['status'] = 'enquiry';
        $data['processed_by'] = auth()->id();

        AdmissionEnquiry::create($data);

        return redirect()->route('admission.index')->with('success', 'Admission enquiry registered successfully.');
    }

    /**
     * Show a single enquiry.
     */
    public function show(AdmissionEnquiry $admission)
    {
        $admission->load('classRoom', 'processedBy', 'student');
        return view('admin.pages.admission.show', compact('admission'));
    }

    /**
     * Edit an enquiry.
     */
    public function edit(AdmissionEnquiry $admission)
    {
        $classes = ClassRoom::all();
        return view('admin.pages.admission.edit', compact('admission', 'classes'));
    }

    /**
     * Update an enquiry.
     */
    public function update(Request $request, AdmissionEnquiry $admission)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'contact_no'   => 'required|string|max:20',
            'status'       => 'required|in:enquiry,test_scheduled,test_taken,approved,rejected,enrolled',
        ]);

        $admission->update($request->all());

        return redirect()->route('admission.index')->with('success', 'Enquiry updated successfully.');
    }

    /**
     * Delete an enquiry.
     */
    public function destroy(AdmissionEnquiry $admission)
    {
        $admission->delete();
        return redirect()->route('admission.index')->with('success', 'Enquiry deleted successfully.');
    }

    /**
     * Schedule admission test.
     */
    public function scheduleTest(Request $request, AdmissionEnquiry $admission)
    {
        $request->validate([
            'test_date' => 'required|date',
        ]);

        $admission->update([
            'test_date' => $request->test_date,
            'status'    => 'test_scheduled',
        ]);

        return redirect()->route('admission.show', $admission)->with('success', 'Test scheduled successfully.');
    }

    /**
     * Record test result.
     */
    public function recordTestResult(Request $request, AdmissionEnquiry $admission)
    {
        $request->validate([
            'test_marks'   => 'required|numeric|min:0',
            'test_remarks' => 'nullable|string',
        ]);

        $admission->update([
            'test_marks'   => $request->test_marks,
            'test_remarks' => $request->test_remarks,
            'status'       => 'test_taken',
        ]);

        return redirect()->route('admission.show', $admission)->with('success', 'Test result recorded.');
    }

    /**
     * Approve admission.
     */
    public function approve(AdmissionEnquiry $admission)
    {
        $admission->update([
            'status'       => 'approved',
            'processed_by' => auth()->id(),
        ]);

        return redirect()->route('admission.show', $admission)->with('success', 'Admission approved.');
    }

    /**
     * Reject admission.
     */
    public function reject(Request $request, AdmissionEnquiry $admission)
    {
        $admission->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks ?? $admission->remarks,
        ]);

        return redirect()->route('admission.index')->with('success', 'Admission rejected.');
    }

    /**
     * Enroll — convert approved enquiry to student.
     */
    public function enroll(AdmissionEnquiry $admission)
    {
        if ($admission->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved enquiries can be enrolled.');
        }

        if (empty($admission->class_room_id)) {
            return redirect()->back()->with('error', 'Cannot enroll: no class assigned. Please edit the enquiry and select a class first.');
        }

        // Split name into first/last
        $parts = explode(' ', $admission->student_name, 2);
        $firstName = $parts[0];
        $lastName  = $parts[1] ?? '';

        $studentName = $firstName . ' ' . $lastName;
        $slug = Str::slug($studentName);
        $checkSlug = Student::withTrashed()->where('slug', $slug)->first();
        if ($checkSlug) {
            $slug = Str::slug($studentName . '-' . rand(1, 1000));
        }

        $student = Student::create([
            'first_name'           => $firstName,
            'last_name'            => $lastName,
            'student_name'         => $studentName,
            'student_email'        => $slug . '@meezan.edu.pk',
            'slug'                 => $slug,
            'father_name'          => $admission->father_name,
            'contact_no'           => $admission->contact_no,
            'gender'               => $admission->gender ?? 'male',
            'class_room_id'        => $admission->class_room_id,
            'student_status'       => 'active',
            'student_admission_date' => now()->toDateString(),
            'admission_enquiry_id' => $admission->id,
            'address'              => $admission->address,
        ]);

        $admission->update(['status' => 'enrolled']);

        return redirect()->route('student.show', $student)->with('success', 'Student enrolled successfully from admission enquiry.');
    }
}
