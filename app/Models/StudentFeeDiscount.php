<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class StudentFeeDiscount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'effective_from'  => 'date',
        'effective_until' => 'date',
        'is_active'       => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeDiscount()
    {
        return $this->belongsTo(FeeDiscount::class);
    }
}
