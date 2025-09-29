<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'avg_cost')) {
                $table->decimal('avg_cost', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path')->nullable();
            }
            if (!Schema::hasColumn('products', 'code')) {
                $table->string('code', 24)->nullable()->unique();
            }
            if (!Schema::hasColumn('products', 'is_consumable')) {
                $table->boolean('is_consumable')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Em SQLite, dropar colunas exige DBAL. Para segurança, não removemos nada no down.
        // Se precisar mesmo reverter, crie uma migration específica com DBAL em outro momento.
    }
};
