<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentDocumentController extends Controller
{
    /**
     * List documents for a student.
     */
    public function index(Student $student)
    {
        $documents = $student->documents()->orderByDesc('created_at')->get();
        $documentTypes = StudentDocument::documentTypes();
        return view('admin.pages.student.documents', compact('student', 'documents', 'documentTypes'));
    }

    /**
     * Upload a new document.
     */
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'document_type'  => 'required|string',
            'document_title' => 'required|string|max:255',
            'document_file'  => 'required|file|max:10240', // 10MB max
            'expiry_date'    => 'nullable|date',
            'remarks'        => 'nullable|string',
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('img/documents/' . $student->id);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);

        StudentDocument::create([
            'student_id'     => $student->id,
            'document_type'  => $request->document_type,
            'document_title' => $request->document_title,
            'file_path'      => 'img/documents/' . $student->id . '/' . $fileName,
            'file_name'      => $file->getClientOriginalName(),
            'file_size'      => $file->getSize() ?? null,
            'mime_type'      => $file->getClientMimeType() ?? null,
            'expiry_date'    => $request->expiry_date,
            'remarks'        => $request->remarks,
        ]);

        return redirect()->route('student.documents', $student)->with('success', 'Document uploaded successfully.');
    }

    /**
     * Verify a document.
     */
    public function verify(StudentDocument $studentDocument)
    {
        $studentDocument->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document verified successfully.');
    }

    /**
     * Delete a document.
     */
    public function destroy(StudentDocument $studentDocument)
    {
        // Delete physical file
        $fullPath = public_path($studentDocument->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $studentId = $studentDocument->student_id;
        $studentDocument->forceDelete();

        return redirect()->route('student.documents', $studentId)->with('success', 'Document deleted successfully.');
    }
}
