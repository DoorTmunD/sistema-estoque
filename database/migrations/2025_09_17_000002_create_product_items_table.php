<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('product_items')) {
            Schema::create('product_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('serial_internal')->nullable()->unique(); // gerado no boot() do model
                $table->string('serial_external')->nullable();
                $table->enum('status', ['AVAILABLE','LOANED','CONSUMED','BROKEN','LOST'])->default('AVAILABLE');
                $table->foreignId('current_holder_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('acquired_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('product_items', function (Blueprint $table) {
                if (!Schema::hasColumn('product_items', 'serial_internal')) $table->string('serial_internal')->nullable()->unique();
                if (!Schema::hasColumn('product_items', 'serial_external')) $table->string('serial_external')->nullable();
                if (!Schema::hasColumn('product_items', 'status'))         $table->enum('status', ['AVAILABLE','LOANED','CONSUMED','BROKEN','LOST'])->default('AVAILABLE');
                if (!Schema::hasColumn('product_items', 'current_holder_id')) $table->foreignId('current_holder_id')->nullable()->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('product_items', 'acquired_at'))    $table->timestamp('acquired_at')->nullable();
                if (!Schema::hasColumn('product_items', 'notes'))          $table->text('notes')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
