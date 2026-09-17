@extends('layouts.fittrack')

@section('title', 'Add Product')

@section('content')
    <h4 class="fw-bold mb-3">Add New Product</h4>

    <div class="ft-panel" style="max-width: 720px;">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            @include('products._form', ['product' => null])

            <div class="mt-3">
                <button type="submit" class="btn ft-btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
@endsection
