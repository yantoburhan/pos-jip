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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('no_transaksi')->primary();
            $table->date('date');
            $table->string('no_hp_cust');
            $table->text('alamat')->nullable();
            $table->enum('wilayah', ['medan', 'luar_medan', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->string('kecamatan')->nullable();
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('total_penjualan')->default(0);
            $table->unsignedBigInteger('total_poin')->default(0);
            $table->timestamps();

            $table->foreign('no_hp_cust')->references('no_hp_cust')->on('customers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
