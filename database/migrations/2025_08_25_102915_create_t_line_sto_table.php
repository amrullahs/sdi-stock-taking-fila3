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
        Schema::create('t_line_sto', function (Blueprint $table) {
            $table->id();
            $table->date('period_sto');
            $table->unsignedBigInteger('line_id');
            $table->string('created_by', 255);
            $table->string('site', 255);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('line_id')->references('id')->on('m_line')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_line_sto');
    }
};
