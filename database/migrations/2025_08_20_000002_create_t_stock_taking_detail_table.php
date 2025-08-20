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
        Schema::create('t_stock_taking_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_taking_id');
            $table->unsignedBigInteger('model_structure_detail_id');
            $table->integer('storage_count')->nullable();
            $table->integer('wip_count')->nullable();
            $table->integer('ng_count')->nullable();
            $table->integer('total_count')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('stock_taking_id')->references('id')->on('t_stock_taking')->onDelete('cascade');
            $table->foreign('model_structure_detail_id')->references('id')->on('m_model_structure_detail')->onDelete('cascade');
            
            // Indexes
            $table->index(['stock_taking_id']);
            $table->index(['model_structure_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_taking_detail');
    }
};