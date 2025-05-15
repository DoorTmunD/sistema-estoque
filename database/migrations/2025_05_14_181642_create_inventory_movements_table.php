<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementsTable extends Migration
{
    public function up()
{
    Schema::create('inventory_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')
              ->constrained()
              ->cascadeOnDelete();
        $table->enum('type', ['in','out']);
        $table->integer('quantity');
        $table->decimal('unit_price', 10, 2)->default(0.00);
        $table->decimal('total_price', 10, 2)->default(0.00);
        $table->foreignId('responsible_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();
        $table->text('notes')->nullable();
        $table->timestamps();
        // índices para acelerar consultas
        $table->index('product_id');
        $table->index('type');
    });
}

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
}