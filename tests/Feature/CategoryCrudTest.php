<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['nivel' => 'adm']);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_create_category()
    {
        $response = $this->post('/categories', [
            'name' => 'Test Category',
            'description' => 'Description',
        ]);
        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    public function test_validation_error_on_duplicate_name()
    {
        Category::factory()->create(['name' => 'Unique']);
        $response = $this->post('/categories', [
            'name' => 'Unique',
        ]);
        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create();
        $response = $this->put("/categories/{$category->id}", [
            'name' => 'Updated',
            'description' => 'Updated desc',
        ]);
        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Updated']);
    }

    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete("/categories/{$category->id}");
        $response->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
