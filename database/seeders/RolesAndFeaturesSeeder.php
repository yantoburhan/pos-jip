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
            ['name' => 'manage_users', 'group' => 'Users'],
            ['name' => 'view_users', 'group' => 'Users'],
            ['name' => 'create_users', 'group' => 'Users'],
            ['name' => 'update_users', 'group' => 'Users'],
            ['name' => 'delete_users', 'group' => 'Users'],
            ['name' => 'restore_users', 'group' => 'Users'],
            ['name' => 'force_delete_users', 'group' => 'Users'],

            // Product management
            ['name' => 'view_products', 'group' => 'Products'],
            ['name' => 'create_products', 'group' => 'Products'],
            ['name' => 'update_products', 'group' => 'Products'],
            ['name' => 'delete_products', 'group' => 'Products'],

            // Level management
            ['name' => 'view_levels', 'group' => 'Levels'],
            ['name' => 'create_levels', 'group' => 'Levels'],
            ['name' => 'update_levels', 'group' => 'Levels'],
            ['name' => 'delete_levels', 'group' => 'Levels'],
            ['name' => 'manage_levels', 'group' => 'Levels'],

            // Customer management
            ['name' => 'view_customers', 'group' => 'Customers'],
            ['name' => 'create_customers', 'group' => 'Customers'],
            ['name' => 'update_customers', 'group' => 'Customers'],
            ['name' => 'delete_customers', 'group' => 'Customers'],

            // Transaction management
            ['name' => 'view_transactions', 'group' => 'Transactions'],
            ['name' => 'create_transactions', 'group' => 'Transactions'],
            ['name' => 'update_transactions', 'group' => 'Transactions'],
            ['name' => 'delete_transactions', 'group' => 'Transactions'],
            
            // --- Izin Pending dengan GRUP BARU ---
            ['name' => 'view_pending_transactions', 'group' => 'Pending Transactions'], // DIUBAH: Nama grup baru
            ['name' => 'approve_transactions', 'group' => 'Pending Transactions'],      // DIUBAH: Nama grup baru
            ['name' => 'update_pending_transactions', 'group' => 'Pending Transactions'],// DIUBAH: Nama grup baru
            ['name' => 'delete_pending_transactions', 'group' => 'Pending Transactions'],// DIUBAH: Nama grup baru

            // Role management
            ['name' => 'manage_roles', 'group' => 'Roles'],
            ['name' => 'view_roles', 'group' => 'Roles'],
        ];

        // Kosongkan tabel sebelum mengisi untuk menghindari duplikat
        DB::table('role_feature')->delete();
        DB::table('features')->delete();
        DB::table('roles')->delete();

        DB::table('features')->insert($features);
        $allFeatures = DB::table('features')->pluck('id', 'name');

        // =========================
        // 2. Insert Roles
        // =========================
        $roles = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Kasir'],
            ['id' => 3, 'name' => 'Staff'],
        ];
        DB::table('roles')->insert($roles);

        // =========================
        // 3. Hubungkan Role dengan Features
        // =========================
        // --- Admin: semua fitur ---
        $roleFeatureAdmin = [];
        foreach ($allFeatures as $featureId) {
            $roleFeatureAdmin[] = ['role_id' => 1, 'feature_id' => $featureId];
        }
        DB::table('role_feature')->insert($roleFeatureAdmin);

        // --- Kasir: ---
        $kasirFeatures = [
            'view_customers',
            'create_customers',
            'view_products',
            'create_products',
            'view_transactions',
            'create_transactions',
            'view_pending_transactions',
            'update_pending_transactions',
            'delete_pending_transactions',
        ];
        $roleFeatureKasir = [];
        foreach ($kasirFeatures as $fname) {
            if (isset($allFeatures[$fname])) {
                $roleFeatureKasir[] = ['role_id' => 2, 'feature_id' => $allFeatures[$fname]];
            }
        }
        DB::table('role_feature')->insert($roleFeatureKasir);

        // --- Staff: ---
        $staffFeatures = [
            'view_products',
            'view_customers',
            'view_transactions',
        ];
        $roleFeatureStaff = [];
        foreach ($staffFeatures as $fname) {
            if (isset($allFeatures[$fname])) {
                $roleFeatureStaff[] = ['role_id' => 3, 'feature_id' => $allFeatures[$fname]];
            }
        }
        DB::table('role_feature')->insert($roleFeatureStaff);
    }
}

