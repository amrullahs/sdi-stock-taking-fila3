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
            $table->string('model')->nullable()->after('category');
            $table->index(['model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('index_product_structure', function (Blueprint $table) {
            $table->dropIndex(['model']);
            $table->dropColumn('model');
        });
    }
};
