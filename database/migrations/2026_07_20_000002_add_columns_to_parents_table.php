<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            if (!Schema::hasColumn('parents', 'type')) {
                $table->enum('type', ['main','second','guardian'])->default('main')->after('photo');
            }
            if (!Schema::hasColumn('parents', 'verified')) {
                $table->boolean('verified')->default(false)->after('type');
            }
            if (!Schema::hasColumn('parents', 'emergency')) {
                $table->boolean('emergency')->default(false)->after('verified');
            }
        });
    }
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['type', 'verified', 'emergency']);
        });
    }
};
