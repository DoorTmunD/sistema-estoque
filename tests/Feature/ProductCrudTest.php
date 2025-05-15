<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\Supplier;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['nivel' => 'adm']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_create_product()
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->post('/products', [
            'name' => 'Test Product',
            'description' => 'Desc',
            'price_custo' => 99.99,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_validation_error_on_missing_fields()
    {
        $response = $this->post('/products', []);
        $response->assertSessionHasErrors(['name', 'price_custo']);
    }

    public function test_admin_can_update_product()
    {
        $product = Product::factory()->create();
        $response = $this->put("/products/{$product->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated desc',
            'price_custo' => 120.50,
            'category_id' => $product->category_id,
            'supplier_id' => $product->supplier_id,
        ]);
        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Updated Name']);
    }

    public function test_admin_can_delete_product()
    {
        $product = Product::factory()->create();
        $response = $this->delete("/products/{$product->id}");
        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
