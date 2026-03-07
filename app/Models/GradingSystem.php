<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class GradingSystem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function gradeRules()
    {
        return $this->hasMany(GradeRule::class)->orderBy('min_percentage', 'desc');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'grading_system_id');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ──

    /**
     * Get the grade for a given percentage.
     */
    public function getGradeForPercentage(float $percentage): ?GradeRule
    {
        return $this->gradeRules
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }
}
