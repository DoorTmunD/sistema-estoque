<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
 public function run(): void
{
    // Common inicial — usa updateOrCreate para não duplicar
    User::updateOrCreate(
        ['email' => 'common@exemplo.com'],
        [
            'name'              => 'User Common',
            'password'          => bcrypt('senha123'),
            'nivel'             => 'common',
            'email_verified_at' => now(),
        ]
    );

    // Outros usuários de demonstração
    User::factory()->count(5)->create();
}
}