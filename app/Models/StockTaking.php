<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTaking extends Model
{
    protected $table = 't_stock_taking';
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (StockTaking $stockTaking) {
            // Delete all related stock taking details
            $stockTaking->stockTakingDetails()->delete();
        });
    }
    
    protected $fillable = [
        'tanggal_sto',
        'sto_start_at',
        'sto_submit_at',
        'sto_update_at',
        'sto_user',
        'model',
        'model_structure_id',
        'status',
        'progress',
    ];
    
    protected $casts = [
        'tanggal_sto' => 'date',
        'sto_start_at' => 'datetime',
        'sto_submit_at' => 'datetime',
        'sto_update_at' => 'datetime',
        'model_structure_id' => 'integer',
        'progress' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the model structure that owns this stock taking.
     */
    public function modelStructure(): BelongsTo
    {
        return $this->belongsTo(ModelStructure::class, 'model_structure_id');
    }

    /**
     * Get the stock taking details for this stock taking.
     */
    public function stockTakingDetails(): HasMany
    {
        return $this->hasMany(StockTakingDetail::class, 'stock_taking_id');
    }
    
    /**
     * Status constants
     */
    const STATUS_OPEN = 'open';
    const STATUS_ON_PROGRESS = 'on_progress';
    const STATUS_CLOSE = 'close';
    
    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_ON_PROGRESS => 'On Progress',
            self::STATUS_CLOSE => 'Close',
        ];
    }

    /**
     * Calculate progress based on updated stock taking details
     * Progress = (number of updated rows / total rows) * 100
     */
    public function calculateProgress(): int
    {
        $totalDetails = $this->stockTakingDetails()->count();
        
        if ($totalDetails === 0) {
            return 0;
        }
        
        $updatedDetails = $this->stockTakingDetails()
            ->where(function ($query) {
                $query->whereNotNull('storage_count')
                      ->orWhereNotNull('wip_count')
                      ->orWhereNotNull('ng_count');
            })
            ->count();
        
        return (int) round(($updatedDetails / $totalDetails) * 100);
    }

    /**
     * Update progress and save
     * Also auto-update status from 'open' to 'on_progress' when progress > 0%
     */
    public function updateProgress(): void
    {
        $this->progress = $this->calculateProgress();
        
        // Auto-update status from 'open' to 'on_progress' when progress > 0%
        if ($this->status === self::STATUS_OPEN && $this->progress > 0) {
            $this->status = self::STATUS_ON_PROGRESS;
        }
        
        $this->save();
    }

    /**
     * Set start time when first data is updated
     */
    public function setStartTimeIfNotSet(): void
    {
        if ($this->sto_start_at === null) {
            $this->sto_start_at = now();
            $this->save();
        }
    }

    /**
     * Update the update time to current timestamp
     */
    public function updateTimestamp(): void
    {
        $this->sto_update_at = now();
        $this->save();
    }

    /**
     * Check if this is the first data update (no previous updates)
     */
    public function isFirstUpdate(): bool
    {
        return $this->stockTakingDetails()
            ->where(function ($query) {
                $query->whereNotNull('storage_count')
                      ->orWhereNotNull('wip_count')
                      ->orWhereNotNull('ng_count');
            })
            ->count() === 0;
    }
}