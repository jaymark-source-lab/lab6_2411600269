@extends('layouts.fittrack')

@section('title', 'Stock Adjustment')

@section('content')
    <h4 class="fw-bold mb-1">Stock Adjustment</h4>
    <p class="text-muted mb-3">Enter the physical count. The system calculates the difference and records an adjustment.</p>

    <div class="ft-panel" style="max-width: 720px;">
        <form method="POST" action="{{ route('adjustments.store') }}">
            @csrf

            <div class="mb-3">
                <label for="product_id" class="form-label">Product</label>
                <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">Select a product...</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id', $selected) == $product->id)>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="system_quantity" class="form-label">System Quantity</label>
                    <input type="text" id="system_quantity" class="form-control bg-light" value="—" readonly>
                </div>
                <div class="col-md-4">
                    <label for="physical_count" class="form-label">Physical Count</label>
                    <input type="number" name="physical_count" id="physical_count" min="0"
                           class="form-control @error('physical_count') is-invalid @enderror"
                           value="{{ old('physical_count') }}" required>
                    @error('physical_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Difference</label>
                    <div id="difference" class="form-control bg-light fw-bold">—</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Adjustment</label>
                <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror" required>
                    <option value="">Select a reason...</option>
                    @foreach ($reasons as $value => $label)
                        <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" rows="3" maxlength="500"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="e.g. 2 units found with cracked handles">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn ft-btn-primary">Save Adjustment</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const productSelect = document.getElementById('product_id');
    const systemInput = document.getElementById('system_quantity');
    const physicalInput = document.getElementById('physical_count');
    const differenceBox = document.getElementById('difference');
    const productApiBase = @json(url('/api/products'));

    let systemQuantity = null;

    // load the current system quantity via AJAX
    async function loadSystemQuantity() {
        systemQuantity = null;
        updateDifference();

        if (!productSelect.value) {
            systemInput.value = '—';
            return;
        }

        systemInput.value = 'Loading...';

        try {
            const response = await fetch(`${productApiBase}/${productSelect.value}`, { headers: { Accept: 'application/json' } });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const json = await response.json();
            systemQuantity = json.data.quantity;
            systemInput.value = systemQuantity;
        } catch (error) {
            systemInput.value = 'Could not load';
        }

        updateDifference();
    }

    // show physical - system with a color
    function updateDifference() {
        differenceBox.classList.remove('text-success', 'text-danger', 'text-muted');

        if (systemQuantity === null || physicalInput.value === '') {
            differenceBox.textContent = '—';
            return;
        }

        const difference = parseInt(physicalInput.value, 10) - systemQuantity;

        if (difference === 0) {
            differenceBox.textContent = '0 (matches)';
            differenceBox.classList.add('text-muted');
        } else {
            differenceBox.textContent = (difference > 0 ? '+' : '') + difference;
            differenceBox.classList.add(difference > 0 ? 'text-success' : 'text-danger');
        }
    }

    productSelect.addEventListener('change', loadSystemQuantity);
    physicalInput.addEventListener('input', updateDifference);
    loadSystemQuantity();
</script>
@endpush