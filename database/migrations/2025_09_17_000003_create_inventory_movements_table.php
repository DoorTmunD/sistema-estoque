<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('product_item_id')->nullable()->constrained('product_items')->nullOnDelete();
                $table->string('type', 30); // ENTRY, LOAN_OUT, LOAN_RETURN, CONSUMPTION, ADJUST
                $table->integer('qty')->default(0);
                $table->integer('before_stock')->nullable();
                $table->integer('after_stock')->nullable();
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->decimal('total_cost', 12, 2)->nullable();
                $table->foreignId('collaborator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('performed_at')->nullable();
                $table->text('notes')->nullable();
                $table->json('properties')->nullable(); // em SQLite vira TEXT
                $table->timestamps();

                $table->index(['product_id', 'type']);
                $table->index(['performed_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
