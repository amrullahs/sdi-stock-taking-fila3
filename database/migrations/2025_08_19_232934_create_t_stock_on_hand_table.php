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
        Schema::create('t_stock_on_hand', function (Blueprint $table) {
            $table->id();
            $table->string('item_number', 255);
            $table->string('desc', 255);
            $table->string('location', 255);
            $table->string('lot', 255);
            $table->string('ref', 255);
            $table->string('status', 255);
            $table->integer('qty_on_hand');
            $table->string('confirming', 255);
            $table->date('created');
            $table->integer('total_on_hand');
            $table->unsignedBigInteger('period_sto_id');
            $table->datetime('uploaded');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('period_sto_id')->references('id')->on('t_period_sto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_on_hand');
    }
};
