<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'parent_id')) {
                $table->bigInteger('parent_id')->unsigned()->nullable()->after('child_id');
            }
            if (!Schema::hasColumn('attendance', 'late_reason')) {
                $table->text('late_reason')->nullable()->after('confirmed_at');
            }
            if (!Schema::hasColumn('attendance', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('pickup_by');
            }
        });
        // Fix enum to include all statuses
        DB::statement("ALTER TABLE attendance MODIFY status ENUM('checkin','checkout','absent','present','late','late_checkout') NOT NULL DEFAULT 'absent'");
    }
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'late_reason', 'is_verified']);
        });
    }
};
