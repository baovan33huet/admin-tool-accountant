@extends('layouts.app')

@section('title', 'Bán hàng - ' . $store->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Bán hàng tháng {{ \Carbon\Carbon::parse($month.'-01')->format('m/Y') }}
            – {{ $store->name }}
        </h4>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('product-sales.index', $store) }}" class="row g-3">
                <div class="col-md-4">
                    <label for="month" class="form-label">Tháng</label>
                    <input type="month" name="month" id="month" class="form-control form-control-sm"
                           value="{{ $month }}">
                </div>
                <div class="col-md-6">
                    <label for="product_id" class="form-label">Sản phẩm</label>
                    <select name="product_id" id="product_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $selectedProductId == $product->id ? 'selected' : '' }}>
                                {{ $product->sku }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-sm btn-primary w-100" type="submit">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    @if($saleData->isEmpty())
        <div class="alert alert-info">
            Chưa có dữ liệu bán hàng trong tháng này.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th class="text-end">Số lượng bán</th>
                            <th class="text-end">Tổng doanh thu</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($saleData as $index => $row)
                            <tr>
                                <td>{{ $saleData->firstItem() + $index }}</td>
                                <td>{{ $row->sku }}</td>
                                <td>{{ $row->name }}</td>
                                <td class="text-end">{{ $row->total_quantity }}</td>
                                <td class="text-end">{{ number_format($row->total_revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Tổng</td>
                            <td class="text-end">{{ $totalQuantity }}</td>
                            <td class="text-end">{{ number_format($totalRevenue, 0, ',', '.') }} đ</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                @if($saleData->hasPages())
                    <div class="card-footer">
                        {{ $saleData->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

