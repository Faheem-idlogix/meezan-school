<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Voucher extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'school_id', 'student_id', 'voucher_code', 'amount',
        'expiry_date', 'type', 'category', 'description',
        'reference_no', 'voucher_date', 'payment_mode',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'expiry_date'  => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────
    public function scopeIncome($q)    { return $q->where('type', 'income'); }
    public function scopeExpense($q)   { return $q->where('type', 'expense'); }
    public function scopeThisMonth($q) { return $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year); }
}

