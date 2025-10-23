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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi');
            $table->foreignId('id_product')->constrained('products')->onDelete('cascade');
            $table->float('quantity', 8, 2);
            $table->integer('point_per_item');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('total_price');
            $table->timestamps();

            $table->foreign('no_transaksi')->references('no_transaksi')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
