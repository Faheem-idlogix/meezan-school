<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class Attendance extends Model
{
    use SoftDeletes, LogsActivity;
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}
