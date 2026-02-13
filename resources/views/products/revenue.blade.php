@extends('layouts.app')

@section('title', 'Doanh thu - ' . $store->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Doanh thu tháng {{ \Carbon\Carbon::parse($month.'-01')->format('m/Y') }}
            – {{ $store->name }}
        </h4>
        <form method="GET" action="{{ route('products.revenue', $store) }}" class="d-flex align-items-center">
            <input type="month" name="month" class="form-control form-control-sm me-2"
                   value="{{ $month }}">
            <button class="btn btn-sm btn-outline-primary" type="submit">Xem</button>
        </form>
    </div>

    @if($revenueData->isEmpty())
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
                            <th class="text-end">Doanh thu</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($revenueData as $index => $row)
                            <tr>
                                <td>{{ $revenueData->firstItem() + $index }}</td>
                                <td>{{ $row->sku }}</td>
                                <td>{{ $row->name }}</td>
                                <td class="text-end">{{ $row->total_quantity }}</td>
                                <td class="text-end">{{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Tổng</td>
                            <td class="text-end">{{ $totalQuantity }}</td>
                            <td class="text-end">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                @if($revenueData->hasPages())
                    <div class="card-footer">
                        {{ $revenueData->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

