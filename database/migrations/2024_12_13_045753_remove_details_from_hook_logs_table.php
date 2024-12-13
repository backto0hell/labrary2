<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hook_logs', function (Blueprint $table) {
            $table->dropColumn('details');  // Удаление столбца
        });
    }

    public function down()
    {
        Schema::table('hook_logs', function (Blueprint $table) {
            $table->text('details')->nullable();  // Добавление столбца обратно, если нужно
        });
    }
};
