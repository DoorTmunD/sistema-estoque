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
                $table->string('type', 20); // ENTRY, LOAN_OUT, LOAN_RETURN, CONSUMPTION, ADJUST
                $table->integer('qty')->default(0);
                $table->integer('before_stock')->nullable();
                $table->integer('after_stock')->nullable();
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->decimal('total_cost', 10, 2)->nullable();
                $table->foreignId('collaborator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('performed_at')->nullable();
                $table->text('notes')->nullable();
                $table->json('properties')->nullable();
                $table->timestamps();
            });
            return;
        }

        // Se já existe, só garante as colunas novas
        Schema::table('inventory_movements', function (Blueprint $table) {
            foreach ([
                'product_item_id' => fn() => $table->foreignId('product_item_id')->nullable()->constrained('product_items')->nullOnDelete(),
                'type'            => fn() => $table->string('type', 20)->default('ENTRY'),
                'qty'             => fn() => $table->integer('qty')->default(0),
                'before_stock'    => fn() => $table->integer('before_stock')->nullable(),
                'after_stock'     => fn() => $table->integer('after_stock')->nullable(),
                'unit_cost'       => fn() => $table->decimal('unit_cost', 10, 2)->nullable(),
                'total_cost'      => fn() => $table->decimal('total_cost', 10, 2)->nullable(),
                'collaborator_id' => fn() => $table->foreignId('collaborator_id')->nullable()->constrained('users')->nullOnDelete(),
                'performed_by'    => fn() => $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete(),
                'performed_at'    => fn() => $table->timestamp('performed_at')->nullable(),
                'properties'      => fn() => $table->json('properties')->nullable(),
            ] as $col => $adder) {
                if (!Schema::hasColumn('inventory_movements', $col)) {
                    $adder();
                }
            }
        });
    }

    public function down(): void
    {
        // Sem remoções para compatibilidade com SQLite.
    }
};
