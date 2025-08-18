<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelStructure extends Model
{
    protected $table = 'm_model_structure';

    protected $fillable = [
        'model',
        'line',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the model structure details for this model.
     */
    public function modelStructureDetails(): HasMany
    {
        return $this->hasMany(ModelStructureDetail::class, 'model', 'model');
    }
}
