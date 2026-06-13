<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MAIN PARENT
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // SECOND PARENT
        Schema::create('second_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id');
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // GUARDIAN
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id');
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->timestamps();
        });

        // CHILDREN
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('second_parents');
        Schema::dropIfExists('parents');
    }
};