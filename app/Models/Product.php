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

    // all stock movements for this product
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // the most recent stock movement
    public function latestTransaction()
    {
        return $this->hasOne(InventoryTransaction::class)->latestOfMany();
    }

    // at or below reorder level
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    // nothing left
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    // readable status: $product->stock_status
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }

        return $this->isLowStock() ? 'Low Stock' : 'In Stock';
    }

    // bootstrap color: $product->stock_status_color
    public function getStockStatusColorAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'danger';
        }

        return $this->isLowStock() ? 'warning' : 'success';
    }

    // Product::lowStock()
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    // Product::outOfStock()
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    // Product::inCategory('Accessories')
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}