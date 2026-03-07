<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class AdmissionEnquiry extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'enquiry_date' => 'date',
        'test_date'    => 'date',
    ];

    // ── Relationships ──

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'admission_enquiry_id');
    }

    // ── Scopes ──

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeEnquiries($query)
    {
        return $query->where('status', 'enquiry');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeTestScheduled($query)
    {
        return $query->where('status', 'test_scheduled');
    }

    // ── Helpers ──

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'enquiry'        => '<span class="badge bg-info">Enquiry</span>',
            'test_scheduled' => '<span class="badge bg-warning text-dark">Test Scheduled</span>',
            'test_taken'     => '<span class="badge bg-secondary">Test Taken</span>',
            'approved'       => '<span class="badge bg-success">Approved</span>',
            'rejected'       => '<span class="badge bg-danger">Rejected</span>',
            'enrolled'       => '<span class="badge bg-primary">Enrolled</span>',
            default          => '<span class="badge bg-dark">' . ucfirst($this->status) . '</span>',
        };
    }
}
