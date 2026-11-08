<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\InventoryTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(InventoryTransactionObserver::class)]
class InventoryTransaction extends Model
{
    // allowed transaction types (value => label)
    public const TYPES = [
        'stock_in'   => 'Stock In',
        'stock_out'  => 'Stock Out',
        'adjustment' => 'Adjustment',
    ];

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    // transaction belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // transaction belongs to the user who made it
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // only stock-in rows
    public function scopeStockIn($query)
    {
        return $query->where('type', 'stock_in');
    }

    // only stock-out rows
    public function scopeStockOut($query)
    {
        return $query->where('type', 'stock_out');
    }

    // newest first
    public function scopeRecent($query)
    {
        return $query->latest();
    }
}