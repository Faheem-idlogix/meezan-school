<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class Student extends Model
{
    use SoftDeletes, LogsActivity;

    use HasFactory;
    protected $guarded = ['id'];

    
    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
    public function leaveRequests()
    {
        return $this->morphMany(LeaveRequest::class, 'leavable');
    }
    public function promotions()
    {
        return $this->hasMany(StudentPromotion::class);
    }
    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }
    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    // ── Student Lifecycle relationships ──

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function behaviors()
    {
        return $this->hasMany(StudentBehavior::class);
    }

    public function transferCertificates()
    {
        return $this->hasMany(TransferCertificate::class);
    }

    public function admissionEnquiry()
    {
        return $this->belongsTo(AdmissionEnquiry::class);
    }

    // ── Advanced Fee relationships ──

    public function feeDiscounts()
    {
        return $this->hasMany(StudentFeeDiscount::class);
    }

    public function installmentPlans()
    {
        return $this->hasMany(FeeInstallmentPlan::class);
    }

    public function feeReminders()
    {
        return $this->hasMany(FeeReminder::class);
    }
}
