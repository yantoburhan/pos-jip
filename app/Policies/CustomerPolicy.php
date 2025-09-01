<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /** Boleh lihat daftar customer */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_customers');
    }

    /** Boleh lihat detail customer */
    public function view(User $user, Customer $customer): bool
    {
        return $user->hasFeature('view_customers');
    }

    /** Boleh membuat customer baru */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_customers');
    }

    public function updateAny(User $user): bool
    {
        // Logika ini sama dengan pengecekan izin umum
        return $user->hasFeature('update_customers'); 
    }

    /** Boleh update data customer */
    public function update(User $user, Customer $customer): bool
    {
        return $user->hasFeature('update_customers');
    }

    public function deleteAny(User $user): bool
    {
        // Logika ini sama dengan pengecekan izin umum
        return $user->hasFeature('delete_customers'); 
    }

    /** Boleh hapus customer */
    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasFeature('delete_customers');
    }

    public function viewOpsi(User $user): bool
    {
        return $user->hasFeature('update_customers') || $user->hasFeature('delete_customers');
    }
}
