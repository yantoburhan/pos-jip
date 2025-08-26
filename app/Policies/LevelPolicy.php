<?php

namespace App\Policies;

use App\Models\Level;
use App\Models\User;

class LevelPolicy
{
    /** Boleh lihat daftar level */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_levels');
    }

    /** Boleh lihat detail level */
    public function view(User $user, Level $level): bool
    {
        return $user->hasFeature('view_levels');
    }

    /** Boleh membuat level baru */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_levels');
    }

    /** Boleh mengupdate level */
    public function update(User $user, Level $level): bool
    {
        return $user->hasFeature('update_levels');
    }

    /** Boleh menghapus level */
    public function delete(User $user, Level $level): bool
    {
        return $user->hasFeature('delete_levels');
    }

    /** Boleh melihat opsi tambahan (misalnya setting khusus level) */
    public function viewOpsi(User $user): bool
    {
        return $user->hasFeature('manage_levels');
    }
}
