@extends('layouts.fittrack')

@section('title', 'Product Details')

@section('content')
    @php
        // badge style per stock status
        $statusBadge = [
            'danger'  => 'badge-missed',
            'warning' => 'badge-scheduled',
            'success' => 'badge-completed',
        ][$product->stock_status_color];

        // badge style per transaction type
        $typeBadge = [
            'stock_in'   => 'badge-completed',
            'stock_out'  => 'badge-missed',
            'adjustment' => 'badge-scheduled',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
        <span class="ft-badge {{ $statusBadge }} text-nowrap">{{ $product->stock_status }}</span>
    </div>

    <div class="row g-3">
        {{-- product details --}}
        <div class="col-lg-5">
            <div class="ft-panel">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">SKU</dt>
                    <dd class="col-sm-7">{{ $product->sku }}</dd>

                    <dt class="col-sm-5 text-muted">Category</dt>
                    <dd class="col-sm-7">{{ $product->category }}</dd>

                    <dt class="col-sm-5 text-muted">Quantity</dt>
                    <dd class="col-sm-7 fw-bold">{{ $product->quantity }}</dd>

                    <dt class="col-sm-5 text-muted">Reorder Level</dt>
                    <dd class="col-sm-7">{{ $product->reorder_level }}</dd>

                    <dt class="col-sm-5 text-muted">Unit Price</dt>
                    <dd class="col-sm-7">${{ number_format($product->unit_price, 2) }}</dd>

                    <dt class="col-sm-5 text-muted">Stock Value</dt>
                    <dd class="col-sm-7">${{ number_format($product->quantity * $product->unit_price, 2) }}</dd>

                    <dt class="col-sm-5 text-muted">Supplier</dt>
                    <dd class="col-sm-7">{{ $product->supplier ?: '—' }}</dd>

                    @if ($product->description)
                        <dt class="col-sm-5 text-muted">Description</dt>
                        <dd class="col-sm-7">{{ $product->description }}</dd>
                    @endif
                </dl>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <a href="{{ route('transactions.create', ['product_id' => $product->id]) }}" class="btn ft-btn-primary">
                        <i class="bi bi-plus-lg"></i> Record Transaction
                    </a>
                    @can('admin')
                        <a href="{{ route('adjustments.create', ['product_id' => $product->id]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-clipboard-check"></i> Adjust
                        </a>
                    @endcan
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-light"><i class="bi bi-pencil"></i> Edit</a>
                    <a href="{{ route('products.index') }}" class="btn btn-light">Back</a>
                </div>
            </div>
        </div>

        {{-- transaction history --}}
        <div class="col-lg-7">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-clock-history"></i> Transaction History</div>

                <form method="GET" action="{{ route('products.show', $product) }}" class="d-flex flex-wrap gap-2 align-items-end mb-3">
                    <div>
                        <label for="from" class="form-label small text-muted mb-1">From</label>
                        <input type="date" name="from" id="from" class="form-control form-control-sm" value="{{ request('from') }}">
                    </div>
                    <div>
                        <label for="to" class="form-label small text-muted mb-1">To</label>
                        <input type="date" name="to" id="to" class="form-control form-control-sm" value="{{ request('to') }}">
                    </div>
                    <button type="submit" class="btn btn-sm ft-btn-primary">Filter</button>
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-light">Reset</a>
                </form>

                <div class="table-responsive">
                    <table class="table ft-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Reference</th>
                                <th class="text-end">Change</th>
                                <th class="text-end">Balance</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="small">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-decoration-none">
                                            {{ $transaction->created_at->format('M j, Y g:i A') }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="ft-badge {{ $typeBadge[$transaction->type] }} text-nowrap">
                                            {{ App\Models\InventoryTransaction::TYPES[$transaction->type] }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $transaction->reference_number ?: '—' }}</td>
                                    <td class="text-end fw-semibold {{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                                    </td>
                                    <td class="text-end fw-bold">{{ $transaction->balance_after }}</td>
                                    <td class="small">{{ $transaction->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No transactions in this period.</td>
                                </tr>
                            @endforelse

                            {{-- starting point before any recorded transaction --}}
                            @if (! request()->filled('from'))
                                <tr class="table-light">
                                    <td colspan="4" class="small text-muted">Opening balance (before recorded transactions)</td>
                                    <td class="text-end fw-bold">{{ $openingBalance }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection