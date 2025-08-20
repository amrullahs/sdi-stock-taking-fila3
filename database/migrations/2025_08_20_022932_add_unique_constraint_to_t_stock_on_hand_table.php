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
        // First, remove duplicate records keeping only the latest one
        DB::statement("
            DELETE t1 FROM t_stock_on_hand t1
            INNER JOIN t_stock_on_hand t2 
            WHERE t1.id < t2.id 
            AND t1.item_number = t2.item_number 
            AND t1.period_sto_id = t2.period_sto_id
        ");
        
        Schema::table('t_stock_on_hand', function (Blueprint $table) {
            // Add unique constraint on item_number and period_sto_id combination
            $table->unique(['item_number', 'period_sto_id'], 'unique_item_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_stock_on_hand', function (Blueprint $table) {
            $table->dropUnique('unique_item_period');
        });
    }
};
