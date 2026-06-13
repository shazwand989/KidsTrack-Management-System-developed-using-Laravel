<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (Schema::hasColumn('children', 'nursery_type')) {
                $table->dropColumn('nursery_type');
            }
            if (!Schema::hasColumn('children', 'classroom_id')) {
                $table->foreignId('classroom_id')->nullable()->after('photo')->constrained('classrooms')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (Schema::hasColumn('children', 'classroom_id')) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn('classroom_id');
            }
            if (!Schema::hasColumn('children', 'nursery_type')) {
                $table->enum('nursery_type', ['full_day', 'half_day', 'afternoon', 'flexible', 'weekend', 'trial'])->default('full_day')->after('photo');
            }
        });
    }
};