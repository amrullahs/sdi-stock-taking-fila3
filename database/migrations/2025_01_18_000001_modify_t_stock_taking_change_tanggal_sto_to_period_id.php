<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_stock_taking', function (Blueprint $table) {
            // Drop existing index on tanggal_sto
            $table->dropIndex(['tanggal_sto']);
            
            // Drop the tanggal_sto column
            $table->dropColumn('tanggal_sto');
            
            // Add period_id column
            $table->unsignedBigInteger('period_id')->after('id');
            
            // Add foreign key constraint
            $table->foreign('period_id')->references('id')->on('t_period_sto')->onDelete('cascade');
            
            // Add unique constraint for period_id and model_structure_id combination
            $table->unique(['period_id', 'model_structure_id'], 'unique_period_model_structure');
            
            // Add index for period_id
            $table->index(['period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_stock_taking', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique('unique_period_model_structure');
            
            // Drop foreign key constraint
            $table->dropForeign(['period_id']);
            
            // Drop index
            $table->dropIndex(['period_id']);
            
            // Drop period_id column
            $table->dropColumn('period_id');
            
            // Add back tanggal_sto column
            $table->date('tanggal_sto')->after('id');
            
            // Add back index on tanggal_sto
            $table->index(['tanggal_sto']);
        });
    }
};