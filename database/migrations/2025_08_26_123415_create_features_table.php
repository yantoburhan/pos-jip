<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_features_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ex: manage_products, approve_data
            $table->string('group');          // ex: Users, Products (KOLOM BARU DITAMBAHKAN)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
