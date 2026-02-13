<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSaleController extends Controller
{
    public function index(Store $store, Request $request)
    {
        $stores = Store::orderBy('name')->get();
        $products = Product::where('store_id', $store->id)->orderBy('name')->get();

        // Filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $productId = $request->get('product_id');
        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = ProductSale::select(
                'products.id',
                'products.sku',
                'products.name',
                DB::raw('SUM(product_sales.quantity) as total_quantity'),
                DB::raw('SUM(product_sales.quantity * product_sales.sale_price) as total_revenue')
            )
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_sales.sale_date', [$startDate, $endDate]);

        // Filter theo sản phẩm nếu có
        if ($productId) {
            $query->where('products.id', $productId);
        }

        $saleData = $query->groupBy('products.id', 'products.sku', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->paginate(10);

        // Tính tổng từ tất cả records (không chỉ trang hiện tại)
        $totalQuery = ProductSale::select(
                DB::raw('SUM(product_sales.quantity) as total_quantity'),
                DB::raw('SUM(product_sales.quantity * product_sales.sale_price) as total_revenue')
            )
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_sales.sale_date', [$startDate, $endDate]);
        
        if ($productId) {
            $totalQuery->where('products.id', $productId);
        }
        
        $totals = $totalQuery->first();
        $totalQuantity = $totals->total_quantity ?? 0;
        $totalRevenue = $totals->total_revenue ?? 0;

        return view('product_sales.index', [
            'store' => $store,
            'stores' => $stores,
            'products' => $products,
            'saleData' => $saleData,
            'totalQuantity' => $totalQuantity,
            'totalRevenue' => $totalRevenue,
            'month' => $month,
            'selectedProductId' => $productId,
        ]);
    }

    public function create(Store $store)
    {
        $stores = Store::orderBy('name')->get();
        $products = Product::where('store_id', $store->id)->orderBy('name')->get();

        return view('product_sales.create', [
            'store' => $store,
            'stores' => $stores,
            'products' => $products,
        ]);
    }

    public function store(Request $request, Store $store)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'sale_date' => ['required', 'date'],
        ]);

        $product = Product::where('store_id', $store->id)
            ->where('id', $data['product_id'])
            ->firstOrFail();

        if ($product->quantity < $data['quantity']) {
            return back()->withErrors([
                'quantity' => 'Số lượng tồn kho không đủ. Tồn kho hiện tại: ' . $product->quantity,
            ])->withInput();
        }

        $data['sale_price'] = $product->price;

        ProductSale::create($data);

        $product->decrement('quantity', $data['quantity']);

        return redirect()->route('products.index', $store)
            ->with('status', 'Bán hàng thành công.');
    }
}

