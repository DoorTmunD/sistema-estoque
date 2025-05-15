<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Exemplo de produtos de demonstração
        $samples = [
            [
                'name'        => 'Produto A',
                'description' => 'Descrição do Produto A',
                'category_id' => 1,
                'supplier_id' => 1,
                'quantity'    => 10,
                'unit_price'  => 15.50,
            ],
            // ... outros produtos
        ];

        foreach ($samples as $data) {
            // 1) Cria o produto
            $product = Product::create([
                'name'             => $data['name'],
                'description'      => $data['description'],
                'category_id'      => $data['category_id'],
                'supplier_id'      => $data['supplier_id'],
                'quantity'         => $data['quantity'],
                'unit_price'       => $data['unit_price'],
            ]);

            // 2) Registra movimentação de entrada
            InventoryMovement::create([
                'product_id'     => $product->id,
                'type'           => 'in',
                'quantity'       => $data['quantity'],
                'unit_price'     => $data['unit_price'],
                'total_price'    => $data['quantity'] * $data['unit_price'],
                'responsible_id' => Auth::id() ?: 1, // fallback
            ]);

            // 3) Atualiza snapshot de estoque
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $product->id],
                ['qnt_estoque' => 0, 'qnt_ideal' => 0]
            );
            $inventory->increment('qnt_estoque', $data['quantity']);
        }
    }
}