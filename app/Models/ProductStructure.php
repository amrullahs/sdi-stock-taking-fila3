<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStructure extends Model
{
    protected $table = 'index_product_structure';

    // Use auto-incrementing ID
    public $incrementing = true;

    // Specify that the primary key is an integer
    protected $keyType = 'int';

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
        return $this->hasMany(ProductStructureDetail::class, 'parent_item', 'unique_id');
    }
}
