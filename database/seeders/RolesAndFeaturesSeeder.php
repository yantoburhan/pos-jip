<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1. Insert Features
        // =========================
        $features = [
            // User management
            ['name' => 'manage_users'],
            ['name' => 'view_users'],
            ['name' => 'create_users'],
            ['name' => 'update_users'],
            ['name' => 'delete_users'],
            ['name' => 'restore_users'],
            ['name' => 'force_delete_users'],

            // Product management
            ['name' => 'view_products'],
            ['name' => 'create_products'],
            ['name' => 'update_products'],
            ['name' => 'delete_products'],

            // Level management
            ['name' => 'view_levels'],
            ['name' => 'create_levels'],
            ['name' => 'update_levels'],
            ['name' => 'delete_levels'],
            ['name' => 'manage_levels'],

            // Customer management
            ['name' => 'view_customers'],
            ['name' => 'create_customers'],
            ['name' => 'update_customers'],
            ['name' => 'delete_customers'],
        ];

        DB::table('features')->insert($features);

        // Ambil semua feature ID (pluck menghasilkan array [name => id])
        $allFeatures = DB::table('features')->pluck('id', 'name');

        // =========================
        // 2. Insert Roles
        // =========================
        $roles = [
            ['id' => 1, 'name' => 'Admin'],   // Full akses
            ['id' => 2, 'name' => 'Kasir'],   // Customer & Produk (partial)
            ['id' => 3, 'name' => 'Staff'],   // Hanya view (read only)
        ];

        DB::table('roles')->insert($roles);

        // =========================
        // 3. Hubungkan Role dengan Features
        // =========================

        // --- Admin: semua fitur ---
        $roleFeatureAdmin = [];
        foreach ($allFeatures as $featureName => $featureId) {
            $roleFeatureAdmin[] = [
                'role_id' => 1, // Admin
                'feature_id' => $featureId,
            ];
        }
        DB::table('role_feature')->insert($roleFeatureAdmin);

        // --- Kasir: hanya customer (buat & lihat) + produk (buat & lihat) ---
        $kasirFeatures = [
            'view_customers',
            'create_customers',
            'view_products',
            'create_products',
        ];
        $roleFeatureKasir = [];
        foreach ($kasirFeatures as $fname) {
            $roleFeatureKasir[] = [
                'role_id' => 2, // Kasir
                'feature_id' => $allFeatures[$fname],
            ];
        }
        DB::table('role_feature')->insert($roleFeatureKasir);

        // --- Staff: hanya bisa view produk & customer ---
        $staffFeatures = [
            'view_products',
            'view_customers',
        ];
        $roleFeatureStaff = [];
        foreach ($staffFeatures as $fname) {
            $roleFeatureStaff[] = [
                'role_id' => 3, // Staff
                'feature_id' => $allFeatures[$fname],
            ];
        }
        DB::table('role_feature')->insert($roleFeatureStaff);
    }
}
