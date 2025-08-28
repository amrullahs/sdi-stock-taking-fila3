<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineModelDetail extends Model
{
    protected $table = 'm_line_model_detail';

    protected $fillable = [
        'line_id',
        'model_id',
        'type',
        'qad_number',
        'part_name',
        'part_number',
        'desc',
        'supplier',
        'suplier_code',
        'std_packing',
        'storage',
        'wip_id',
        'image',
    ];

    protected $casts = [
        'std_packing' => 'integer',
    ];

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(ModelStructure::class, 'model_id', 'model');
    }
}
