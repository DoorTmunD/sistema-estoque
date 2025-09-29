<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'avg_cost')) {
                $table->decimal('avg_cost', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'is_consumable')) {
                $table->boolean('is_consumable')->default(false);
            }
            if (!Schema::hasColumn('products', 'code')) {
                $table->string('code', 50)->nullable();
            }
            if (!Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path')->nullable();
            }
            // (Se já existir unit_price, mantém. Se não existir e quiser usar, crie aqui)
            if (!Schema::hasColumn('products', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remover só o que foi adicionado aqui (opcional em dev)
            if (Schema::hasColumn('products', 'category_id'))   $table->dropConstrainedForeignId('category_id');
            if (Schema::hasColumn('products', 'supplier_id'))   $table->dropConstrainedForeignId('supplier_id');
            if (Schema::hasColumn('products', 'avg_cost'))      $table->dropColumn('avg_cost');
            if (Schema::hasColumn('products', 'min_stock'))     $table->dropColumn('min_stock');
            if (Schema::hasColumn('products', 'is_consumable')) $table->dropColumn('is_consumable');
            if (Schema::hasColumn('products', 'code'))          $table->dropColumn('code');
            if (Schema::hasColumn('products', 'image_path'))    $table->dropColumn('image_path');
            if (Schema::hasColumn('products', 'unit_price'))    $table->dropColumn('unit_price');
        });
    }
};
