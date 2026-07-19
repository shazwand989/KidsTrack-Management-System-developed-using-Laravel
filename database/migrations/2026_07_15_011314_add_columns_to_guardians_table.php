<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (!Schema::hasColumn('guardians', 'user_id')) {
                $table->bigInteger('user_id')->unsigned()->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('guardians', 'type')) {
                $table->enum('type', ['main', 'second', 'guardian'])->default('guardian')->after('photo');
            }
            if (!Schema::hasColumn('guardians', 'verified')) {
                $table->boolean('verified')->default(false)->after('type');
            }
            if (!Schema::hasColumn('guardians', 'emergency')) {
                $table->boolean('emergency')->default(false)->after('verified');
            }
        });
    }

    public function down()
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'type', 'verified', 'emergency']);
        });
    }
};