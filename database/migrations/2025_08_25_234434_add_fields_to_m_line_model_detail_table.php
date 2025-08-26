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
            $table->integer('line_id')->after('id');
            $table->integer('model_id')->after('line_id');
            $table->string('qad_number')->after('model_id');
            $table->string('part_name')->after('qad_number');
            $table->string('part_number')->after('part_name');
            $table->string('desc')->nullable()->after('part_number');
            $table->string('supplier')->nullable()->after('desc');
            $table->string('suplier_code')->nullable()->after('supplier');
            $table->integer('std_packing')->nullable()->after('suplier_code');
            $table->string('storage')->nullable()->after('std_packing');
            $table->string('wip_id')->nullable()->after('storage');
            $table->string('image')->nullable()->after('wip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_line_model_detail', function (Blueprint $table) {
            $table->dropColumn([
                'line_id',
                'model_id',
                'qad_number',
                'part_name',
                'part_number',
                'desc',
                'supplier',
                'suplier_code',
                'std_packing',
                'storage',
                'wip_id',
                'image'
            ]);
        });
    }
};
