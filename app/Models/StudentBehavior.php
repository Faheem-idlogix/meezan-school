<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class StudentBehavior extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'incident_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // ── Scopes ──

    public function scopePositive($query)
    {
        return $query->where('type', 'positive');
    }

    public function scopeNegative($query)
    {
        return $query->where('type', 'negative');
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers ──

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'positive' => '<span class="badge bg-success">Positive</span>',
            'negative' => '<span class="badge bg-danger">Negative</span>',
            default    => '<span class="badge bg-secondary">Neutral</span>',
        };
    }

    public static function categories(): array
    {
        return [
            'discipline'  => 'Discipline',
            'academic'    => 'Academic',
            'social'      => 'Social Behavior',
            'sports'      => 'Sports',
            'attendance'  => 'Attendance',
            'cleanliness' => 'Cleanliness',
            'other'       => 'Other',
        ];
    }
}
