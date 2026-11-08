<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventoryTransactionController extends Controller
{
    // list transactions with filters
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $types = InventoryTransaction::TYPES;

        return view('transactions.index', compact('transactions', 'products', 'types'));
    }

    // show the record-transaction form
    public function create(Request $request)
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity']);
        $types = ['stock_in' => 'Stock In', 'stock_out' => 'Stock Out'];
        $selected = $request->query('product_id');

        return view('transactions.create', compact('products', 'types', 'selected'));
    }

    // validate and save a new transaction
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id'       => ['required', 'exists:products,id'],
            'type'             => ['required', Rule::in(['stock_in', 'stock_out'])],
            'quantity'         => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // extra check: enough stock for a stock-out
        $validator->after(function ($validator) use ($request) {
            if ($request->type !== 'stock_out') {
                return;
            }

            $product = Product::find($request->product_id);

            if ($product && (int) $request->quantity > $product->quantity) {
                $validator->errors()->add('quantity', "Only {$product->quantity} in stock for {$product->name}.");
            }
        });

        $validated = $validator->validate();

        // stock-out is saved as a negative number
        $quantity = $validated['type'] === 'stock_out'
            ? -$validated['quantity']
            : $validated['quantity'];

        // insert + stock update succeed or fail together
        DB::transaction(function () use ($validated, $quantity, $request) {
            InventoryTransaction::create([
                'product_id'       => $validated['product_id'],
                'user_id'          => $request->user()->id,
                'type'             => $validated['type'],
                'quantity'         => $quantity,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes'            => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction recorded and stock updated.');
    }

    // show one transaction
    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    // delete a transaction (admin only)
    public function destroy(InventoryTransaction $transaction)
    {
        Gate::authorize('admin');

        // delete + stock reversal succeed or fail together
        DB::transaction(fn () => $transaction->delete());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted and stock reversed.');
    }
}