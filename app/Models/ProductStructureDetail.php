<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStructureDetail extends Model
{
    protected $table = 'index_product_structure_detail';
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'component_item',
        'end_effective',
        'operation',
        'parent_item',
        'quantity_per',
        'remaks',
        'scrap',
        'start_effective',
        'unit_of_measure',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'quantity_per' => 'double',
        'operation' => 'integer',
        'scrap' => 'integer',
    ];
    
    /**
     * Get the parent product structure that owns this detail.
     */
    public function parentStructure(): BelongsTo
    {
        return $this->belongsTo(ProductStructure::class, 'parent_item', 'id');
    }
}