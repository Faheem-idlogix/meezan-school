<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class FeeInstallment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee'    => 'decimal:2',
        'due_date'    => 'date',
        'paid_date'   => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(FeeInstallmentPlan::class, 'fee_installment_plan_id');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')->where('due_date', '<', now());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Helpers ──

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'paid'    => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-info">Partial</span>',
            'overdue' => '<span class="badge bg-danger">Overdue</span>',
            default   => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->due_date < now();
    }
}
