<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClassRoom extends Model
{
    use SoftDeletes;

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
}
