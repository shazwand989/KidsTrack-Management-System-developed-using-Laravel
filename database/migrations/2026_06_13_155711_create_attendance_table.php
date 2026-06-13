<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['checkin', 'checkout', 'absent'])->default('absent');
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->string('drop_off_by')->nullable();
            $table->string('pickup_by')->nullable();
            $table->text('notes')->nullable();
            
            $table->unique(['child_id', 'date']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};