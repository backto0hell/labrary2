<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePermissionsTableAddColumns extends Migration
{
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'name')) {
                $table->string('name')->unique()->after('id');
            }
            if (!Schema::hasColumn('permissions', 'code')) {
                $table->string('code')->unique()->after('name');
            }
            if (!Schema::hasColumn('permissions', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            if (!Schema::hasColumn('permissions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('description');
            }
            if (!Schema::hasColumn('permissions', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('permissions', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['name', 'code', 'description', 'created_by', 'deleted_by', 'deleted_at']);
        });
    }
}
