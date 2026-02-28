<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id')->withTrashed();
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
