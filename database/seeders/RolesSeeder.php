<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'gestionnaire']); // gère une/plusieurs éditions
        Role::firstOrCreate(['name' => 'moderateur']);   // modère programme/intervenants/galerie

        $admin = User::firstOrCreate(
            ['email' => 'admin@cerfi.bf'],
            ['name' => 'Administrateur FOLEM', 'password' => bcrypt('password')]
        );

        $admin->assignRole($superAdmin);
    }
}
