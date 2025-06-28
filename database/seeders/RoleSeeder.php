<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Super Gestionnaire – toutes les permissions
        $super = Role::firstOrCreate(['name' => 'Super Gestionnaire']);
        $super->syncPermissions(Permission::all());

        // 2. Gestionnaire Multi-Sites
        $multiSite = Role::firstOrCreate(['name' => 'Gestionnaire Multi-Sites']);
        $multiSite->syncPermissions(Permission::whereIn('name', [
            'station-list', 'station-edit',
            'fuel-reception-list', 'fuel-reception-create', 'fuel-reception-edit',
            'client-list', 'client-edit',
            'expense-list', 'expense-create', 'expense-edit',
            // Ajouter plus selon les besoins
        ])->get());

        // 3. Gestionnaire de Site Unique
        $singleSite = Role::firstOrCreate(['name' => 'Gestionnaire de Site Unique']);
        $singleSite->syncPermissions(Permission::whereIn('name', [
            'station-list',
            'fuel-reception-list', 'fuel-reception-create',
            'expense-list', 'expense-create',
            'client-list', 'client-create', 'client-edit',
        ])->get());

        // 4. Opérateur Ventes/Approvisionnement (modification)
        $opMod = Role::firstOrCreate(['name' => 'Opérateur Approvisionnement-Vente (modification)']);
        $opMod->syncPermissions(Permission::whereIn('name', [
            'fuel-reception-create', 'fuel-reception-edit',
            'lubricant-reception-create', 'lubricant-reception-edit',
            'credit-topup-create', 'credit-payment-create',
            'create-daily-product-sales',
            'create-daily-simple-revenues',
        ])->get());

        // 5. Opérateur Ventes/Approvisionnement (lecture seule)
        $opRead = Role::firstOrCreate(['name' => 'Opérateur Approvisionnement-Vente (lecture seule)']);
        $opRead->syncPermissions(Permission::whereIn('name', [
            'fuel-reception-list', 'lubricant-reception-list',
            'credit-topup-list', 'credit-payment-list',
            'view-daily-product-sales', 'view-daily-simple-revenues',
        ])->get());
    }
}
