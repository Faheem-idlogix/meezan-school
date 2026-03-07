<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /* ── Relationships ── */

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /* ── Helpers ── */

    public function givePermission(...$permissions): void
    {
        $ids = Permission::whereIn('name', collect($permissions)->flatten())->pluck('id');
        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function revokePermission(...$permissions): void
    {
        $ids = Permission::whereIn('name', collect($permissions)->flatten())->pluck('id');
        $this->permissions()->detach($ids);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /* ── Scopes ── */

    public function scopeSystem($q)
    {
        return $q->where('is_system', true);
    }
}
