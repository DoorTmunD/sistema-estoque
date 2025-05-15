<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['nivel' => 'adm']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_create_supplier()
    {
        $response = $this->post('/suppliers', [
            'name' => 'Test Supplier',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'address' => 'Address',
        ]);
        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['name' => 'Test Supplier']);
    }

    public function test_admin_can_update_supplier()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->put("/suppliers/{$supplier->id}", [
            'name' => 'Updated Supplier',
            'email' => 'upd@example.com',
            'phone' => '0987654321',
            'address' => 'New address',
        ]);
        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['name' => 'Updated Supplier']);
    }

    public function test_admin_can_delete_supplier()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->delete("/suppliers/{$supplier->id}");
        $response->assertRedirect('/suppliers');
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
