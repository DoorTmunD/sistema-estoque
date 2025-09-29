<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movement_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_movement_id')
                  ->constrained('inventory_movements')
                  ->onDelete('cascade');
            $table->string('file_path');         // Caminho do arquivo no storage
            $table->string('original_name');     // Nome original enviado
            $table->string('extension', 12);     // Extensão (jpg, pdf, etc)
            $table->string('mime_type', 48);     // MIME type (image/jpeg, application/pdf)
            $table->unsignedBigInteger('size');  // Tamanho em bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement_files');
    }
};