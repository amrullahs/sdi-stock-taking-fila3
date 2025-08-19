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
    
    protected $appends = [
        'part_number',
        'part_name',
        'image_url'
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
    
    /**
     * Accessor untuk Part Number (mengambil dari kolom qad)
     */
    public function getPartNumberAttribute()
    {
        return $this->qad;
    }
    
    /**
     * Accessor untuk Part Name (penggabungan desc1 dan desc2)
     */
    public function getPartNameAttribute()
    {
        $desc1 = $this->desc1 ?? '';
        $desc2 = $this->desc2 ?? '';
        
        if (empty($desc1) && empty($desc2)) {
            return '';
        }
        
        if (empty($desc2)) {
            return $desc1;
        }
        
        if (empty($desc1)) {
            return $desc2;
        }
        
        return $desc1 . ' - ' . $desc2;
    }
    
    /**
     * Accessor untuk Image (fallback ke nama {qad} jika null)
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return $this->image;
        }
        
        // Fallback ke storage/app/public/img/{qad} dengan berbagai format
        if ($this->qad) {
            $formats = [
                ['path' => "/storage/img/{$this->qad}.svg", 'file' => "storage/img/{$this->qad}.svg"],
                ['path' => "/storage/img/{$this->qad}.png", 'file' => "storage/img/{$this->qad}.png"],
                ['path' => "/storage/img/{$this->qad}.jpg", 'file' => "storage/img/{$this->qad}.jpg"]
            ];
            
            foreach ($formats as $format) {
                if (file_exists(public_path($format['file']))) {
                    return $format['path'];
                }
            }
        }
        
        // Return default image if no specific image found
        return '/images/no-image.svg';
    }
}
