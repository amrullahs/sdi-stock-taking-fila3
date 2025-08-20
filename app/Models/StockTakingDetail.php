<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTakingDetail extends Model
{
    use HasFactory;

    protected $table = 't_stock_taking_detail';

    protected $fillable = [
        'stock_taking_id',
        'model_structure_detail_id',
        'storage_count',
        'wip_count',
        'ng_count',
        'total_count',
    ];

    protected $casts = [
        'storage_count' => 'integer',
        'wip_count' => 'integer',
        'ng_count' => 'integer',
        'total_count' => 'integer',
    ];

    /**
     * Check if this record has been updated (any count field is not null)
     */
    public function isUpdated(): bool
    {
        return $this->storage_count !== null || 
               $this->wip_count !== null || 
               $this->ng_count !== null;
    }

    /**
     * Get the actual count value, treating null as 0 for calculations
     */
    public function getActualStorageCount(): int
    {
        return $this->storage_count ?? 0;
    }

    public function getActualWipCount(): int
    {
        return $this->wip_count ?? 0;
    }

    public function getActualNgCount(): int
    {
        return $this->ng_count ?? 0;
    }

    /**
     * Relasi ke StockTaking
     */
    public function stockTaking(): BelongsTo
    {
        return $this->belongsTo(StockTaking::class, 'stock_taking_id');
    }

    /**
     * Relasi ke ModelStructureDetail
     */
    public function modelStructureDetail(): BelongsTo
    {
        return $this->belongsTo(ModelStructureDetail::class, 'model_structure_detail_id');
    }

    /**
     * Get total on hand from stock on hand table
     */
    public function getTotalOnHandAttribute()
    {
        if (!$this->modelStructureDetail || !$this->stockTaking) {
            return 0;
        }

        $stockOnHand = StockOnHand::where('period_sto_id', $this->stockTaking->period_id)
            ->where('item_number', $this->modelStructureDetail->qad)
            ->first();

        return $stockOnHand ? $stockOnHand->total_on_hand : 0;
    }

    /**
     * Accessor untuk menghitung total count otomatis
     */
    public function getTotalCountAttribute($value)
    {
        if ($value === null) {
            return ($this->storage_count ?? 0) + ($this->wip_count ?? 0) + ($this->ng_count ?? 0);
        }
        return $value;
    }
}