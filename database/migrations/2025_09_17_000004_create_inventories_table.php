<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventories')) {
            Schema::create('inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->unique()->constrained('products')->cascadeOnDelete();
                $table->integer('qnt_estoque')->default(0);
                $table->integer('qnt_ideal')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('inventories', function (Blueprint $table) {
                if (!Schema::hasColumn('inventories', 'qnt_estoque')) $table->integer('qnt_estoque')->default(0);
                if (!Schema::hasColumn('inventories', 'qnt_ideal'))   $table->integer('qnt_ideal')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
