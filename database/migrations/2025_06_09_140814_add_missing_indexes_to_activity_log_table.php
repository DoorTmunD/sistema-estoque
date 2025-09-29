<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingIndexesToActivityLogTable extends Migration
{
public function up()
{
    Schema::table('activity_log', function (Blueprint $table) {
        // Se já existir, vai ignorar o erro, sem prejuízo
        $table->index('causer_id');
        $table->index('created_at');
    });
}

public function down()
{
    Schema::table('activity_log', function (Blueprint $table) {
        $table->dropIndex(['causer_id']);
        $table->dropIndex(['created_at']);
    });
}
}