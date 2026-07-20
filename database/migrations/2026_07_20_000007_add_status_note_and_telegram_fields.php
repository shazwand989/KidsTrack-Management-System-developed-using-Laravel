<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attendance', 'status_note')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->string('status_note')->nullable()->after('status');
            });
        }
        if (!Schema::hasColumn('parents', 'telegram_id')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->string('telegram_id')->nullable()->after('emergency');
            });
        }
        if (!Schema::hasColumn('parents', 'telegram_notification')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->boolean('telegram_notification')->default(false)->after('telegram_id');
            });
        }
    }
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('status_note');
        });
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['telegram_id', 'telegram_notification']);
        });
    }
};
