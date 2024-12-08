<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes(); // Для мягкого удаления
            $table->integer('created_by')->nullable(false);
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
