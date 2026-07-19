<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('day_name'); // Monday, Tuesday, etc.
            $table->time('morning_start')->default('07:00:00');
            $table->time('morning_end')->default('07:30:00');
            $table->time('afternoon_start')->default('12:00:00');
            $table->time('afternoon_end')->default('12:30:00');
            $table->time('evening_start')->default('17:00:00');
            $table->time('evening_end')->default('17:30:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint - one record per day
            $table->unique('day_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('timer_settings');
    }
};