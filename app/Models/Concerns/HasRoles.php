<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->rolePermissions()->contains($permission)) {
            return true;
        }

        return $this->directPermissions->pluck('name')->contains($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function rolePermissions(): Collection
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        return $this->roles->flatMap(fn (Role $role) => $role->permissions->pluck('name'))->unique();
    }

    public function assignRole(string|Role $role): void
    {
        $roleModel = $role instanceof Role ? $role : Role::where('name', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$roleModel->id]);
    }
}
