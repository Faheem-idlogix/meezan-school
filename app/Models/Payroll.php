<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'paid_date'        => 'date',
        'whatsapp_sent_at' => 'datetime',
    ];

    // Month name helper
    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }

    // ── Relationships ────────────────────────────────────────────

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeForSchool($query, int $schoolId = 1)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
