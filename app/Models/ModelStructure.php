<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
