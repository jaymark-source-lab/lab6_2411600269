@php
    $old = fn ($field, $default = null) => old($field, $product->$field ?? $default);
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $old('name') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ $old('sku') }}" required>
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ $old('category') }}" required>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Supplier</label>
        <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror" value="{{ $old('supplier') }}">
        @error('supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ $old('quantity') }}" required>
        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Reorder Level</label>
        <input type="number" name="reorder_level" min="0" class="form-control @error('reorder_level') is-invalid @enderror" value="{{ $old('reorder_level') }}" required>
        @error('reorder_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Unit Price</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ $old('unit_price') }}" required>
        </div>
        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ $old('description') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
