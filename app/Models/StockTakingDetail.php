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