<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'voucher_code',
        'amount',
        'expiry_date',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }
}
