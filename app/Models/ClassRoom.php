<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class ClassRoom extends Model
{
    use SoftDeletes, LogsActivity;

    use HasFactory;
    protected $guarded = ['id'];

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
    public function student()
    {
        return $this->hasMany(Student::class, 'id');
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_room_id');
    }

    public function classSubject()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'class_room_id');
    }

    public function lateFeeRules()
    {
        return $this->hasMany(LateFeeRule::class, 'class_room_id');
    }

    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'class_room_id');
    }
}
