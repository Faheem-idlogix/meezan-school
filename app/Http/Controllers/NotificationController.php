<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NotificationController extends Controller
{
    /* ────────────────────────────
     *  NOTIFICATION MANAGEMENT
     * ──────────────────────────── */

    /**
     * List all notifications (admin management page).
     */
    public function index()
    {
        $notifications = Notification::with('sender', 'targetRole')
            ->withCount('recipients')
            ->latest()
            ->paginate(20);

        return view('admin.pages.notifications.index', compact('notifications'));
    }

    /**
     * Create form — compose a new notification.
     */
    public function create()
    {
        $roles   = Role::orderBy('display_name')->get();
        $classes = ClassRoom::orderBy('class_name')->get();
        $users   = User::orderBy('name')->get();

        return view('admin.pages.notifications.create', compact('roles', 'classes', 'users'));
    }

    /**
     * Store & dispatch notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'type'        => 'required|in:info,warning,success,danger',
            'target_type' => 'required|in:all,role,class,user',
            'target_role_id'  => 'required_if:target_type,role|nullable|exists:roles,id',
            'target_class_id' => 'required_if:target_type,class|nullable',
            'target_user_ids' => 'required_if:target_type,user|nullable|array',
            'target_user_ids.*' => 'exists:users,id',
        ]);

        $notification = Notification::create([
            'title'          => $request->title,
            'message'        => $request->message,
            'type'           => $request->type,
            'target_type'    => $request->target_type,
            'target_role_id' => $request->target_type === 'role' ? $request->target_role_id : null,
            'target_class_id'=> $request->target_type === 'class' ? $request->target_class_id : null,
            'link'           => $request->link,
            'sender_id'      => auth()->id(),
        ]);

        if ($request->target_type === 'user' && $request->target_user_ids) {
            $notification->recipients()->attach(
                collect($request->target_user_ids)->mapWithKeys(fn($id) => [$id => ['read_at' => null]])
            );
        }

        $count = $notification->dispatchToRecipients();

        return redirect()->route('notifications.index')
            ->with('success', "Notification sent to {$count} user(s).");
    }

    /**
     * View a specific notification detail.
     */
    public function show(Notification $notification)
    {
        $notification->load('sender', 'targetRole', 'recipients');
        return view('admin.pages.notifications.show', compact('notification'));
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->recipients()->detach();
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted.');
    }

    /* ────────────────────────────
     *  NAVBAR BELL — AJAX APIs
     * ──────────────────────────── */

    /**
     * Get unread count + latest notifications for navbar bell (AJAX).
     */
    public function navbarData()
    {
        $user = auth()->user();

        $unreadCount = Notification::unreadByUser($user)->count();

        $latest = Notification::forUser($user)
            ->with('sender')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($n) use ($user) {
                return [
                    'id'        => $n->id,
                    'title'     => $n->title,
                    'message'   => \Illuminate\Support\Str::limit($n->message, 60),
                    'type_icon' => $n->type_icon,
                    'type'      => $n->type,
                    'link'      => $n->link,
                    'time'      => $n->created_at->diffForHumans(),
                    'read'      => $n->isReadBy($user),
                    'sender'    => $n->sender->name ?? 'System',
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latest,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Notification $notification)
    {
        $notification->markReadFor(auth()->user());

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        $user = auth()->user();

        \DB::table('notification_user')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /* ────────────────────────────
     *  MY NOTIFICATIONS PAGE
     * ──────────────────────────── */

    /**
     * All notifications for the logged-in user.
     */
    public function myNotifications()
    {
        $user = auth()->user();
        $notifications = Notification::forUser($user)
            ->with('sender')
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::unreadByUser($user)->count();

        return view('admin.pages.notifications.my', compact('notifications', 'unreadCount'));
    }

    /* ────────────────────────────
     *  UPDATE PASSWORD
     * ──────────────────────────── */

    /**
     * Show password change form.
     */
    public function showChangePassword()
    {
        return view('admin.pages.profile.change-password');
    }

    /**
     * Process password change.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Show profile page.
     */
    public function showProfile()
    {
        $user = auth()->user();
        return view('admin.pages.profile.index', compact('user'));
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($request->only('name', 'email'));

        return back()->with('success', 'Profile updated successfully!');
    }
}
