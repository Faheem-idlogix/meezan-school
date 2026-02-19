<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timetable extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // ── Relationships ────────────────────────────────────────────

    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id')->withTrashed();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function session()
    {
        return $this->belongsTo(Session::class)->withTrashed();
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_room_id', $classId)->where('is_active', true);
    }

    public function scopeForDay($query, string $day)
    {
        return $query->where('day', strtolower($day));
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function getDurationAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end) . ' min';
    }
}
