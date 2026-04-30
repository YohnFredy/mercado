<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        $date = now()->format('Ymd');
        $lastOrder = static::query()
            ->where('order_number', 'like', "ORD-{$date}-%")
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            $lastSequence = (int) str($lastOrder->order_number)->afterLast('-')->toString();
            $sequence = $lastSequence + 1;
        }

        return sprintf('ORD-%s-%04d', $date, $sequence);
    }
}
