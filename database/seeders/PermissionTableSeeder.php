<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [

            // Roles
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Users
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // Stations
            'station-list',
            'station-create',
            'station-edit',
            'station-delete',
            'station-associate',

            // Clients Credit
            'client-credit',

            // CATEGORIES PRODUITS
            'category-list',
            'category-create',
            'category-edit',
            'category-delete',

            // PRODUITS
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            // FOURNISSEURS
            'supplier-list',
            'supplier-create',
            'supplier-view',
            'supplier-edit',
            'supplier-delete',

            // CLIENTS
            'client-list',
            'client-create',
            'client-view',
            'client-edit',
            'client-delete',

            // CATEGORIES DE DEPENSES
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',

            // TANKS
            'tank-list',
            'tank-create',
            'tank-view',
            'tank-edit',
            'tank-delete',

            // FUEL RECEPTIONS
            'fuel-reception-list',
            'fuel-reception-create',
            'fuel-reception-view',
            'fuel-reception-edit',
            'fuel-reception-delete',
            'fuel-reception-export',

            // DEPENSES
            'expense-list',
            'expense-create',
            'expense-view',
            'expense-edit',
            'expense-delete',

            // CONDITIONNEMENTS
            'packaging-list',
            'packaging-create',
            'packaging-edit',
            'packaging-delete',

            // LUBRICANT RECEPTIONS
            'lubricant-reception-list',
            'lubricant-reception-create',
            'lubricant-reception-view',
            'lubricant-reception-edit',
            'lubricant-reception-delete',

            // BALANCES TOPUPS & USAGES
            'balance-topup-list',
            'balance-topup-create',
            'balance-topup-view',
            'balance-topup-edit',
            'balance-topup-delete',

            'balance-usage-list',
            'balance-usage-create',
            'balance-usage-edit',
            'balance-usage-delete',

            'balance-view',

            // CREDITS
            'credit-topup-list',
            'credit-topup-create',
            'credit-topup-view',
            'credit-topup-edit',
            'credit-topup-delete',

            'credit-payment-list',
            'credit-payment-create',
            'credit-payment-edit',
            'credit-payment-delete',

            'credit-view',
            'credit-export',

            // FACTURES D'ACHAT
            'purchase-invoice-list',
            'purchase-invoice-create',
            'purchase-invoice-edit',
            'purchase-invoice-delete',
            'purchase-invoice-export',

            // PRODUCT-PACKAGINGS
            'view-product-packagings',
            'create-product-packagings',
            'edit-product-packagings',
            'delete-product-packagings',

            // FUEL PRODUCTS
            'view-fuel-products',
            'create-fuel-products',
            'edit-fuel-products',
            'delete-fuel-products',

            // LUBRICANT PRODUCTS
            'view-lubricant-products',
            'create-lubricant-products',
            'edit-lubricant-products',
            'delete-lubricant-products',

            'view-transporters',
            'create-transporters',
            'edit-transporters',
            'delete-transporters',

            'view-drivers',
            'create-drivers',
            'edit-drivers',
            'delete-drivers',

            'view-product-packagings',

            'view-pumps',
            'create-pumps',
            'edit-pumps',
            'delete-pumps',

            'view-fuel-indexes',
            'create-fuel-indexes',
            'edit-fuel-indexes',
            'view-fuel-index-details',

            'view-daily-product-sales',
            'create-daily-product-sales',

            'view-daily-simple-revenues',
            'create-daily-simple-revenues',
            'edit-daily-simple-revenues',
            'delete-daily-simple-revenues',

            'view-daily-revenue-review',
            'create-daily-revenue-review',

            'view-daily-revenue-validations',
            'create-daily-revenue-validations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
