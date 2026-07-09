<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'quantity_in_stock',
        'price_per_item',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in_stock' => 'integer',
            'price_per_item' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function getTotalValueAttribute(): float
    {
        return round($this->quantity_in_stock * $this->price_per_item, 2);
    }
}
