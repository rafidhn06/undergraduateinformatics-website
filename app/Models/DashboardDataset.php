<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardDataset extends Model
{
    use HasFactory;

    protected $table = 'dashboard_datasets';

    protected $fillable = [
        'title',
        'slug',
        'sheet_name',
        'chart_type',
        'x_label',
        'y_label',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DashboardDatasetItem::class, 'dataset_id');
    }
}