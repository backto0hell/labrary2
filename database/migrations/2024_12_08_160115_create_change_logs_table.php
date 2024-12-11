<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeLogsTable extends Migration
{
    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // Тип сущности (например, 'User', 'Role', 'Permission')
            $table->integer('entity_id'); // ID мутирующей записи
            $table->json('old_value'); // Значение записи до мутации
            $table->json('new_value'); // Значение записи после мутации
            $table->integer('mutated_by'); // ID пользователя, совершившего мутацию
            $table->integer('created_by')->nullable(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('change_logs');
    }
}
