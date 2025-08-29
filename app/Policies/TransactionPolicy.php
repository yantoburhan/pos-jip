<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /** Boleh lihat daftar transaksi */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_transactions');
    }

    /** Boleh lihat detail transaksi */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasFeature('view_transactions');
    }

    /** Boleh membuat transaksi baru */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_transactions');
    }

    /** Boleh mengupdate transaksi */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasFeature('update_transactions');
    }

    /** Boleh menghapus transaksi */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasFeature('delete_transactions');
    }

    /** BARU: Boleh melihat kolom Opsi */
    public function viewOpsi(User $user): bool
    {
        return $user->hasFeature('view_transactions') || $user->hasFeature('update_transactions') || $user->hasFeature('delete_transactions');
    }
}

