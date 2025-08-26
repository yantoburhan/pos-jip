<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->roles == 1; // Hanya Admin
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return true; // Semua user bisa melihat data user, termasuk dirinya sendiri
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->roles == 1; // hanya admin yang bisa membuat user baru
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->roles == 1; // hanya admin yang bisa mengupdate user
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->roles == 1; // hanya admin yang bisa menghapus user, kecuali dirinya sendiri
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->roles == 1; // hanya admin yang bisa mengembalikan user yang dihapus
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->roles == 1; // hanya admin yang bisa menghapus permanen user
    }

    /**
     * Aturan untuk siapa yang bisa melihat kolom Opsi.
     * Tetap hanya Admin.
     */
    public function viewOpsi(User $user): bool
    {
        return $user->roles == 1;
    }
}
