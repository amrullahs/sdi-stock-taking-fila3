<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOnHand extends Model
{
    protected $table = 't_stock_on_hand';
    
    protected $fillable = [
        'item_number',
        'desc',
        'location',
        'lot',
        'ref',
        'status',
        'qty_on_hand',
        'confirming',
        'created',
        'total_on_hand',
        'period_sto_id',
        'uploaded'
    ];
    
    protected $casts = [
        'created' => 'date',
        'uploaded' => 'datetime',
        'item_number' => 'integer',
        'lot' => 'integer',
        'qty_on_hand' => 'integer',
        'total_on_hand' => 'integer',
        'period_sto_id' => 'integer'
    ];
    
    public function periodSto(): BelongsTo
    {
        return $this->belongsTo(PeriodSto::class, 'period_sto_id');
    }
}
