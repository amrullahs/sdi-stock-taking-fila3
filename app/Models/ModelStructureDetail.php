<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelStructureDetail extends Model
{
    protected $table = 'm_model_structure_detail';
    
    protected $fillable = [
        'model_structure_id',
        'model',
        'qad',
        'desc1',
        'desc2',
        'supplier',
        'suplier_code',
        'standard_packing',
        'image',
        'storage',
        'wip_id',
    ];
    
    protected $casts = [
        'model_structure_id' => 'integer',
        'standard_packing' => 'integer',
        'wip_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the model structure that owns this detail.
     */
    public function modelStructure(): BelongsTo
    {
        return $this->belongsTo(ModelStructure::class, 'model_structure_id');
    }
    
    /**
     * Get the model structure by model name (legacy relationship).
     */
    public function modelStructureByModel(): BelongsTo
    {
        return $this->belongsTo(ModelStructure::class, 'model', 'model');
    }
}
