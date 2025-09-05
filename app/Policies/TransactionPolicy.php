<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Pengecekan super admin. Jika user punya izin ini, semua diizinkan.
     * Izin 'approve_transactions' kita anggap sebagai izin tertinggi.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasFeature('approve_transactions')) {
            return true;
        }
    }

    /**
     * Boleh lihat daftar transaksi (halaman utama).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasFeature('view_transactions');
    }

    /**
     * Boleh lihat detail satu transaksi.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Izinkan jika user punya izin umum, ATAU jika itu transaksi pending miliknya sendiri.
        return $user->hasFeature('view_transactions') || 
               ($transaction->status === 'pending' && $user->id === $transaction->operator_id);
    }

    /**
     * Boleh membuat transaksi baru.
     */
    public function create(User $user): bool
    {
        return $user->hasFeature('create_transactions');
    }

    /**
     * Boleh mengupdate transaksi (LOGIKA CERDAS).
     */
    public function update(User $user, Transaction $transaction): bool
    {
        // Jika transaksi SUDAH DISETUJUI, user harus punya izin 'update_transactions'.
        if ($transaction->status === 'approved') {
            return $user->hasFeature('update_transactions');
        }

        // Jika transaksi MASIH PENDING, user harus punya izin 'update_pending_transactions'
        // DAN dia adalah pembuat transaksi tersebut.
        if ($transaction->status === 'pending') {
            return $user->hasFeature('update_pending_transactions') && $user->id === $transaction->operator_id;
        }

        return false; // Jika statusnya aneh, tolak akses.
    }

    /**
     * Boleh menghapus transaksi (LOGIKA CERDAS).
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        // Jika transaksi SUDAH DISETUJUI, user harus punya izin 'delete_transactions'.
        if ($transaction->status === 'approved') {
            return $user->hasFeature('delete_transactions');
        }

        // Jika transaksi MASIH PENDING, user harus punya izin 'delete_pending_transactions'
        // DAN dia adalah pembuat transaksi tersebut.
        if ($transaction->status === 'pending') {
            return $user->hasFeature('delete_pending_transactions') && $user->id === $transaction->operator_id;
        }

        return false; // Jika statusnya aneh, tolak akses.
    }
}

