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
        // First, check and fix any problematic data
        $problematicRecords = DB::table('t_line_sto')
            ->whereRaw("created_by NOT REGEXP '^[0-9]+$'")
            ->get();
            
        foreach ($problematicRecords as $record) {
            $user = DB::table('users')
                ->where('name', $record->created_by)
                ->orWhere('email', $record->created_by)
                ->first();
                
            if ($user) {
                DB::table('t_line_sto')
                    ->where('id', $record->id)
                    ->update(['created_by' => $user->id]);
            } else {
                // If no user found, set to first admin user or create a default
                $adminUser = DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', 'super_admin')
                    ->select('users.id')
                    ->first();
                    
                if ($adminUser) {
                    DB::table('t_line_sto')
                        ->where('id', $record->id)
                        ->update(['created_by' => $adminUser->id]);
                } else {
                    // Fallback to user ID 1 if no admin found
                    DB::table('t_line_sto')
                        ->where('id', $record->id)
                        ->update(['created_by' => 1]);
                }
            }
        }
        
        // Change the column type to unsignedBigInteger
        Schema::table('t_line_sto', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->change();
            
            // Add foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_line_sto', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['created_by']);
            
            // Change back to string
            $table->string('created_by', 255)->change();
        });
    }
};
