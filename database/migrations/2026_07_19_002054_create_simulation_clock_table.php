<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_clock', function (Blueprint $table) {
            $table->id();
            $table->dateTime('simulation_time');
            $table->time('morning_start')->default('07:00:00');
            $table->time('morning_end')->default('07:30:00');
            $table->time('evening_start')->default('17:00:00');
            $table->time('evening_end')->default('17:30:00');
            $table->boolean('use_simulation')->default(false);
            $table->timestamps();
        });

        DB::table('simulation_clock')->insert([
            'simulation_time' => now(),
            'morning_start' => '07:00:00',
            'morning_end' => '07:30:00',
            'evening_start' => '17:00:00',
            'evening_end' => '17:30:00',
            'use_simulation' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_clock');
    }
};