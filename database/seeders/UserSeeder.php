<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria um usuário comum
        User::updateOrCreate(
            ['email' => 'common@exemplo.com'],
            [
                'name'              => 'User Common',
                'password'          => bcrypt('senha123'),
                'email_verified_at' => now(),
                'nivel'             => 'common', // use sempre 'common', 'adm', 'operador', 'super-admin'
            ]
        );

        // Outros usuários de demonstração (todos comuns)
        User::factory(5)->create([
            'nivel' => 'common',
        ]);
    }
}