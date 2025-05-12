<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna role_id na tabela users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                // SQLite não suporta ADD CONSTRAINT depois de criada a tabela
                $table->unsignedBigInteger('role_id')->default(1);
            } else {
                $table->foreignId('role_id')
                      ->default(1)
                      ->constrained('roles');
            }
        });
    }

    /**
     * Reverte a alteração.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->dropColumn('role_id');
            } else {
                $table->dropConstrainedForeignId('role_id');
            }
        });
    }
};