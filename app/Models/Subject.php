<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class Subject extends Model
{
    use SoftDeletes, LogsActivity;
    use HasFactory;
    protected $guarded = ['id'];

    public function classSubject()
    {
        return $this->hasMany(ClassSubject::class, 'subject_id');
    }
}
