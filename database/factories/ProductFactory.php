<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'price_custo' => $this->faker->randomFloat(2, 10, 2000),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
