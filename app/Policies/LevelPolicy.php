<?php

namespace App\Policies;

use App\Models\Level;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LevelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Izinkan semua user yang sudah login untuk melihat halaman index produk
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Level $level): bool
    {
        // Izinkan semua user melihat detail produk
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin yang boleh membuat produk baru
        return $user->roles == 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Level $level): bool
    {
        // Hanya admin yang boleh mengedit produk
        return $user->roles == 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Level $level): bool
    {
        // Hanya admin yang boleh menghapus produk
        return $user->roles == 1;
    }
}