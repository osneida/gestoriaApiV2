<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeed extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'AGENCY',  'description' => 'Rol gestoría'],
            ['name' => 'COMPANY', 'description' => 'Rol empresa'],
            ['name' => 'WORKER',  'description' => 'Rol trabajador']
        ];

        Role::insert($roles);
    }
}
