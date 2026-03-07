<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Exam extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];

    protected $casts = [
        'date'      => 'date',
        'weightage' => 'decimal:2',
    ];

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    // ── Helpers ──

    public function getStatusBadgeAttribute(): string
    {
        $status = $this->status ?? 'draft';
        return match ($status) {
            'draft'            => '<span class="badge bg-secondary">Draft</span>',
            'published'        => '<span class="badge bg-info">Published</span>',
            'result_pending'   => '<span class="badge bg-warning text-dark">Result Pending</span>',
            'result_published' => '<span class="badge bg-success">Results Published</span>',
            default            => '<span class="badge bg-dark">' . ucfirst($status) . '</span>',
        };
    }
}
