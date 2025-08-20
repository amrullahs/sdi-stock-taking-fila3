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
        Schema::create('m_model_structure_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_structure_id');
            $table->string('model', 255);
            $table->string('qad', 255);
            $table->string('desc1', 255);
            $table->string('desc2', 255);
            $table->string('supplier', 255);
            $table->string('suplier_code', 255);
            $table->integer('standard_packing');
            $table->string('storage', 255);
            $table->string('wip_id', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
            
            // Foreign key constraints
            $table->foreign('model_structure_id')->references('id')->on('m_model_structure')->onDelete('cascade');
            // Removed foreign key on 'model' column due to missing index in referenced table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_model_structure_detail');
    }
};
