<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ReportCardConfig extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'show_grade'      => 'boolean',
        'show_gpa'        => 'boolean',
        'show_position'   => 'boolean',
        'show_percentage' => 'boolean',
        'show_remarks'    => 'boolean',
        'show_attendance' => 'boolean',
        'show_behavior'   => 'boolean',
        'is_default'      => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
