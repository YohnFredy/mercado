<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_document',
        'shipping_address',
        'shipping_country',
        'shipping_department',
        'shipping_city',
        'shipping_notes',
        'subtotal',
        'shipping_cost',
        'total',
        'pts',
        'status',
        'payment_method',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique, human-readable order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            // Genera un código de 8 caracteres alfanuméricos (ej: A1B2-C3D4)
            $random = strtoupper(Str::random(8));
            $number = substr($random, 0, 4).'-'.substr($random, 4, 4);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}
