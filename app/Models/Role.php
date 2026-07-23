<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'role_name', 'description', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function hasPermission(string $permissionKey): bool
    {
        // Use already-loaded permissions relation to avoid extra queries
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('permission_key', $permissionKey);
        }
        return $this->permissions()->where('permission_key', $permissionKey)->exists();
    }
}
