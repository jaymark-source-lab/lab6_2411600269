@extends('layouts.fittrack')

@section('title', 'Transaction Details')

@section('content')
    @php
        // badge style per type
        $badges = [
            'stock_in'   => 'badge-completed',
            'stock_out'  => 'badge-missed',
            'adjustment' => 'badge-scheduled',
        ];
        $product = $transaction->product;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Transaction #{{ $transaction->id }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Back to list
            </a>
            @can('admin')
                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                      onsubmit="return confirm('Delete this transaction? Its stock change will be reversed.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                </form>
            @endcan
        </div>
    </div>

    <div class="row g-3">
        {{-- transaction details --}}
        <div class="col-lg-4">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-receipt"></i> Transaction</div>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Type</dt>
                    <dd class="col-7">
                        <span class="ft-badge {{ $badges[$transaction->type] }}">
                            {{ App\Models\InventoryTransaction::TYPES[$transaction->type] }}
                        </span>
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Quantity</dt>
                    <dd class="col-7 fw-semibold {{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Reference</dt>
                    <dd class="col-7">{{ $transaction->reference_number ?: '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Date</dt>
                    <dd class="col-7">{{ $transaction->created_at->format('M j, Y g:i A') }}</dd>

                    <dt class="col-5 text-muted fw-normal">Notes</dt>
                    <dd class="col-7 mb-0">{{ $transaction->notes ?: '—' }}</dd>
                </dl>
            </div>
        </div>

        {{-- product info --}}
        <div class="col-lg-4">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-box-seam"></i> Product</div>
                <dl class="row">
                    <dt class="col-5 text-muted fw-normal">Name</dt>
                    <dd class="col-7">{{ $product->name }}</dd>

                    <dt class="col-5 text-muted fw-normal">SKU</dt>
                    <dd class="col-7">{{ $product->sku }}</dd>

                    <dt class="col-5 text-muted fw-normal">Category</dt>
                    <dd class="col-7">{{ $product->category }}</dd>

                    <dt class="col-5 text-muted fw-normal">Current Stock</dt>
                    <dd class="col-7">
                        {{ $product->quantity }}
                        <span class="badge text-bg-{{ $product->stock_status_color }}">{{ $product->stock_status }}</span>
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Unit Price</dt>
                    <dd class="col-7">${{ number_format($product->unit_price, 2) }}</dd>
                </dl>
                <a href="{{ route('products.show', $product) }}" class="btn btn-sm ft-btn-primary">View Product</a>
            </div>
        </div>

        {{-- who recorded it --}}
        <div class="col-lg-4">
            <div class="ft-panel">
                <div class="ft-panel-title"><i class="bi bi-person-badge"></i> Recorded By</div>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Name</dt>
                    <dd class="col-7">{{ $transaction->user->name }}</dd>

                    <dt class="col-5 text-muted fw-normal">Email</dt>
                    <dd class="col-7 text-break">{{ $transaction->user->email }}</dd>

                    <dt class="col-5 text-muted fw-normal">Role</dt>
                    <dd class="col-7 mb-0">{{ $transaction->user->is_admin ? 'Admin' : 'Staff' }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection