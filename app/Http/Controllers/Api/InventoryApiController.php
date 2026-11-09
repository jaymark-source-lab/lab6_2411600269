<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Models\InventoryTransaction;
use App\Models\Product;

class InventoryApiController extends Controller
{
    // GET /api/dashboard/stats
    public function stats()
    {
        return response()->json([
            'total_products'     => Product::count(),
            'total_units'        => (int) Product::sum('quantity'),
            'low_stock'          => Product::lowStock()->count(),
            'out_of_stock'       => Product::outOfStock()->count(),
            'inventory_value'    => round((float) Product::selectRaw('SUM(quantity * unit_price) as total')->value('total'), 2),
            'transactions_today' => InventoryTransaction::whereDate('created_at', today())->count(),
            'generated_at'       => now()->toIso8601String(),
        ]);
    }

    // GET /api/products/low-stock
    public function lowStock()
    {
        $products = Product::lowStock()->orderBy('quantity')->get();

        return ProductResource::collection($products);
    }

    // GET /api/transactions/recent
    public function recentTransactions()
    {
        $transactions = InventoryTransaction::with(['product', 'user'])->recent()->take(10)->get();

        return TransactionResource::collection($transactions);
    }
}