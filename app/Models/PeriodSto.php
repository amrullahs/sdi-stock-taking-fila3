<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class PeriodSto extends Model
{
    protected $table = 't_period_sto';
    
    protected $fillable = [
        'period_sto',
        'created_by',
        'site',
    ];
    
    protected $casts = [
        'period_sto' => 'date',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->name;
            }
        });
    }
    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'name');
    }
}
