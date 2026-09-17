@extends('layouts.fittrack')

@section('title', 'Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Product Inventory</h4>
        <a href="{{ route('products.create') }}" class="btn ft-btn-primary">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="ft-filter-bar">
        <div class="ft-filter-group">
            <label for="category">Category</label>
            <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group flex-grow-1">
            <label for="search">Search</label>
            <div class="ft-search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search by product name...">
            </div>
        </div>
        <div class="ft-filter-group">
            <button type="submit" class="btn ft-btn-primary">Filter</button>
            <a href="{{ route('products.index') }}" class="btn btn-light">Reset</a>
        </div>
    </form>

    <div class="ft-panel">
        <div class="table-responsive">
            <table class="table ft-table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                        <th>Unit Price</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="{{ $product->isLowStock() ? 'table-danger bg-opacity-10' : '' }}">
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>{{ $product->reorder_level }}</td>
                            <td>${{ number_format($product->unit_price, 2) }}</td>
                            <td>{{ $product->supplier ?: '—' }}</td>
                            <td>
                                @if ($product->isLowStock())
                                    <span class="ft-badge badge-missed">Low Stock</span>
                                @else
                                    <span class="ft-badge badge-completed">In Stock</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
@endsection
