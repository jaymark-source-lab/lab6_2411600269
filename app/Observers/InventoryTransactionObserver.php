<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    // after a transaction is saved: apply it to the product's stock
    public function created(InventoryTransaction $transaction): void
    {
        // lock the product row so two users can't update it at the same time
        $product = Product::lockForUpdate()->findOrFail($transaction->product_id);

        $oldQuantity = $product->quantity;
        $newQuantity = $oldQuantity + $transaction->quantity;

        // block negative stock (Option A: throw and rollback)
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Not enough stock. Only {$product->quantity} {$product->name} available.",
            ]);
        }

        $product->update(['quantity' => $newQuantity]);

        $this->alertIfNowLow($product, $oldQuantity);
    }

    // after a transaction is deleted: undo its stock change
    public function deleted(InventoryTransaction $transaction): void
    {
        $product = Product::lockForUpdate()->find($transaction->product_id);

        // product already removed, nothing to reverse
        if (! $product) {
            return;
        }

        $oldQuantity = $product->quantity;
        $newQuantity = $oldQuantity - $transaction->quantity;

        // e.g. deleting a stock-in after those items were already sold
        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => "Can't delete: {$product->name} stock would go below zero.",
            ]);
        }

        $product->update(['quantity' => $newQuantity]);

        $this->alertIfNowLow($product, $oldQuantity);
    }

    // notify admins only when stock first crosses into low stock
    private function alertIfNowLow(Product $product, int $oldQuantity): void
    {
        $wasAlreadyLow = $oldQuantity <= $product->reorder_level;

        if ($wasAlreadyLow || ! $product->isLowStock()) {
            return;
        }

        $admins = User::where('is_admin', true)->get();

        // send only if the database transaction actually commits
        DB::afterCommit(fn () => Notification::send($admins, new LowStockNotification($product)));
    }
}