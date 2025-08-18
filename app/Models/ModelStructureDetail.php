<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelStructureDetail extends Model
{
    protected $table = 'm_model_structure_detail';
    
    protected $fillable = [
        'model',
        'qad',
        'desc1',
        'desc2',
        'supplier',
        'suplier_code',
        'standard_packing',
        'storage',
    ];
    
    protected $casts = [
        'standard_packing' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the model structure that owns this detail.
     */
    public function modelStructure(): BelongsTo
    {
        return $this->belongsTo(ModelStructure::class, 'model', 'model');
    }
}
