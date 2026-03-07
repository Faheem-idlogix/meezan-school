<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class FeeStructure extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'amount'       => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_room_id', $classId);
    }

    // ── Helpers ──

    public static function feeCategories(): array
    {
        return [
            'tuition'    => 'Tuition Fee',
            'admission'  => 'Admission Fee',
            'exam'       => 'Exam Fee',
            'transport'  => 'Transport Fee',
            'lab'        => 'Lab Fee',
            'sports'     => 'Sports Fee',
            'library'    => 'Library Fee',
            'computer'   => 'Computer Fee',
            'stationery' => 'Stationery Charges',
            'uniform'    => 'Uniform Fee',
            'books'      => 'Books Fee',
            'other'      => 'Other',
        ];
    }
}
