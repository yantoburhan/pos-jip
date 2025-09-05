<?php // Anda boleh menambahkan tag php jika mau, tapi tidak wajib

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom status di akhir tabel. Ini aman.
            $table->enum('status', ['pending', 'approved'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};