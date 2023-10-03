<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassFeeVoucher extends Model
{
    use HasFactory;

    protected $guarded = ['class_fee_voucher_id'];
    protected $primaryKey = 'class_fee_voucher_id'; // Replace with the actual primary key column name

         
    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id');
    }

    public function student_fee()
    {
        return $this->hasMany(StudentFee::class, 'class_fee_voucher_id');
    }
    

}
