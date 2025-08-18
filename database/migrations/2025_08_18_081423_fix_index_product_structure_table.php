<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup existing data
        $existingData = DB::table('index_product_structure')->get();
        
        // Drop the table
        Schema::dropIfExists('index_product_structure');
        
        // Recreate the table with correct structure
        Schema::create('index_product_structure', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('item_number')->nullable();
            $table->string('category')->nullable();
            $table->string('model')->nullable();
            $table->timestamps();
        });
        
        // Restore data with new auto-increment IDs
        foreach ($existingData as $record) {
            DB::table('index_product_structure')->insert([
                'item_number' => $record->item_number,
                'category' => $record->category,
                'model' => $record->model,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed
        // as it involves data transformation
    }
};
