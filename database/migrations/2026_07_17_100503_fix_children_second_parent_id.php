<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Drop foreign key sedia ada (jika ada)
        Schema::table('children', function (Blueprint $table) {
            try {
                $table->dropForeign(['second_parent_id']);
            } catch (\Exception $e) {
                // Foreign key takde, skip
            }
        });

        // Step 2: Fix column - rujuk kepada parents table
        Schema::table('children', function (Blueprint $table) {
            $table->unsignedBigInteger('second_parent_id')->nullable()->change();
        });

        // Step 3: Add foreign key ke parents table
        Schema::table('children', function (Blueprint $table) {
            $table->foreign('second_parent_id')
                  ->references('id')
                  ->on('parents')
                  ->onDelete('set null');
        });

        // Step 4: Fix data sedia ada - tukar second_parent_id dari second_parents.id ke parents.id
        DB::statement("
            UPDATE children c
            JOIN second_parents sp ON c.second_parent_id = sp.id
            SET c.second_parent_id = sp.parent_id
            WHERE c.second_parent_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['second_parent_id']);
        });
    }
};