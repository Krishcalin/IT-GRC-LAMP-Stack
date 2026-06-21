<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Application user. Mirrors the FastAPI `users` table: the password lives in
 * `hashed_password` (not the Laravel default `password`), and primary keys are UUIDs.
 */
class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'email', 'full_name', 'hashed_password', 'department',
        'is_active', 'is_superuser', 'auth_provider', 'idp_subject_id',
    ];

    protected $hidden = [
        'hashed_password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_superuser' => 'boolean',
        ];
    }

    /** Auth password column is `hashed_password` (to match the source schema). */
    public function getAuthPassword(): ?string
    {
        return $this->hashed_password;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /** Convenience display name. */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->email;
    }

    /**
     * Permission check honouring the seeded permission grammar:
     *   "*"            -> everything
     *   "controls:*"   -> any action on controls
     *   "*:read"       -> read anything
     *   "controls:read"-> exact match
     *   "controls:own" -> treated as a read-capable grant here
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_superuser) {
            return true;
        }

        [$resource, $action] = array_pad(explode(':', $permission, 2), 2, '*');

        foreach ($this->roles as $role) {
            $perms = $role->permissions ?? [];
            if (in_array('*', $perms, true)) {
                return true;
            }
            if (in_array($permission, $perms, true)) {
                return true;
            }
            if (in_array("{$resource}:*", $perms, true)) {
                return true;
            }
            if ($action === 'read') {
                if (in_array('*:read', $perms, true) || in_array("{$resource}:own", $perms, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
