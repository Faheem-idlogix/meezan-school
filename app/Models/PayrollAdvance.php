<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class PayrollAdvance extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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
