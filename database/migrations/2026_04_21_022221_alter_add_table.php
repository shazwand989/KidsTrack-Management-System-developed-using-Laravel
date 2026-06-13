<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
 public function up(): void
 {
 Schema::table('users', function (Blueprint $table) {
 $table->string('age')->nullable()->after('name');
 });
 Schema::table('subjects', function (Blueprint $table) {
 $table->string('lecturer_name')->nullable()->after('subject_name');
 });
 }
 public function down(): void
 {
 Schema::table('users', function (Blueprint $table) {
 $table->dropColumn('age');
 });
 Schema::table('subjects', function (Blueprint $table) {
 $table->dropColumn('lecturer_name');
 });
 }
};