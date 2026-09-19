<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardDatasetItem extends Model
{
    use HasFactory;

    protected $table = 'dashboard_dataset_items';

    protected $fillable = [
        'dataset_id',
        'label',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(DashboardDataset::class, 'dataset_id');
    }
}
