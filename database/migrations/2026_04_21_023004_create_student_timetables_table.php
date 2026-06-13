<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
 {
 Schema::create('student_timetables', function (Blueprint $table)
 {
 $table->id();
 $table->unsignedBigInteger('user_id')->nullable();
 $table->unsignedBigInteger('subject_id')->nullable();
 $table->unsignedBigInteger('day_id')->nullable();
 $table->unsignedBigInteger('hall_id')->nullable();
 $table->unsignedBigInteger('lecturer_group_id')->nullable();
 $table->string('time_from')->nullable();
 $table->string('time_to')->nullable();
 $table->timestamps();
 });
 }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_timetables');
    }
};
