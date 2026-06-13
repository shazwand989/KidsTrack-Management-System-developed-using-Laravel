<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
 public function up(): void
 {
 Schema::table('student_timetables', function (Blueprint
$table) {
 // First ensure the columns exist and are the right type
$table->foreign('subject_id')
 ->references('id')
->on('subjects')
->onDelete('set null');
$table->foreign('day_id')
 ->references('id')
->on('days')
->onDelete('set null');
 $table->foreign('hall_id')
 ->references('id')
->on('halls')
->onDelete('set null');
 $table->foreign('user_id')
 ->references('id')
->on('users')
->onDelete('set null');
 $table->foreign('lecturer_group_id')
 ->references('id')
->on('lecturer_groups')
->onDelete('set null');
 });
 }
 public function down(): void
 {
 Schema::table('student_timetables', function (Blueprint
$table) {
 $table->dropForeign(['subject_id']);
 $table->dropForeign(['day_id']);
 $table->dropForeign(['hall_id']);
 $table->dropForeign(['user_id']);
 $table->dropForeign(['lecturer_group_id']);
 });
 }
};