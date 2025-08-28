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
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            $table->text('remark')->nullable()->after('total_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto_detail', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
};
