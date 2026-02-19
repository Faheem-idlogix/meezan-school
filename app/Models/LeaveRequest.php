<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function leavable()
    {
        return $this->morphTo();
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForTeachers($query)
    {
        return $query->where('leavable_type', Teacher::class);
    }

    public function scopeForStudents($query)
    {
        return $query->where('leavable_type', Student::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function calculateDays(): int
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default    => '<span class="badge bg-warning text-dark">Pending</span>',
        };
    }
}
