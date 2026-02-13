<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('name')->paginate(10);
        
        // Tháng hiện tại
        $currentMonth = now()->format('Y-m');
        $startDate = \Carbon\Carbon::parse($currentMonth . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Doanh thu từng cửa hàng trong tháng này
        $storeRevenues = Store::select(
                'stores.id',
                'stores.name',
                DB::raw('COALESCE(SUM(product_sales.quantity * product_sales.sale_price), 0) as revenue')
            )
            ->leftJoin('products', 'stores.id', '=', 'products.store_id')
            ->leftJoin('product_sales', function($join) use ($startDate, $endDate) {
                $join->on('products.id', '=', 'product_sales.product_id')
                     ->whereBetween('product_sales.sale_date', [$startDate, $endDate]);
            })
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return view('dashboard', compact(
            'stores',
            'storeRevenues',
            'currentMonth'
        ));
    }
}

