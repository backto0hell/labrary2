<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHookLogsTable extends Migration
{
    public function up()
    {
        Schema::create('hook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('action');
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hook_logs');
    }
}
