<?php

namespace Database\Seeders;

use App\Models\User; // <-- JANGAN LUPA IMPORT
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- JANGAN LUPA IMPORT

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat user admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'username' => 'superadmin',
            'password' => Hash::make('rahasia'), // passwordnya adalah "rahasia"
            'roles' => 1, // 1 untuk admin
        ]);
    }
}
