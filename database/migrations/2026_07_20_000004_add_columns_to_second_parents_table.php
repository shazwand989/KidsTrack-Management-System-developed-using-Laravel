<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('second_parents', function (Blueprint $table) {
            if (!Schema::hasColumn('second_parents', 'user_id')) {
                $table->bigInteger('user_id')->unsigned()->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('second_parents', 'photo')) {
                $table->string('photo')->nullable()->after('address');
            }
            if (!Schema::hasColumn('second_parents', 'type')) {
                $table->enum('type', ['main','second','guardian'])->default('second')->after('photo');
            }
            if (!Schema::hasColumn('second_parents', 'verified')) {
                $table->boolean('verified')->default(false)->after('type');
            }
            if (!Schema::hasColumn('second_parents', 'emergency')) {
                $table->boolean('emergency')->default(false)->after('verified');
            }
        });
    }
    public function down(): void
    {
        Schema::table('second_parents', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'photo', 'type', 'verified', 'emergency']);
        });
    }
};
