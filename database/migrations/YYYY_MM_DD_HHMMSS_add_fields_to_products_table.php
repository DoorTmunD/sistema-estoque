<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            // Código/prefixo (para formar o serial interno) — opcional, único
            $table->string('code', 24)->nullable()->unique()->after('name');

            // Flag de consumível (toner, fita, etc.)
            $table->boolean('is_consumable')->default(false)->after('supplier_id');

            // (Opcional) preço de venda — se quiser usar o price_venda do form
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
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }
};
