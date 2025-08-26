<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user yang sudah login boleh melihat daftar customer
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Semua user yang sudah login boleh melihat detail customer
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya Admin (roles == 1) yang bisa membuat customer baru
        return $user->roles == 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Hanya Admin (roles == 1) yang bisa mengedit customer
        return $user->roles == 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Hanya Admin (roles == 1) yang bisa menghapus customer
        return $user->roles == 1;
    }
}