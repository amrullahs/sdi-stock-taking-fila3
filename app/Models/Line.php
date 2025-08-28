<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends Model
{
    protected $table = 'm_line';
    
    protected $fillable = [
        'line',
        'leader',
    ];
    
    /**
     * Get the line model details for this line.
     */
    public function lineModelDetails(): HasMany
    {
        return $this->hasMany(LineModelDetail::class);
    }
    
    /**
     * Get the line STOs for this line.
     */
    public function lineSTOs(): HasMany
    {
        return $this->hasMany(LineSto::class);
    }
}
