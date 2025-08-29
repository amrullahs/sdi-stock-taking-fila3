<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PeriodSto extends Model
{
    use LogsActivity;
    protected $table = 't_period_sto';
    
    protected $fillable = [
        'period_sto',
        'created_by',
        'site',
        'status',
    ];
    
    protected $casts = [
        'period_sto' => 'date',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->id;
            }
        });
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['period_sto', 'site', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function stockOnHands(): HasMany
    {
        return $this->hasMany(StockOnHand::class, 'period_sto_id');
    }
    
    /**
     * Get the line STOs for the period STO
     */
    public function lineStos(): HasMany
    {
        return $this->hasMany(LineSto::class, 'period_id');
    }
    
    /**
     * Get formatted period STO for display
     */
    public function getFormattedPeriodStoAttribute(): string
    {
        return $this->period_sto ? $this->period_sto->format('d-m-Y') : '';
    }

}
