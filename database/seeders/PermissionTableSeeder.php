<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
         ];

         $permissions = [
             'user-list',
             'user-create',
             'user-edit',
             'user-delete',
             'user-active',
          ];


          $permissions = [
             'station-list',
             'station-create',
             'station-edit',
             'station-delete',
             'station-active',
          ];


          $permissions = [
             'category-list',
             'category-create',
             'category-edit',
             'category-delete',
          ];


          $permissions = [
             'product-list',
             'product-create',
             'product-edit',
             'product-delete',

          ];

          $permissions = [
             'suppliers-list',
             'suppliers-create',
             'suppliers-edit',
             'suppliers-delete',

          ];

          $permissions = [
             'clients-list',
             'clients-create',
             'clients-edit',
             'clients-delete',
          ];



 $permissions = [
    'station-associate',
 ];
 */

        $permissions = [
            /*  //categories
           'category-list',
           'category-create',
           'category-edit',
           'category-delete',

              //produits
              'product-list',
              'product-create',
              'product-edit',
              'product-delete',

               //fournisseurs
              'supplier-list',
              'supplier-create',
              'supplier-edit',
              'supplier-delete',

                  //fournisseurs
                  'client-list',
                  'client-create',
                  'client-view',
                  'client-edit',
                  'client-delete',*/

            // Rubrique des Depenses
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
