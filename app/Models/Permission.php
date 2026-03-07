<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = ['id'];

    /* ── Relationships ── */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }

    /* ── Scopes ── */

    public function scopeModule($q, string $module)
    {
        return $q->where('module', $module);
    }

    public function scopeGroup($q, string $group)
    {
        return $q->where('group', $group);
    }
}
