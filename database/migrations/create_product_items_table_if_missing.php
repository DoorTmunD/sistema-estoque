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
                $table->string('serial_internal', 64)->nullable()->unique();
                $table->string('serial_external', 100)->nullable();
                $table->string('status', 16)->default('AVAILABLE'); // AVAILABLE, LOANED, CONSUMED, BROKEN, LOST
                $table->foreignId('current_holder_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('acquired_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
