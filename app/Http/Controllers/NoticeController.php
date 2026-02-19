<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('creator')->latest()->get();
        return view('admin.pages.notice.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.pages.notice.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required',
            'audience' => 'required|in:all,students,teachers,parents',
            'priority' => 'required|in:low,medium,high',
        ]);

        Notice::create([
            'title'          => $request->title,
            'content'        => $request->content,
            'audience'       => $request->audience,
            'priority'       => $request->priority,
            'send_whatsapp'  => $request->has('send_whatsapp'),
            'is_active'      => true,
            'created_by'     => auth()->id(),
        ]);

        return redirect()->route('notice.index')->with('success', 'Notice published successfully');
    }

    public function show(Notice $notice)
    {
        return view('admin.pages.notice.show', compact('notice'));
    }

    public function edit(Notice $notice)
    {
        return view('admin.pages.notice.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required',
            'audience' => 'required|in:all,students,teachers,parents',
            'priority' => 'required|in:low,medium,high',
        ]);

        $notice->update([
            'title'         => $request->title,
            'content'       => $request->content,
            'audience'      => $request->audience,
            'priority'      => $request->priority,
            'send_whatsapp' => $request->has('send_whatsapp'),
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('notice.index')->with('success', 'Notice updated successfully');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('notice.index')->with('success', 'Notice deleted');
    }

    public function toggleStatus(Notice $notice)
    {
        $notice->update(['is_active' => !$notice->is_active]);
        return response()->json(['success' => true, 'is_active' => $notice->is_active]);
    }
}
