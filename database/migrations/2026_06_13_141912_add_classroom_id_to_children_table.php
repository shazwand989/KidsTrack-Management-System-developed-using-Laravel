<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (!Schema::hasColumn('children', 'classroom_id')) {
                $table->unsignedBigInteger('classroom_id')->nullable()->after('guardian_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            if (Schema::hasColumn('children', 'classroom_id')) {
                $table->dropColumn('classroom_id');
            }
        });
    }
};