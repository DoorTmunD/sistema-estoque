<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            // code (único, opcional) — só cria se não existir
            if (!Schema::hasColumn('products', 'code')) {
                $table->string('code', 24)->nullable()->unique()->after('name');
            }

            // is_consumable — só cria se não existir
            if (!Schema::hasColumn('products', 'is_consumable')) {
                $table->boolean('is_consumable')->default(false)->after('supplier_id');
            }

            // sale_price — já protegido
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sale_price')) {
                $table->dropColumn('sale_price');
            }
            if (Schema::hasColumn('products', 'is_consumable')) {
                $table->dropColumn('is_consumable');
            }
            if (Schema::hasColumn('products', 'code')) {
                // Em alguns drivers o índice único se chama products_code_unique
                try { $table->dropUnique(['code']); } catch (\Throwable $e) {}
                try { $table->dropUnique('products_code_unique'); } catch (\Throwable $e) {}
                $table->dropColumn('code');
            }
        });
    }
};
