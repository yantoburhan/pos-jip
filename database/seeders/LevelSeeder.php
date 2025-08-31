<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat level default "N/A" yang dibutuhkan oleh CustomerController.
        // Seeder ini memastikan level ini selalu ada.
        Level::firstOrCreate(
            ['name' => 'N/A'], // Cari berdasarkan nama untuk menghindari duplikat
            ['level_point' => 0] // Buat dengan poin 0 jika belum ada
        );

        // Anda bisa menambahkan level lain di sini untuk data awal
        // Contoh (bisa di-uncomment jika perlu):
        /*
        Level::firstOrCreate(
            ['name' => 'Bronze'],
            ['level_point' => 100]
        );
        Level::firstOrCreate(
            ['name' => 'Silver'],
            ['level_point' => 500]
        );
        Level::firstOrCreate(
            ['name' => 'Gold'],
            ['level_point' => 1000]
        );
        */
    }
}