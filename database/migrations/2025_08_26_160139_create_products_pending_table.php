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
        Schema::create('products_pending', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('point', 12, 1);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_pending');
    }
};
