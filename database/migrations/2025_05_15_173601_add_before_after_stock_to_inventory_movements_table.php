<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/XXXX_add_before_after_stock_to_inventory_movements_table.php
public function up()
{
    Schema::table('inventory_movements', function(Blueprint $t){
        $t->integer('before_stock')->default(0)->after('quantity');
        $t->integer('after_stock')->default(0)->after('before_stock');
    });
}
public function down()
{
    Schema::table('inventory_movements', fn(Blueprint $t) =>
        $t->dropColumn(['before_stock','after_stock'])
    );
}
};