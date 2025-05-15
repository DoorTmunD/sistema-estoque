<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DemoUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'demo@teste.com'],
            [
                'name'     => 'Demo Super Admin',
                'password' => bcrypt('secret123'),
                'nivel'    => 'super-admin',
            ]
        );
    }
}