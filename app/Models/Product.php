<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    protected $fillable = [
        'brand_id',
        'sku',
        'barcode',
        'title',
        'slug',
        'description',
        'cost_price_excl_vat',
        'selling_price_excl_vat',
        'vat_percentage',
        'discount_percentage',
        'specifications',
        'additional_information',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'is_active' => 'boolean',
            'cost_price_excl_vat' => 'decimal:2',
            'selling_price_excl_vat' => 'decimal:2',
            'vat_percentage' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
