<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Plan extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = ['features' => 'array'];

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function hasFeature(string $key): bool
    {
        return ($this->features[$key] ?? false) === true;
    }
}
