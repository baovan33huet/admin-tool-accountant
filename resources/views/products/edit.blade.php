@extends('layouts.app')

@section('title', 'Sửa sản phẩm - ' . $store->name)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Sửa sản phẩm: {{ $product->name }} ({{ $store->name }})</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.update', [$store, $product]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="sku" class="form-label">Mã sản phẩm</label>
                    <input type="text"
                           id="sku"
                           name="sku"
                           value="{{ old('sku', $product->sku) }}"
                           class="form-control @error('sku') is-invalid @enderror"
                           required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $product->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="base_price" class="form-label">Giá gốc</label>
                        <input type="number"
                               step="0.01"
                               id="base_price"
                               name="base_price"
                               value="{{ old('base_price', $product->base_price) }}"
                               class="form-control @error('base_price') is-invalid @enderror"
                               required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">Giá bán</label>
                        <input type="number"
                               step="0.01"
                               id="price"
                               name="price"
                               value="{{ old('price', $product->price) }}"
                               class="form-control @error('price') is-invalid @enderror"
                               required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Số lượng tồn</label>
                        <input type="number"
                               id="quantity"
                               name="quantity"
                               value="{{ old('quantity', $product->quantity) }}"
                               class="form-control @error('quantity') is-invalid @enderror"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a href="{{ route('products.index', $store) }}" class="btn btn-secondary ms-2">Hủy</a>
            </form>
        </div>
    </div>
@endsection
