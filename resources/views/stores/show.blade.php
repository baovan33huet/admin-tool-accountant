@extends('layouts.app')

@section('title', 'Sản phẩm - ' . $store->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Sản phẩm của cửa hàng: {{ $store->name }}</h4>
        <div class="btn-group" role="group" aria-label="Thao tác sản phẩm">
            <a href="{{ route('products.create', $store) }}" class="btn btn-sm btn-primary me-2 rounded-pill">
                Thêm sản phẩm
            </a>
            <a href="{{ route('product-imports.create', $store) }}" class="btn btn-sm btn-outline-secondary me-2 rounded-pill">
                Nhập hàng
            </a>
            <a href="{{ route('product-sales.create', $store) }}" class="btn btn-sm btn-success me-2 rounded-pill">
                Bán hàng
            </a>
            <a href="{{ route('products.revenue', $store) }}" class="btn btn-sm btn-info me-2 rounded-pill">
                Xem doanh thu
            </a>
            <a href="{{ route('product-imports.index', $store) }}" class="btn btn-sm btn-warning me-2 rounded-pill">
                Xem nhập hàng
            </a>
            <a href="{{ route('product-sales.index', $store) }}" class="btn btn-sm btn-outline-success rounded-pill">
                Xem bán hàng
            </a>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="alert alert-info">
            Cửa hàng này chưa có sản phẩm nào.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-start">Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-end">Giá gốc</th>
                            <th class="text-end">Giá bán</th>
                            <th class="text-end">Tồn kho</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $index => $product)
                            <tr>
                                <td>{{ $products->firstItem() + $index }}</td>
                                <td class="text-start">{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-end">{{ number_format($product->base_price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $product->quantity }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.edit', [$store, $product]) }}" class="btn btn-outline-primary me-2">
                                            Sửa
                                        </a>
                                        <form action="{{ route('products.destroy', [$store, $product]) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                    <div class="card-footer">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

