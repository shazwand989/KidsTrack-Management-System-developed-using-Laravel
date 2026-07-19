<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('children', function (Blueprint $table) {
            if (!Schema::hasColumn('children', 'qr_code')) {
                $table->string('qr_code')->nullable()->unique()->after('photo');
            }
            if (!Schema::hasColumn('children', 'qr_code_url')) {
                $table->string('qr_code_url')->nullable()->after('qr_code');
            }
        });
    }

    public function down()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'qr_code_url']);
        });
    }
};