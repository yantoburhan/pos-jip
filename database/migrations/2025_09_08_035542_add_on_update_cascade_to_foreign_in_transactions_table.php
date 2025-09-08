<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // 1. Hapus foreign key constraint yang lama
            // Nama constraint default biasanya: nama_tabel_nama_kolom_foreign
            $table->dropForeign('transactions_no_hp_cust_foreign');

            // 2. Tambahkan kembali foreign key dengan onUpdate('cascade')
            $table->foreign('no_hp_cust')
                  ->references('no_hp_cust')
                  ->on('customers')
                  ->onUpdate('cascade') // <-- INI BAGIAN PENTINGNYA
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Logika untuk membatalkan (rollback) migration
            $table->dropForeign(['no_hp_cust']);

            $table->foreign('no_hp_cust')
                  ->references('no_hp_cust')
                  ->on('customers')
                  ->onDelete('restrict');
        });
    }
};