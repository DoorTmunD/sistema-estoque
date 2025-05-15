<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Common inicial
        User::factory()->create([
            'name' => 'User Common',
            'email' => 'common@exemplo.com',
            'password' => bcrypt('senha123'),
            'nivel' => 'common',
        ]);

        // Outros usuários
        User::factory()->count(5)->create();
    }
}
