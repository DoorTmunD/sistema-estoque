<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Seriais
            $table->string('internal_serial')->unique();
            $table->string('external_serial')->nullable();

            // Status do item
            $table->enum('status', ['available','loaned','consumed','discarded','defective'])
                  ->default('available');

            // Quem está segurando o item (emprestado)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Datas do ciclo de empréstimo
            $table->timestamp('loaned_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_items');
    }
};
