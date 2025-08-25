<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineSto extends Model
{
    protected $table = 't_line_sto';
    
    protected $fillable = [
        'period_sto',
        'line_id',
        'created_by',
        'site',
    ];
    
    protected $casts = [
        'period_sto' => 'date',
    ];
    
    /**
     * Get the line that owns the LineSto
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'line_id');
    }
}
