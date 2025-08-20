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
        Schema::table('t_stock_on_hand', function (Blueprint $table) {
            $table->index('item_number', 'idx_t_stock_on_hand_item_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_stock_on_hand', function (Blueprint $table) {
            $table->dropIndex('idx_t_stock_on_hand_item_number');
        });
    }
};
