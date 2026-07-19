<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulation_clock', function (Blueprint $table) {
            $table->time('morning_checkin')->nullable()->after('simulation_time');
            $table->time('evening_checkout')->nullable()->after('morning_checkin');
        });
    }

    public function down(): void
    {
        Schema::table('simulation_clock', function (Blueprint $table) {
            $table->dropColumn([
                'morning_checkin',
                'evening_checkout',
            ]);
        });
    }
};