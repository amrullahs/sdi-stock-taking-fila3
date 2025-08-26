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
        Schema::table('m_line_model_detail', function (Blueprint $table) {
            $table->foreign('line_id')->references('id')->on('m_line')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_line_model_detail', function (Blueprint $table) {
            $table->dropForeign(['line_id']);
        });
    }
};
