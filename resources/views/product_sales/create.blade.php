@extends('layouts.app')

@section('title', 'Bán hàng - ' . $store->name)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Bán hàng cho cửa hàng: {{ $store->name }}</h4>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('product-sales.store', $store) }}">
                @csrf

                <div class="mb-3">
                    <label for="product_id" class="form-label">Sản phẩm</label>
                    <input type="text" 
                           id="product_search" 
                           class="form-control mb-2" 
                           placeholder="🔍 Tìm kiếm sản phẩm (mã SKU hoặc tên)..."
                           autocomplete="off">
                    <select id="product_id"
                            name="product_id"
                            class="form-select @error('product_id') is-invalid @enderror"
                            required>
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-search="{{ strtolower($product->sku . ' ' . $product->name) }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->sku }} - {{ $product->name }} (Tồn: {{ $product->quantity }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Gõ vào ô tìm kiếm phía trên để lọc danh sách sản phẩm</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Số lượng bán</label>
                        <input type="number"
                               id="quantity"
                               name="quantity"
                               value="{{ old('quantity', 1) }}"
                               class="form-control @error('quantity') is-invalid @enderror"
                               min="1"
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sale_date" class="form-label">Ngày bán</label>
                        <input type="date"
                               id="sale_date"
                               name="sale_date"
                               value="{{ old('sale_date', now()->toDateString()) }}"
                               class="form-control @error('sale_date') is-invalid @enderror"
                               required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Lưu phiếu bán</button>
                <a href="{{ route('products.index', $store) }}" class="btn btn-secondary ms-2">Hủy</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('product_search');
            const select = document.getElementById('product_id');
            const options = Array.from(select.querySelectorAll('option'));
            const allOptions = options.slice(); // Lưu tất cả options gốc

            // Khôi phục lại tất cả options
            function restoreOptions() {
                select.innerHTML = '';
                allOptions.forEach(function(option) {
                    select.appendChild(option.cloneNode(true));
                });
            }

            // Khởi tạo lại options
            restoreOptions();

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                // Xóa tất cả options hiện tại
                select.innerHTML = '';
                
                // Thêm option "-- Chọn sản phẩm --" luôn
                const defaultOption = allOptions[0].cloneNode(true);
                select.appendChild(defaultOption);
                
                // Thêm các options khớp với search term
                allOptions.slice(1).forEach(function(option) {
                    const searchText = option.getAttribute('data-search') || '';
                    if (searchTerm === '' || searchText.includes(searchTerm)) {
                        select.appendChild(option.cloneNode(true));
                    }
                });

                // Nếu có search term và có kết quả, scroll đến option đầu tiên
                if (searchTerm !== '' && select.options.length > 1) {
                    select.selectedIndex = 1;
                } else {
                    select.selectedIndex = 0;
                }
            });

            // Khi chọn sản phẩm, hiển thị tên sản phẩm đã chọn trong search box
            select.addEventListener('change', function() {
                if (this.value !== '') {
                    const selectedOption = this.options[this.selectedIndex];
                    searchInput.value = selectedOption.text.trim();
                } else {
                    searchInput.value = '';
                }
            });
        });
    </script>
@endsection
