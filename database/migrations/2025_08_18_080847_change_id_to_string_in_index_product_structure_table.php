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
        Schema::table('index_product_structure', function (Blueprint $table) {
            // Drop auto-increment and change to string
            $table->string('id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('index_product_structure', function (Blueprint $table) {
            // Revert back to auto-increment integer
            $table->id()->change();
        });
    }
};
