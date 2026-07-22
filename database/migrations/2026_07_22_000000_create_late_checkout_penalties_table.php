<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('late_checkout_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->string('scheduled_checkout', 10)->nullable();
            $table->string('actual_checkout', 10)->nullable();
            $table->integer('late_minutes')->default(0);
            $table->integer('grace_period')->default(10);
            $table->decimal('penalty_amount', 8, 2)->default(0);
            $table->string('bill_code')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed, cancelled
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Settings table for penalty config
        Schema::create('penalty_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('penalty_settings')->insert([
            ['key' => 'enabled', 'value' => 'true'],
            ['key' => 'grace_period', 'value' => '10'],
            ['key' => 'penalty_amount', 'value' => '20.00'],
            ['key' => 'toyyibpay_mode', 'value' => 'sandbox'],
            ['key' => 'toyyibpay_category', 'value' => '1h3x8o4a'],
            ['key' => 'toyyibpay_secret', 'value' => 'wibp6oak-4iwy-ocio-l65c-oletr03exv6e'],
            ['key' => 'callback_url', 'value' => rtrim(config('app.url'), '/') . '/api/penalty/callback'],
            ['key' => 'return_url', 'value' => rtrim(config('app.url'), '/') . '/parent/penalties'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('late_checkout_penalties');
        Schema::dropIfExists('penalty_settings');
    }
};
