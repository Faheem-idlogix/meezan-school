<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class VoucherItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
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
