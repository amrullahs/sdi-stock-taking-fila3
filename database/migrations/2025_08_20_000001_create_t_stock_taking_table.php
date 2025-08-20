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
        Schema::create('t_stock_taking', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_sto');
            $table->datetime('sto_start_at')->nullable();
            $table->datetime('sto_submit_at')->nullable();
            $table->datetime('sto_update_at')->nullable();
            $table->string('sto_user', 255);
            $table->string('model', 255);
            $table->unsignedBigInteger('model_structure_id');
            $table->enum('status', ['open', 'on_progress', 'close'])->default('open');
            $table->integer('progress')->default(0);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('model_structure_id')->references('id')->on('m_model_structure')->onDelete('cascade');
            
            // Indexes
            $table->index(['status']);
            $table->index(['tanggal_sto']);
            $table->index(['model_structure_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_taking');
    }
};