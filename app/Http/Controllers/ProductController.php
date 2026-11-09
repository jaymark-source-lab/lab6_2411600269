<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // list products with category filter and search
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(10);
        $categories = Product::select('category')->distinct()->pluck('category');

        return view('products.index', compact('products', 'categories'));
    }

    // show the add form
    public function create()
    {
        return view('products.create');
    }

    // save a new product (quantity here is the opening stock)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:255|unique:products,sku',
            'category'      => 'required|string|max:255',
            'description'   => 'nullable|string',
            'quantity'      => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    // show one product with its transaction history
    public function show(Request $request, Product $product)
    {
        // all movements, newest first
        $transactions = $product->transactions()
            ->with('user')
            ->recent()
            ->orderByDesc('id')
            ->get();

        // work backwards from current stock to get the balance after each one
        $balance = $product->quantity;

        foreach ($transactions as $transaction) {
            $transaction->balance_after = $balance;
            $balance -= $transaction->quantity;
        }

        // stock before the first recorded transaction
        $openingBalance = $balance;

        // date range filter (applied after balances so they stay correct)
        if ($request->filled('from')) {
            $transactions = $transactions->filter(fn ($t) => $t->created_at->toDateString() >= $request->from);
        }

        if ($request->filled('to')) {
            $transactions = $transactions->filter(fn ($t) => $t->created_at->toDateString() <= $request->to);
        }

        return view('products.show', compact('product', 'transactions', 'openingBalance'));
    }

    // show the edit form
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // update product details (stock is NOT changed here)
    public function update(Request $request, Product $product)
    {
        // quantity is left out on purpose: stock only changes through transactions
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:255|unique:products,sku,' . $product->id,
            'category'      => 'required|string|max:255',
            'description'   => 'nullable|string',
            'reorder_level' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // delete a product (its transactions are removed by cascade)
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}