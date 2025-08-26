<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineSto extends Model
{
    protected $table = 't_line_sto';
    
    protected $fillable = [
        'period_id',
        'line_id',
        'created_by',
        'site',
        'tanggal_sto',
        'sto_start_at',
        'sto_submit_at',
        'sto_update_at',
        'status',
        'progress',
    ];
    
    protected $casts = [
        'tanggal_sto' => 'date',
        'sto_start_at' => 'datetime',
        'sto_submit_at' => 'datetime',
        'sto_update_at' => 'datetime',
        'progress' => 'integer',
    ];
    
    /**
     * Get the line that owns the LineSto
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'line_id');
    }
    
    /**
     * Get the period STO that owns the LineSto
     */
    public function periodSto(): BelongsTo
    {
        return $this->belongsTo(PeriodSto::class, 'period_id');
    }
}
