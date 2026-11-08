<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    // after a transaction is saved: apply it to the product's stock
    public function created(InventoryTransaction $transaction): void
    {
        // lock the product row so two users can't update it at the same time
        $product = Product::lockForUpdate()->findOrFail($transaction->product_id);

        $newQuantity = $product->quantity + $transaction->quantity;

        // block negative stock (Option A: throw and rollback)
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Not enough stock. Only {$product->quantity} {$product->name} available.",
            ]);
        }

        $product->update(['quantity' => $newQuantity]);
    }
}