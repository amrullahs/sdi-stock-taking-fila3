<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_line_sto', function (Blueprint $table) {
            // Drop the period_sto column
            $table->dropColumn('period_sto');
            
            // Add period_id column
            $table->unsignedBigInteger('period_id')->after('id');
            
            // Add foreign key constraint
            $table->foreign('period_id')->references('id')->on('t_period_sto')->onDelete('cascade');
            
            // Add index for period_id
            $table->index(['period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['period_id']);
            
            // Drop index
            $table->dropIndex(['period_id']);
            
            // Drop period_id column
            $table->dropColumn('period_id');
            
            // Add back period_sto column
            $table->date('period_sto')->after('id');
        });
    }
};
