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
            // Make all columns nullable except item_number
            $table->string('desc', 255)->nullable()->change();
            $table->string('location', 255)->nullable()->change();
            $table->string('lot', 255)->nullable()->change();
            $table->string('ref', 255)->nullable()->change();
            $table->string('status', 255)->nullable()->change();
            $table->integer('qty_on_hand')->nullable()->change();
            $table->string('confirming', 255)->nullable()->change();
            $table->date('created')->nullable()->change();
            $table->integer('total_on_hand')->nullable()->change();
            $table->datetime('uploaded')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_stock_on_hand', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('desc', 255)->nullable(false)->change();
            $table->string('location', 255)->nullable(false)->change();
            $table->string('lot', 255)->nullable(false)->change();
            $table->string('ref', 255)->nullable(false)->change();
            $table->string('status', 255)->nullable(false)->change();
            $table->integer('qty_on_hand')->nullable(false)->change();
            $table->string('confirming', 255)->nullable(false)->change();
            $table->date('created')->nullable(false)->change();
            $table->integer('total_on_hand')->nullable(false)->change();
            $table->datetime('uploaded')->nullable(false)->change();
        });
    }
};
