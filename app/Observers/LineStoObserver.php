<?php

namespace App\Observers;

use App\Models\LineSto;
use App\Models\LineStoDetail;
use App\Models\LineModelDetail;
use Illuminate\Support\Facades\Auth;

class LineStoObserver
{
    /**
     * Handle the LineSto "creating" event.
     */
    public function creating(LineSto $lineSto): void
    {
        // Auto-set created_by dengan nama user yang sedang login
        if (Auth::check() && empty($lineSto->created_by)) {
            $lineSto->created_by = Auth::user()->name;
        }
    }

    /**
     * Handle the LineSto "created" event.
     */
    public function created(LineSto $lineSto): void
    {
        // Auto-create LineStoDetail untuk setiap LineModelDetail yang ada di line ini
        $lineModelDetails = LineModelDetail::where('line_id', $lineSto->line_id)->get();
        
        foreach ($lineModelDetails as $lineModelDetail) {
            LineStoDetail::create([
                'period_id' => $lineSto->period_id,
                'line_sto_id' => $lineSto->id,
                'line_model_detail_id' => $lineModelDetail->id,
                // Semua count fields akan null secara default
            ]);
        }
    }

    /**
     * Handle the LineSto "updated" event.
     */
    public function updated(LineSto $lineSto): void
    {
        //
    }

    /**
     * Handle the LineSto "deleted" event.
     */
    public function deleted(LineSto $lineSto): void
    {
        //
    }

    /**
     * Handle the LineSto "restored" event.
     */
    public function restored(LineSto $lineSto): void
    {
        //
    }

    /**
     * Handle the LineSto "force deleted" event.
     */
    public function forceDeleted(LineSto $lineSto): void
    {
        //
    }
}
