@extends('layouts.fittrack')

@section('title', 'Product Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
        @if ($product->isLowStock())
            <span class="ft-badge badge-missed">Low Stock</span>
        @else
            <span class="ft-badge badge-completed">In Stock</span>
        @endif
    </div>

    <div class="ft-panel" style="max-width: 640px;">
        <dl class="row mb-0">
            <dt class="col-sm-4 text-muted">SKU</dt>
            <dd class="col-sm-8">{{ $product->sku }}</dd>

            <dt class="col-sm-4 text-muted">Category</dt>
            <dd class="col-sm-8">{{ $product->category }}</dd>

            <dt class="col-sm-4 text-muted">Quantity</dt>
            <dd class="col-sm-8">{{ $product->quantity }}</dd>

            <dt class="col-sm-4 text-muted">Reorder Level</dt>
            <dd class="col-sm-8">{{ $product->reorder_level }}</dd>

            <dt class="col-sm-4 text-muted">Unit Price</dt>
            <dd class="col-sm-8">${{ number_format($product->unit_price, 2) }}</dd>

            <dt class="col-sm-4 text-muted">Supplier</dt>
            <dd class="col-sm-8">{{ $product->supplier ?: '—' }}</dd>

            @if ($product->description)
                <dt class="col-sm-4 text-muted">Description</dt>
                <dd class="col-sm-8">{{ $product->description }}</dd>
            @endif
        </dl>

        <div class="mt-4">
            <a href="{{ route('products.edit', $product) }}" class="btn ft-btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            <a href="{{ route('products.index') }}" class="btn btn-light">Back to Products</a>
        </div>
    </div>
@endsection
