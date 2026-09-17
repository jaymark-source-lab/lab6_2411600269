<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'description',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}