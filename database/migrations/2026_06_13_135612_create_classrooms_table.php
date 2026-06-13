<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('classrooms', function (Blueprint $table) {
        $table->id();

        // Basic Info
        $table->string('name');
        $table->string('code');
        $table->string('age_group');
        $table->integer('capacity');
        $table->integer('min_age');
        $table->integer('max_age');

        // Teacher
        $table->foreignId('teacher_id')->nullable();

        // Schedule
        $table->time('start_time');
        $table->time('end_time');

        // Extra
        $table->string('color')->default('#FF6B6B');
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->text('description')->nullable();

        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};