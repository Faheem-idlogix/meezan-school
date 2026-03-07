<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /* ───── Relationships ───── */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function targetRole()
    {
        return $this->belongsTo(Role::class, 'target_role_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'notification_user')
                     ->withPivot('read_at')
                     ->withTimestamps();
    }

    /* ───── Scopes ───── */

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('recipients', fn($q) => $q->where('user_id', $user->id));
    }

    public function scopeUnreadByUser($query, User $user)
    {
        return $query->whereHas('recipients', function ($q) use ($user) {
            $q->where('user_id', $user->id)->whereNull('read_at');
        });
    }

    /* ───── Helpers ───── */

    public function isReadBy(User $user): bool
    {
        return $this->recipients()
                     ->where('user_id', $user->id)
                     ->whereNotNull('read_at')
                     ->exists();
    }

    public function markReadFor(User $user): void
    {
        $this->recipients()->updateExistingPivot($user->id, ['read_at' => now()]);
    }

    /**
     * Dispatch this notification to the correct users based on target_type.
     */
    public function dispatchToRecipients(): int
    {
        $userIds = collect();

        switch ($this->target_type) {
            case 'all':
                $userIds = User::pluck('id');
                break;

            case 'role':
                if ($this->target_role_id) {
                    $role = Role::find($this->target_role_id);
                    if ($role) {
                        $userIds = $role->users()->pluck('users.id');
                    }
                }
                break;

            case 'class':
                if ($this->target_class_id) {
                    // Get teacher user IDs from timetable for this class
                    $teacherIds = \DB::table('timetables')
                        ->where('class_room_id', $this->target_class_id)
                        ->distinct()
                        ->pluck('teacher_id');
                    // Map teacher emails to user accounts
                    $teacherEmails = Teacher::whereIn('id', $teacherIds)->pluck('teacher_email');
                    $teacherUserIds = User::whereIn('email', $teacherEmails)->pluck('id');

                    // Map student emails from this class to user accounts
                    $studentEmails = Student::where('class_room_id', $this->target_class_id)->pluck('student_email');
                    $studentUserIds = User::whereIn('email', $studentEmails)->pluck('id');

                    $userIds = $teacherUserIds->merge($studentUserIds)->unique();
                }
                break;

            case 'user':
                // For single-user targeting, the recipient is added directly
                // Nothing extra to dispatch
                return $this->recipients()->count();
        }

        // Exclude the sender
        if ($this->sender_id) {
            $userIds = $userIds->reject(fn($id) => $id == $this->sender_id);
        }

        // Sync recipients (without detaching any already-set ones)
        $existingIds = $this->recipients()->pluck('users.id');
        $newIds = $userIds->diff($existingIds);

        if ($newIds->isNotEmpty()) {
            $this->recipients()->attach($newIds->mapWithKeys(fn($id) => [$id => ['read_at' => null]]));
        }

        return $this->recipients()->count();
    }

    /**
     * Type badge HTML.
     */
    public function getTypeBadgeAttribute(): string
    {
        $colors = [
            'info'    => 'bg-info',
            'warning' => 'bg-warning text-dark',
            'success' => 'bg-success',
            'danger'  => 'bg-danger',
        ];
        $color = $colors[$this->type] ?? 'bg-secondary';
        return '<span class="badge ' . $color . '">' . ucfirst($this->type) . '</span>';
    }

    /**
     * Icon based on type.
     */
    public function getTypeIconAttribute(): string
    {
        $icons = [
            'info'    => 'bi bi-info-circle-fill text-primary',
            'warning' => 'bi bi-exclamation-triangle-fill text-warning',
            'success' => 'bi bi-check-circle-fill text-success',
            'danger'  => 'bi bi-x-circle-fill text-danger',
        ];
        return $icons[$this->type] ?? 'bi bi-bell-fill text-secondary';
    }
}
