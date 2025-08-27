<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /** Boleh lihat daftar produk */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_products');
    }

    /** Boleh lihat detail produk */
    public function view(User $user, Product $product): bool
    {
        return $user->hasFeature('view_products');
    }

    /** Boleh membuat produk baru */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_products');
    }

    /** Boleh mengupdate produk */
    public function update(User $user, Product $product): bool
    {
        return $user->hasFeature('update_products');
    }

    /** Boleh menghapus produk */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasFeature('delete_products');
    }

    public function viewOpsi(User $user): bool
    {
        return $user->hasFeature('update_products') || $user->hasFeature('delete_products');
    }
}
