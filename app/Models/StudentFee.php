<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $guarded = ['student_fee_id'];
    protected $primaryKey = 'student_fee_id'; // Replace with the actual primary key column name



    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function class_fee_voucher()
    {
        return $this->belongsTo(ClassFeeVoucher::class, 'class_fee_voucher_id');
    }

    public function getIssueDateAttribute($value)
    {
        // Convert the date to your desired format
        return date('d F Y', strtotime($value));
    }

    // Accessor for submit_date
    public function getSubmitDateAttribute($value)
    {
        // Convert the date to your desired format
        return date('d F Y', strtotime($value));
    }


    public static function generateUniqueVoucherNumber()
    {
        // Generate a random 5-digit integer between 10000 and 99999
        $voucherNumber = mt_rand(10000, 99999);

        // Check if the generated voucher number already exists in the database
        while (StudentFee::where('voucher_no', $voucherNumber)->exists()) {
            // Regenerate the random part until a unique voucher number is generated
            $voucherNumber = mt_rand(10000, 99999);
        }

        return $voucherNumber;
    }
}
