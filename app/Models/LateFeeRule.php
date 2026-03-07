<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class LateFeeRule extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'charge_amount' => 'decimal:2',
        'max_late_fee'  => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate late fee for given overdue days.
     */
    public function calculateLateFee(int $overdueDays, float $feeAmount = 0): float
    {
        if ($overdueDays <= $this->grace_days) return 0;

        $lateDays = $overdueDays - $this->grace_days;

        $fee = match ($this->charge_type) {
            'fixed'      => (float) $this->charge_amount,
            'percentage' => ($feeAmount * (float) $this->charge_amount) / 100,
            'per_day'    => $lateDays * (float) $this->charge_amount,
            default      => 0,
        };

        // Apply cap if set
        if ($this->max_late_fee && $fee > (float) $this->max_late_fee) {
            $fee = (float) $this->max_late_fee;
        }

        return round($fee, 2);
    }
}
