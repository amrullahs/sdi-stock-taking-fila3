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
        // First, update existing records with line_id from t_line_sto if line_id is null
        DB::statement('
            UPDATE t_line_sto_detail lsd 
            JOIN t_line_sto ls ON lsd.line_sto_id = ls.id 
            SET lsd.line_id = ls.line_id
            WHERE lsd.line_id IS NULL OR lsd.line_id = 0
        ');
        
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('line_id')->references('id')->on('m_line')->onDelete('cascade');
            
            // Add index for better performance
            $table->index('line_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            $table->dropForeign(['line_id']);
            $table->dropIndex(['line_id']);
        });
    }
};