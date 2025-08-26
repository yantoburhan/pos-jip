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
        Schema::create('customers', function (Blueprint $table) {
            $table->string('no_hp_cust')->primary(); // Kolom string sebagai Primary Key
            $table->string('cust_name');
            $table->integer('cust_point')->default(0);
            $table->unsignedBigInteger('level_id'); // Kolom untuk Foreign Key
            $table->integer('total_spent')->default(0);
            $table->timestamps(); // Kolom created_at dan updated_at

            // Mendefinisikan Foreign Key Constraint
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
