<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
