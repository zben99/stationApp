<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::pluck('name'); // ou une sélection personnalisée

        $user = User::create([
            'name' => 'Ben',
            'email' => 'admin',
            'password' => bcrypt('12345678'),
        ]);

        $role = Role::where('name', 'Super Gestionnaire')->first();

        if ($role) {
            $role->syncPermissions($permissions); // Associe toutes les permissions
            $user->assignRole($role); // Assigne le rôle à l'utilisateur
        } else {
            throw new \Exception("Le rôle 'Super Gestionnaire' est introuvable.");
        }
    }
}
