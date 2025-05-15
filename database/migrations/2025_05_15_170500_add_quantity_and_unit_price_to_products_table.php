<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('supplier_id')
                  ->comment('Quantidade inicial do produto');
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity')
                  ->comment('Preço unitário');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_price']);
        });
    }
};