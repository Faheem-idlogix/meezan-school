<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Alumni extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'alumni';
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }
}
