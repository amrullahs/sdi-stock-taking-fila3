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
        Schema::create('model_structure', function (Blueprint $table) {
            $table->id();
            $table->string('model')->unique();
            $table->string('line')->nullable();
            $table->timestamps();
            
            $table->index(['model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_structure');
    }
};
