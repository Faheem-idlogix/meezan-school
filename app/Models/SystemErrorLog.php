<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemErrorLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'context' => 'array',
    ];
}
