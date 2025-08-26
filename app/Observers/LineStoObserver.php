<?php

namespace App\Observers;

use App\Models\LineSto;
use App\Models\LineStoDetail;
use App\Models\LineModelDetail;

class LineStoObserver
{
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
                'total_count' => 0,
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
