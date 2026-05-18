<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'item_name',
        'item_condition',
        'item_quantity',
        'item_quantity_unit',
        'harvest_date',
        'region',
        'item_brand',
        'item_material',
        'item_season',
        'item_sizes',
        'item_colors',
    ];

    protected $casts = [
        'item_quantity' => 'decimal:2',
        'harvest_date' => 'date',
        'item_sizes' => 'array',
        'item_colors' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
