<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'cost',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost' => 'decimal:2',
    ];

    /**
     * Scope to return only active cities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @return BelongsTo<Department, City> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
