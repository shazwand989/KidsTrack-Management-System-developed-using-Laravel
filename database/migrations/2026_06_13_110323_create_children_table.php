<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            
            // Child Basic Information
            $table->string('name');
            $table->integer('age');
            $table->string('ic_number')->unique();
            $table->date('dob')->nullable();
            $table->text('address');
            $table->string('photo')->nullable();
            
            // Nursery Type
            $table->enum('nursery_type', ['full_day', 'half_day', 'afternoon', 'flexible', 'weekend', 'trial'])->default('full_day');
            
            // Parent/Guardian Relationships
            $table->foreignId('parent_id')->constrained('parents')->onDelete('cascade');
            $table->foreignId('second_parent_id')->nullable()->constrained('parents')->onDelete('set null');
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->onDelete('set null');
            
            // Additional Information
            $table->text('medical_notes')->nullable();
            $table->text('dietary')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->date('enrollment_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};