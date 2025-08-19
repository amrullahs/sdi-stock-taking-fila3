<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTaking extends Model
{
    protected $table = 't_stock_taking';
    
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
}