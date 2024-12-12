<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_2fa_enabled')->default(false);
            $table->integer('two_fa_code')->nullable();
            $table->timestamp('two_fa_expires_at')->nullable();
            $table->timestamp('two_fa_last_request_at')->nullable()->after('two_fa_expires_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_2fa_enabled', 'two_fa_code', 'two_fa_expires_at', 'two_fa_last_request_at']);
        });
    }
};
