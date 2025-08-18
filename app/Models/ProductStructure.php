<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStructure extends Model
{
    protected $table = 'index_product_structure';
    
    protected $fillable = [
        'item_number',
        'category',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
