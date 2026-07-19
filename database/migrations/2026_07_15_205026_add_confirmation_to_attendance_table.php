<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->boolean('confirmed')->default(false)->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed');
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['confirmed', 'confirmed_at']);
        });
    }
};