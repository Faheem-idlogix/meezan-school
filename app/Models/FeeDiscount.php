<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class FeeDiscount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active'      => 'boolean',
        'valid_from'     => 'date',
        'valid_until'    => 'date',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function studentDiscounts()
    {
        return $this->hasMany(StudentFeeDiscount::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_fee_discounts');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        });
    }

    // ── Helpers ──

    public static function discountTypes(): array
    {
        return [
            'all'        => 'All Students',
            'sibling'    => 'Sibling Discount',
            'merit'      => 'Merit Based',
            'need_based' => 'Need Based',
            'staff_child' => 'Staff Child',
        ];
    }
}
