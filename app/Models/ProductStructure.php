<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStructure extends Model
{
    protected $table = 'index_product_structure';
    
    // Specify that ID is not auto-incrementing since we're using ObjectId
    public $incrementing = false;
    
    // Specify that the primary key is a string (ObjectId)
    protected $keyType = 'string';
    
    protected $fillable = [
        'item_number',
        'category',
        'model',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the product structure details for this product.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProductStructureDetail::class, 'parent_item', 'id');
    }
}
