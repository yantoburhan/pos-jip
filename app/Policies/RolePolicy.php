<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Boleh melihat daftar role.
     */
    public function viewAny(User $user): bool
    {
        // Izin diberikan jika user memiliki feature 'view_roles' ATAU 'manage_roles'
        return $user->hasFeature('view_roles') || $user->hasFeature('manage_roles');
    }

    /**
     * Boleh melihat detail role.
     */
    public function view(User $user, Role $role): bool
    {
        // Izin diberikan jika user memiliki feature 'view_roles' ATAU 'manage_roles'
        return $user->hasFeature('view_roles') || $user->hasFeature('manage_roles');
    }

    /**
     * Boleh membuat role baru.
     */
    public function create(User $user): bool
    {
        // Izin hanya untuk yang bisa mengelola role
        return $user->hasFeature('manage_roles');
    }

    /**
     * Boleh mengupdate role.
     */
    public function update(User $user, Role $role): bool
    {
        // Izin hanya untuk yang bisa mengelola role
        return $user->hasFeature('manage_roles');
    }

    /**
     * Boleh menghapus role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Izin hanya untuk yang bisa mengelola role
        return $user->hasFeature('manage_roles');
    }
}
