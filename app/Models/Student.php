<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Student extends Model
{
    use SoftDeletes;

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
}
