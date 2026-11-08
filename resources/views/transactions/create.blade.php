@extends('layouts.fittrack')

@section('title', 'Record Transaction')

@section('content')
    <h4 class="fw-bold mb-3">Record Stock Movement</h4>

    <div class="ft-panel" style="max-width: 720px;">
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf

            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">Select a product...</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}"
                                @selected(old('product_id', $selected) == $product->id)>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div id="stock-info" class="form-text"></div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Transaction Type</label>
                @foreach ($types as $value => $label)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type" id="type_{{ $value }}"
                               value="{{ $value }}" @checked(old('type', 'stock_in') === $value)>
                        <label class="form-check-label" for="type_{{ $value }}">{{ $label }}</label>
                    </div>
                @endforeach
                @error('type') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" min="1"
                           class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity') }}" required>
                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="reference_number" class="form-label">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" maxlength="50"
                           class="form-control @error('reference_number') is-invalid @enderror"
                           value="{{ old('reference_number') }}" placeholder="e.g. PO-0042 or SO-1001">
                    @error('reference_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" rows="3" maxlength="500"
                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn ft-btn-primary">Save Transaction</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stock-info');
    const typeRadios = document.querySelectorAll('input[name="type"]');

    // show current stock and limit stock-out quantity
    function updateStockInfo() {
        const option = productSelect.selectedOptions[0];
        const isStockOut = document.querySelector('input[name="type"]:checked')?.value === 'stock_out';

        if (!option || !option.value) {
            stockInfo.textContent = '';
            quantityInput.removeAttribute('max');
            return;
        }

        const stock = parseInt(option.dataset.quantity, 10);

        stockInfo.innerHTML = `Current stock: <strong>${stock}</strong>`
            + (isStockOut ? ` · you can remove up to <strong>${stock}</strong>` : '');

        if (isStockOut) {
            quantityInput.max = stock;
        } else {
            quantityInput.removeAttribute('max');
        }
    }

    productSelect.addEventListener('change', updateStockInfo);
    typeRadios.forEach(radio => radio.addEventListener('change', updateStockInfo));
    updateStockInfo();
</script>
@endpush