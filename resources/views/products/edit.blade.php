@extends('layouts.fittrack')

@section('title', 'Edit Product')

@section('content')
    <h4 class="fw-bold mb-3">Edit Product</h4>

    <div class="ft-panel" style="max-width: 720px;">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')
            @include('products._form')

            <div class="mt-3">
                <button type="submit" class="btn ft-btn-primary">Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
