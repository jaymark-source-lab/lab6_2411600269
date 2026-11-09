<?php

namespace App\Http\Resources;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    // transaction as JSON
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'type'             => $this->type,
            'type_label'       => InventoryTransaction::TYPES[$this->type] ?? $this->type,
            'quantity'         => $this->quantity,
            'reference_number' => $this->reference_number,
            'product'          => [
                'id'   => $this->product->id,
                'name' => $this->product->name,
                'sku'  => $this->product->sku,
                'url'  => route('products.show', $this->product->id),
            ],
            'user'             => $this->user->name,
            'created_at'       => $this->created_at->toIso8601String(),
            'created_at_human' => $this->created_at->diffForHumans(),
            'url'              => route('transactions.show', $this->id),
        ];
    }
}