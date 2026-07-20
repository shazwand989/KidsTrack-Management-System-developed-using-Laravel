<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            if (!Schema::hasColumn('classrooms', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('classrooms', 'age_group')) {
                $table->string('age_group')->nullable()->after('code');
            }
            if (!Schema::hasColumn('classrooms', 'min_age')) {
                $table->integer('min_age')->default(0)->after('age_group');
            }
            if (!Schema::hasColumn('classrooms', 'max_age')) {
                $table->integer('max_age')->default(0)->after('min_age');
            }
            if (!Schema::hasColumn('classrooms', 'teacher_id')) {
                $table->bigInteger('teacher_id')->unsigned()->nullable()->after('capacity');
            }
            if (!Schema::hasColumn('classrooms', 'start_time')) {
                $table->time('start_time')->default('08:00:00')->after('teacher_id');
            }
            if (!Schema::hasColumn('classrooms', 'end_time')) {
                $table->time('end_time')->default('17:00:00')->after('start_time');
            }
            if (!Schema::hasColumn('classrooms', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('end_time');
            }
            if (!Schema::hasColumn('classrooms', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
            if (!Schema::hasColumn('classrooms', 'color')) {
                $table->string('color')->default('#FF6B6B')->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn(['code', 'age_group', 'min_age', 'max_age', 'teacher_id', 'start_time', 'end_time', 'status', 'description', 'color']);
        });
    }
};
