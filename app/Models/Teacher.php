<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Teacher extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $guarded = ['id'];
    public function leaveRequests()
    {
        return $this->morphMany(LeaveRequest::class, 'leavable');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function advances()
    {
        return $this->hasMany(PayrollAdvance::class);
    }

    public function pendingAdvances()
    {
        return $this->hasMany(PayrollAdvance::class)->where('is_deducted', false);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }}
