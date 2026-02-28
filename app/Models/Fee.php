<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class Fee extends Model
{
    use SoftDeletes, LogsActivity;

    use HasFactory;
    protected $guarded = ['id'];

}
