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
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            // Change count columns to nullable and remove default values
            $table->integer('storage_count')->nullable()->default(null)->change();
            $table->integer('wip_count')->nullable()->default(null)->change();
            $table->integer('ng_count')->nullable()->default(null)->change();
            $table->integer('total_count')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            // Revert back to default 0
            $table->integer('storage_count')->default(0)->change();
            $table->integer('wip_count')->default(0)->change();
            $table->integer('ng_count')->default(0)->change();
            $table->integer('total_count')->default(0)->change();
        });
    }
};
