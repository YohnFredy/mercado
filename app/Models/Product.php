<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

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
            'selling_price_incl_vat' => 'decimal:2',
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

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('brand', fn ($bq) => $bq->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('categories', fn ($cq) => $cq->where('name', 'like', '%'.$search.'%'));
        });
    }

    public function scopePriceMin($query, $price)
    {
        return $query->where('selling_price_incl_vat', '>=', $price);
    }

    public function scopePriceMax($query, $price)
    {
        return $query->where('selling_price_incl_vat', '<=', $price);
    }

    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('selling_price_incl_vat', $direction);
    }
}
