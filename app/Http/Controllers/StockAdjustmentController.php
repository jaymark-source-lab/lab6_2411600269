<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockAdjustmentController extends Controller
{
    // reasons for an adjustment (value => label)
    public const REASONS = [
        'physical_count' => 'Physical count discrepancy',
        'damaged'        => 'Damaged goods',
        'theft_loss'     => 'Theft / loss',
        'system_error'   => 'System error',
        'other'          => 'Other',
    ];

    // show the adjustment form (admin only)
    public function create(Request $request)
    {
        Gate::authorize('admin');

        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $reasons = self::REASONS;
        $selected = $request->query('product_id');

        return view('adjustments.create', compact('products', 'reasons', 'selected'));
    }

    // compare the physical count with the system and record the difference
    public function store(Request $request)
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason'         => ['required', Rule::in(array_keys(self::REASONS))],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $transaction = DB::transaction(function () use ($validated, $request) {
            // lock so the count can't change while we compare
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            $systemQuantity = $product->quantity;
            $physicalCount = (int) $validated['physical_count'];
            $difference = $physicalCount - $systemQuantity;

            if ($difference === 0) {
                throw ValidationException::withMessages([
                    'physical_count' => "No adjustment needed: the count matches the system ({$systemQuantity}).",
                ]);
            }

            // keep the full story in the notes for the audit trail
            $notes = sprintf(
                'Reason: %s. System: %d, counted: %d.',
                self::REASONS[$validated['reason']],
                $systemQuantity,
                $physicalCount
            );

            if (! empty($validated['notes'])) {
                $notes .= ' ' . $validated['notes'];
            }

            // the observer applies the difference to the product
            return InventoryTransaction::create([
                'product_id'       => $product->id,
                'user_id'          => $request->user()->id,
                'type'             => 'adjustment',
                'quantity'         => $difference,
                'reference_number' => 'ADJ-' . now()->format('Ymd-His'),
                'notes'            => $notes,
            ]);
        });

        $sign = $transaction->quantity > 0 ? '+' : '';

        return redirect()->route('transactions.show', $transaction)
            ->with('success', "Stock adjusted by {$sign}{$transaction->quantity}.");
    }
}