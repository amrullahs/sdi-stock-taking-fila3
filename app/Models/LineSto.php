<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LineSto extends Model
{
    use SoftDeletes, LogsActivity;
    
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

    /**
     * Get the user who created this LineSto
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the line STO details for the LineSto
     */
    public function lineStoDetails(): HasMany
    {
        return $this->hasMany(LineStoDetail::class, 'line_sto_id');
    }

    /**
     * Get the progress percentage based on filled count fields
     * Formula: (count of non-null storage_count + wip_count + ng_count) / (total rows * 3) * 100
     */
    public function getProgressAttribute(): int
    {
        $totalRows = $this->lineStoDetails()->count();
        
        if ($totalRows === 0) {
            return 0;
        }
        
        // Count non-null values for each count field
        $filledStorageCount = $this->lineStoDetails()->whereNotNull('storage_count')->count();
        $filledWipCount = $this->lineStoDetails()->whereNotNull('wip_count')->count();
        $filledNgCount = $this->lineStoDetails()->whereNotNull('ng_count')->count();
        
        $totalFilledCounts = $filledStorageCount + $filledWipCount + $filledNgCount;
        $maxPossibleCounts = $totalRows * 3; // 3 count fields per row
        
        return $maxPossibleCounts > 0 ? round(($totalFilledCounts / $maxPossibleCounts) * 100) : 0;
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['period_id', 'line_id', 'tanggal_sto', 'sto_start_at', 'sto_submit_at', 'status', 'progress'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
