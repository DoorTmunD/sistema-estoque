<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1 = super-admin | 2 = adm | 3 = user
        $roles = [
            ['id' => 1, 'name' => 'super-admin'],
            ['id' => 2, 'name' => 'adm'],
            ['id' => 3, 'name' => 'user'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}