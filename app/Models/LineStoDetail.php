<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineStoDetail extends Model
{
    protected $table = 't_line_sto_detail';

    protected $fillable = [
        'period_id',
        'line_sto_id',
        'line_id',
        'line_model_detail_id',
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

    // Relationships
    public function periodSto(): BelongsTo
    {
        return $this->belongsTo(PeriodSto::class, 'period_id');
    }

    public function lineSto(): BelongsTo
    {
        return $this->belongsTo(LineSto::class, 'line_sto_id');
    }

    public function lineModelDetail(): BelongsTo
    {
        return $this->belongsTo(LineModelDetail::class, 'line_model_detail_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'line_id');
    }

    /**
     * Get total on hand from stock on hand table
     */
    public function getTotalOnHandAttribute()
    {
        if (!$this->lineModelDetail || !$this->lineSto) {
            return 0;
        }

        $stockOnHand = StockOnHand::where('period_sto_id', $this->lineSto->period_id)
            ->where('item_number', $this->lineModelDetail->qad_number)
            ->first();

        return $stockOnHand ? $stockOnHand->total_on_hand : 0;
    }
    // Accessor untuk total count otomatis
    public function getTotalCountAttribute($value)
    {
        $storage = $this->storage_count ?? 0;
        $wip = $this->wip_count ?? 0;
        $ng = $this->ng_count ?? 0;
        
        // Jika semua nilai null, return null
        if (is_null($this->storage_count) && is_null($this->wip_count) && is_null($this->ng_count)) {
            return null;
        }
        
        return $storage + $wip + $ng;
    }
}
