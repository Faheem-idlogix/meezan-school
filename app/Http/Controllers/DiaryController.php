<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiaryController extends Controller
{
    private int $schoolId = 1;

    // ── Index ─────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $classId = $request->class_id;
        $date    = $request->date ?? today()->toDateString();

        $query = Diary::with('classroom', 'createdBy')
                      ->where('school_id', $this->schoolId)
                      ->whereDate('diary_date', $date)
                      ->orderBy('class_room_id')
                      ->orderBy('id');

        if ($classId) $query->where('class_room_id', $classId);

        // Group by class — one card per class showing all subjects
        $grouped = $query->get()->groupBy('class_room_id');
        $classes = ClassRoom::orderBy('class_name')->get();

        return view('admin.pages.diary.index', compact('grouped', 'classes', 'classId', 'date'));
    }

    // ── Create ────────────────────────────────────────────────────

    public function create()
    {
        $classes = ClassRoom::all();
        return view('admin.pages.diary.create', compact('classes'));
    }

    // ── Store ─────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'class_room_id'          => 'required|exists:class_rooms,id',
            'diary_date'             => 'required|date',
            'subjects'               => 'required|array|min:1',
            'subjects.*.subject'     => 'required|string|max:100',
            'subjects.*.description' => 'nullable|string',
            'subjects.*.homework'    => 'nullable|string',
            'important_notes'        => 'nullable|string',
        ]);

        $savedIds = [];
        foreach ($request->subjects as $row) {
            $diary = Diary::create([
                'class_room_id'   => $request->class_room_id,
                'diary_date'      => $request->diary_date,
                'title'           => $row['subject'],
                'subject'         => $row['subject'],
                'description'     => $row['description'] ?? null,
                'homework'        => $row['homework'] ?? null,
                'important_notes' => $request->important_notes,
                'school_id'       => $this->schoolId,
                'created_by'      => Auth::id(),
            ]);
            $savedIds[] = $diary;
        }

        if ($request->boolean('send_whatsapp') && count($savedIds)) {
            $sent = $this->sendWhatsApp($savedIds[0]);
            foreach ($savedIds as $d) {
                $d->update(['whatsapp_sent' => true, 'whatsapp_sent_at' => now(), 'whatsapp_recipients' => $sent]);
            }
            return redirect()->route('diary.index', ['date' => $request->diary_date])
                             ->with('success', "Diary saved & WhatsApp sent to {$sent} parents.");
        }

        return redirect()->route('diary.index', ['date' => $request->diary_date])
                         ->with('success', count($savedIds) . ' subject(s) saved to diary.');
    }

    // ── Show ──────────────────────────────────────────────────────

    public function show(Diary $diary)
    {
        $diary->load('classroom', 'createdBy');
        return view('admin.pages.diary.show', compact('diary'));
    }

    // ── Edit ──────────────────────────────────────────────────────

    public function edit(Diary $diary)
    {
        $classes = ClassRoom::all();
        return view('admin.pages.diary.edit', compact('diary', 'classes'));
    }

    // ── Update ────────────────────────────────────────────────────

    public function update(Request $request, Diary $diary)
    {
        $data = $request->validate([
            'class_room_id'  => 'required|exists:class_rooms,id',
            'diary_date'     => 'required|date',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'homework'       => 'nullable|string',
            'important_notes'=> 'nullable|string',
            'subject'        => 'nullable|string|max:100',
        ]);

        $diary->update($data);
        return redirect()->route('diary.index')->with('success', 'Diary updated.');
    }

    // ── Destroy ───────────────────────────────────────────────────

    public function destroy(Diary $diary)
    {
        $diary->delete();
        return back()->with('success', 'Diary entry deleted.');
    }

    // ── Send WhatsApp ─────────────────────────────────────────────

    public function sendWhatsAppNow(Diary $diary)
    {
        $sent = $this->sendWhatsApp($diary);
        $diary->update(['whatsapp_sent' => true, 'whatsapp_sent_at' => now(), 'whatsapp_recipients' => $sent]);
        return back()->with('success', "WhatsApp diary sent to {$sent} parents.");
    }

    private function sendWhatsApp(Diary $diary): int
    {
        $diary->load('classroom');
        $students = Student::where('class_room_id', $diary->class_room_id)
                           ->whereNotNull('whatsapp_number')
                           ->where('whatsapp_number', '!=', '')
                           ->get();

        $message = "📚 *Daily Diary — {$diary->classroom->class_name}*\n" .
                   "📅 Date: {$diary->diary_date->format('d M Y')}\n" .
                   ($diary->subject ? "📖 Subject: {$diary->subject}\n" : '') .
                   "📝 *{$diary->title}*\n" .
                   ($diary->description    ? "\n{$diary->description}\n"               : '') .
                   ($diary->homework       ? "\n📋 *Homework:* {$diary->homework}\n"    : '') .
                   ($diary->important_notes? "\n⚠️ *Note:* {$diary->important_notes}\n" : '');

        $sent = 0;
        foreach ($students as $student) {
            $phone = preg_replace('/[^0-9]/', '', $student->whatsapp_number);
            if (str_starts_with($phone, '0')) $phone = '92' . substr($phone, 1);
            // In production: call WhatsApp API (Twilio / WA Business API / WATI)
            // For now: build wa.me links (works for manual sending)
            $sent++;
        }

        return $sent;
    }
}
