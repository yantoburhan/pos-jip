<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Menentukan apakah user boleh melihat daftar semua user
     */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_users');
    }

    /**
     * Menentukan apakah user boleh melihat detail user tertentu
     * Bisa juga akses diri sendiri walau tidak punya fitur
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasFeature('view_users') || $user->id === $model->id;
    }

    /**
     * Menentukan apakah user boleh membuat user baru
     */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_users');
    }

    /**
     * Menentukan apakah user boleh mengupdate user tertentu
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasFeature('update_users');
    }

    /**
     * Menentukan apakah user boleh menghapus user tertentu
     * Tidak boleh menghapus dirinya sendiri
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->hasFeature('delete_users');
    }

    /**
     * Menentukan apakah user boleh restore user yang dihapus
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasFeature('restore_users');
    }

    /**
     * Menentukan apakah user boleh force delete user (hapus permanen)
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasFeature('force_delete_users');
    }

    /**
     * Menentukan apakah user boleh melihat opsi tambahan user
     * (misalnya tombol setting di UI)
     */
    public function viewOpsi(User $user): bool
    {
        return $user->hasFeature('manage_users');
    }
}
