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
        Schema::create('t_line_sto_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('line_sto_id');
            $table->unsignedBigInteger('line_id')->nullable();
            $table->unsignedBigInteger('line_model_detail_id');
            $table->integer('storage_count')->default(0);
            $table->integer('wip_count')->default(0);
            $table->integer('ng_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('period_id')->references('id')->on('t_period_sto')->onDelete('cascade');
            $table->foreign('line_sto_id')->references('id')->on('t_line_sto')->onDelete('cascade');
            $table->foreign('line_model_detail_id')->references('id')->on('m_line_model_detail')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['line_sto_id', 'line_model_detail_id']);
            $table->index('period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_line_sto_detail');
    }
};
