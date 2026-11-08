@extends('layouts.fittrack')

@section('title', 'Transactions')

@section('content')
    @php
        // badge style per type
        $badges = [
            'stock_in'   => 'badge-completed',
            'stock_out'  => 'badge-missed',
            'adjustment' => 'badge-scheduled',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Stock Transactions</h4>
        <a href="{{ route('transactions.create') }}" class="btn ft-btn-primary">
            <i class="bi bi-plus-lg"></i> Record Transaction
        </a>
    </div>

    {{-- errors, e.g. a delete that would make stock negative --}}
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="GET" action="{{ route('transactions.index') }}" class="ft-filter-bar">
        <div class="ft-filter-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-select">
                <option value="">All products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All types</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-filter-group">
            <label for="from">From</label>
            <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="ft-filter-group">
            <label for="to">To</label>
            <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="ft-filter-group">
            <button type="submit" class="btn ft-btn-primary">Filter</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-light">Reset</a>
        </div>
    </form>

    <div class="ft-panel">
        <div class="table-responsive">
            <table class="table ft-table align-middle">
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th class="text-end">Quantity</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('M j, Y g:i A') }}</td>
                            <td>
                                <a href="{{ route('products.show', $transaction->product) }}" class="text-decoration-none">
                                    {{ $transaction->product->name }}
                                </a>
                                <div class="small text-muted">{{ $transaction->product->sku }}</div>
                            </td>
                            <td>
                                <span class="ft-badge {{ $badges[$transaction->type] }}">
                                    {{ $types[$transaction->type] }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold {{ $transaction->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->quantity > 0 ? '+' : '' }}{{ $transaction->quantity }}
                            </td>
                            <td>{{ $transaction->reference_number ?: '—' }}</td>
                            <td>{{ $transaction->user->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('admin')
                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this transaction? Its stock change will be reversed.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection