<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // item da movimentação (ex.: empréstimo do item X)
            if (!Schema::hasColumn('inventory_movements', 'item_id')) {
                $table->foreignId('item_id')
                      ->nullable()
                      ->after('product_id')
                      ->constrained('product_items')
                      ->nullOnDelete();
            }

            // colaborador envolvido (quem recebe o empréstimo)
            if (!Schema::hasColumn('inventory_movements', 'collaborator_id')) {
                $table->foreignId('collaborator_id')
                      ->nullable()
                      ->after('responsible_id')
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'collaborator_id')) {
                $table->dropConstrainedForeignId('collaborator_id');
            }
            if (Schema::hasColumn('inventory_movements', 'item_id')) {
                $table->dropConstrainedForeignId('item_id');
            }
        });
    }
};
