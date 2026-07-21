<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable

{
    use \App\Traits\HasFirms;

    use HasFactory, Notifiable;

    protected $fillable = [
        'firm_id', 'role_id', 'name', 'email', 'mobile_number',
        'password', 'role', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime'];
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * Relationship to Role model (via role_id FK).
     * Backward-compatible — blade views using $user->role that check is_object() still work
     * because eager-loaded role() returns the Role model, not the string column.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /** Alias for explicit eager-loading */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Returns the role name string safely regardless of whether role is loaded.
     */
    public function getRoleName(): string
    {
        $role = $this->relationLoaded('role') ? $this->getRelation('role') : null;
        if ($role && is_object($role)) {
            return strtolower($role->role_name ?? $role->name ?? '');
        }
        // Fall back to the string column
        return strtolower($this->attributes['role'] ?? '');
    }

    /**
     * True for Admin, Super Admin, and Firm Admin — these bypass ALL permission checks.
     */
    public function isAdmin(): bool
    {
        $roleStr = strtolower($this->role ?? '');
        if (in_array($roleStr, ['admin', 'super admin', 'superadmin', 'super_admin', 'firm admin', 'firm_admin'])) {
            return true;
        }

        if (!$this->role_id) return false;

        $roleObj = $this->relationLoaded('role') ? $this->getRelation('role') : Role::find($this->role_id);
        if (!$roleObj) return false;

        $name = strtolower($roleObj->role_name ?? $roleObj->name ?? '');
        return in_array($name, ['admin', 'super admin', 'superadmin', 'super_admin', 'firm admin', 'firm_admin']);
    }

    /** Kept for backward compatibility */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if the user has a specific permission key.
     * Admin roles always return true.
     */
    public function hasPermission(string $permissionKey): bool
    {
        if ($this->isAdmin()) return true;
        if (!$this->role_id) return false;

        // Prefer already-loaded role to avoid extra queries
        $roleObj = $this->relationLoaded('role') ? $this->getRelation('role') : null;
        if (!$roleObj) {
            $roleObj = Role::with('permissions')->find($this->role_id);
        } elseif (!$roleObj->relationLoaded('permissions')) {
            $roleObj->load('permissions');
        }

        if (!$roleObj || !is_object($roleObj)) return false;

        return $roleObj->hasPermission($permissionKey);
    }
}
