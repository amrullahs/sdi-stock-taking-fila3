<?php

namespace App\Observers;

use App\Models\LineStoDetail;
use App\Models\LineSto;

class LineStoDetailObserver
{
    /**
     * Handle the LineStoDetail "creating" event.
     */
    public function creating(LineStoDetail $lineStoDetail): void
    {
        // Auto-fill line_id from line_sto relationship
        if ($lineStoDetail->line_sto_id && !$lineStoDetail->line_id) {
            $lineSto = LineSto::find($lineStoDetail->line_sto_id);
            if ($lineSto) {
                $lineStoDetail->line_id = $lineSto->line_id;
            }
        }
    }

    /**
     * Handle the LineStoDetail "updating" event.
     */
    public function updating(LineStoDetail $lineStoDetail): void
    {
        // Auto-update line_id if line_sto_id changes
        if ($lineStoDetail->isDirty('line_sto_id')) {
            $lineSto = LineSto::find($lineStoDetail->line_sto_id);
            if ($lineSto) {
                $lineStoDetail->line_id = $lineSto->line_id;
            }
        }
    }

    /**
     * Handle the LineStoDetail "updated" event.
     */
    public function updated(LineStoDetail $lineStoDetail): void
    {
        $this->updateLineStoStatus($lineStoDetail);
    }

    /**
     * Handle the LineStoDetail "created" event.
     */
    public function created(LineStoDetail $lineStoDetail): void
    {
        $this->updateLineStoStatus($lineStoDetail);
    }

    /**
     * Handle the LineStoDetail "deleted" event.
     */
    public function deleted(LineStoDetail $lineStoDetail): void
    {
        $this->updateLineStoProgress($lineStoDetail);
    }

    /**
     * Update LineSto status and timestamps when count fields change
     */
    private function updateLineStoStatus(LineStoDetail $lineStoDetail): void
    {
        // Check if any count fields have values (not null)
        $hasCountValues = !is_null($lineStoDetail->storage_count) || 
                         !is_null($lineStoDetail->wip_count) || 
                         !is_null($lineStoDetail->ng_count);

        if ($hasCountValues && $lineStoDetail->line_sto_id) {
            $lineSto = LineSto::find($lineStoDetail->line_sto_id);
            
            if ($lineSto) {
                $updateData = [];
                
                // Set status to onprogress if not already
                if ($lineSto->status !== 'onprogress') {
                    $updateData['status'] = 'onprogress';
                }
                
                // Set sto_start_at if not already set
                if (is_null($lineSto->sto_start_at)) {
                    $updateData['sto_start_at'] = now();
                }
                
                // Always update sto_update_at
                $updateData['sto_update_at'] = now();
                
                // Calculate and update progress
                $updateData['progress'] = $lineSto->progress;
                
                if (!empty($updateData)) {
                    $lineSto->update($updateData);
                }
            }
        }
    }

    /**
     * Update LineSto progress when LineStoDetail is deleted
     */
    private function updateLineStoProgress(LineStoDetail $lineStoDetail): void
    {
        if ($lineStoDetail->line_sto_id) {
            $lineSto = LineSto::find($lineStoDetail->line_sto_id);
            
            if ($lineSto) {
                $lineSto->update([
                    'progress' => $lineSto->progress,
                    'sto_update_at' => now()
                ]);
            }
        }
    }
}