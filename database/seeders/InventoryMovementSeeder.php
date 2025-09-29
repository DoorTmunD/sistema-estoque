<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;

class InventoryMovementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('nivel', 'super-admin')->first() ?? User::first();
        $products = Product::all();

        foreach ($products as $product) {
            InventoryMovement::updateOrCreate(
                [
                    'product_id'     => $product->id,
                    'type'           => 'in',
                    'notes'          => 'Entrada inicial para testes',
                ],
                [
                    'quantity'       => 10,
                    'before_stock'   => max(0, $product->quantity - 10),
                    'after_stock'    => $product->quantity,
                    'unit_price'     => $product->unit_price,
                    'total_price'    => $product->unit_price * 10,
                    'responsible_id' => $admin->id ?? 1,
                ]
            );

            InventoryMovement::updateOrCreate(
                [
                    'product_id'     => $product->id,
                    'type'           => 'out',
                    'notes'          => 'Saída para testes',
                ],
                [
                    'quantity'       => 2,
                    'before_stock'   => $product->quantity,
                    'after_stock'    => max(0, $product->quantity - 2),
                    'unit_price'     => $product->unit_price,
                    'total_price'    => $product->unit_price * 2,
                    'responsible_id' => $admin->id ?? 1,
                ]
            );
        }
    }
}