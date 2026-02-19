<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diary extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'diary_date'       => 'date',
        'whatsapp_sent_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_room_id', $classId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('diary_date', today());
    }
}
