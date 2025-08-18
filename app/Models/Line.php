<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $table = 'm_line';
    
    protected $fillable = [
        'line',
        'leader',
    ];
}
