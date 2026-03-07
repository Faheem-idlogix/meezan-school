<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class FeeInstallmentPlan extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'total_amount'       => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'start_date'         => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ──

    public function getPaidAmountAttribute()
    {
        return $this->installments()->where('status', 'paid')->sum('paid_amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_amount <= 0) return 0;
        return round(($this->paid_amount / $this->total_amount) * 100, 1);
    }
}
