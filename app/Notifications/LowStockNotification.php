<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // the product that is running low
    public function __construct(public Product $product)
    {
    }

    // send to the database (bell icon) and email
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // email version
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->product;

        return (new MailMessage)
            ->subject("Low stock alert: {$product->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$product->name} ({$product->sku}) is running low.")
            ->line("Current quantity: {$product->quantity} | Reorder level: {$product->reorder_level}")
            ->action('Record a stock-in', route('transactions.create', ['product_id' => $product->id]))
            ->line('Suggested action: reorder from ' . ($product->supplier ?: 'your supplier') . '.');
    }

    // database version (saved as JSON in the data column)
    public function toArray(object $notifiable): array
    {
        $product = $this->product;

        return [
            'product_id'    => $product->id,
            'product_name'  => $product->name,
            'sku'           => $product->sku,
            'quantity'      => $product->quantity,
            'reorder_level' => $product->reorder_level,
            'message'       => "{$product->name} is low: {$product->quantity} left (reorder at {$product->reorder_level}).",
        ];
    }
}