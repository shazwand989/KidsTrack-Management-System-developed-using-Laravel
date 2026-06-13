<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('age_group');
                $table->integer('min_age');
                $table->integer('max_age');
                $table->integer('capacity')->default(20);
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->time('start_time')->default('08:00:00');
                $table->time('end_time')->default('17:00:00');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('description')->nullable();
                $table->string('color')->default('#FF6B6B');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};