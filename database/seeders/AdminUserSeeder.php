<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@exemplo.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('senha123'),
                'role_id'  => 1,
                'nivel'    => 'super-admin',
            ]
        );
    }
}