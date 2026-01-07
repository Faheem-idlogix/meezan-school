<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'voucher_id',
        'item_name',
        'item_price',
    ];
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
