<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop FK columns from children
        if (Schema::hasColumn('children', 'parent_id')) {
            DB::statement('ALTER TABLE children DROP COLUMN parent_id');
        }
        if (Schema::hasColumn('children', 'second_parent_id')) {
            DB::statement('ALTER TABLE children DROP COLUMN second_parent_id');
        }
        if (Schema::hasColumn('children', 'guardian_id')) {
            DB::statement('ALTER TABLE children DROP COLUMN guardian_id');
        }

        // Drop parent_id from attendance
        if (Schema::hasColumn('attendance', 'parent_id')) {
            DB::statement('ALTER TABLE attendance DROP COLUMN parent_id');
        }

        // Add missing user columns
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('parent1')->after('password');
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->string('age')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'verified')) {
                $table->boolean('verified')->default(false)->after('photo');
            }
        });

        // Add user_id to attendance
        if (!Schema::hasColumn('attendance', 'user_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('child_id')->constrained('users')->nullOnDelete();
            });
        }

        // Create guardianships pivot table
        if (!Schema::hasTable('guardianships')) {
            Schema::create('guardianships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
                $table->enum('relationship', ['main_parent', 'second_parent', 'guardian']);
                $table->boolean('is_emergency_contact')->default(false);
                $table->timestamps();
                $table->unique(['user_id', 'child_id', 'relationship']);
            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('guardianships');

        Schema::table('children', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id');
            $table->foreignId('second_parent_id')->nullable()->after('parent_id');
            $table->foreignId('guardian_id')->nullable()->after('second_parent_id');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('child_id');
        });
    }
};
