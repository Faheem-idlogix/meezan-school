<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class TransferCertificate extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'issue_date'   => 'date',
        'leaving_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function issuedByUser()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // ── Scopes ──

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    // ── Helpers ──

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '<span class="badge bg-warning text-dark">Draft</span>',
            'issued'    => '<span class="badge bg-success">Issued</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public static function generateTcNumber(): string
    {
        $year = now()->format('Y');
        $last = static::withTrashed()->where('tc_number', 'like', "TC-{$year}-%")->count();
        return "TC-{$year}-" . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
