<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['name' => 'Eletrônicos',    'description' => 'Produtos eletrônicos em geral.'],
            ['name' => 'Informática',    'description' => 'Hardware, software e periféricos.'],
            ['name' => 'Escritório',     'description' => 'Materiais de escritório.'],
            // … até 10 itens fixos …
        ];

        foreach ($samples as $data) {
            Category::updateOrCreate(
                ['name' => $data['name']],
                ['description' => $data['description']]
            );
        }
    }
}