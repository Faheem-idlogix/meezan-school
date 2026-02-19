<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollAdvance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['advance_date' => 'date'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopePending($query)
    {
        return $query->where('is_deducted', false);
    }
}
