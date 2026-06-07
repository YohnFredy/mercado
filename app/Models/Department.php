<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to return only active departments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to return departments that have at least one active city.
     */
    public function scopeWithActiveCities(Builder $query): Builder
    {
        return $query->whereHas('cities', fn (Builder $q) => $q->where('is_active', true));
    }

    /** @return HasMany<City> */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
