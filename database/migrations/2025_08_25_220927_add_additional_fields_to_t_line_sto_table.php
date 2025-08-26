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
        Schema::table('t_line_sto', function (Blueprint $table) {
            $table->date('tanggal_sto')->nullable()->after('period_id');
            $table->datetime('sto_start_at')->nullable()->after('tanggal_sto');
            $table->datetime('sto_submit_at')->nullable()->after('sto_start_at');
            $table->datetime('sto_update_at')->nullable()->after('sto_submit_at');
            $table->enum('status', ['open', 'onprogress', 'close'])->default('open')->after('sto_update_at');
            $table->integer('progress')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_sto',
                'sto_start_at',
                'sto_submit_at',
                'sto_update_at',
                'status',
                'progress'
            ]);
        });
    }
};
