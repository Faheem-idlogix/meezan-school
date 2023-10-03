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
}
