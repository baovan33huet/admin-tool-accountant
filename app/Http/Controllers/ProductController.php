<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Store $store)
    {
        $products = Product::where('store_id', $store->id)->orderBy('name')->paginate(10);
        $stores = Store::orderBy('name')->get();

        return view('stores.show', [
            'store' => $store,
            'products' => $products,
            'stores' => $stores,
        ]);
    }

    public function create(Store $store)
    {
        $stores = Store::orderBy('name')->get();

        return view('products.create', [
            'store' => $store,
            'stores' => $stores,
        ]);
    }

    public function store(Request $request, Store $store)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $data['store_id'] = $store->id;

        Product::create($data);

        return redirect()->route('products.index', $store)
            ->with('status', 'Thêm sản phẩm thành công.');
    }

    public function edit(Store $store, Product $product)
    {
        $stores = Store::orderBy('name')->get();

        return view('products.edit', [
            'store' => $store,
            'product' => $product,
            'stores' => $stores,
        ]);
    }

    public function update(Request $request, Store $store, Product $product)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'name' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product->update($data);

        return redirect()->route('products.index', $store)
            ->with('status', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Store $store, Product $product)
    {
        $product->delete();

        return redirect()->route('products.index', $store)
            ->with('status', 'Xóa sản phẩm thành công.');
    }

    public function revenue(Store $store, Request $request)
    {
        $stores = Store::orderBy('name')->get();

        $month = $request->get('month', now()->format('Y-m'));
        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $revenueData = ProductSale::select(
                'products.id',
                'products.sku',
                'products.name',
                DB::raw('SUM(product_sales.quantity) as total_quantity'),
                DB::raw('SUM(product_sales.quantity * product_sales.sale_price) as total_revenue')
            )
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_sales.sale_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.sku', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->paginate(10);

        // Tính tổng từ tất cả records (không chỉ trang hiện tại)
        $totals = ProductSale::select(
                DB::raw('SUM(product_sales.quantity) as total_quantity'),
                DB::raw('SUM(product_sales.quantity * product_sales.sale_price) as total_revenue')
            )
            ->join('products', 'product_sales.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_sales.sale_date', [$startDate, $endDate])
            ->first();
        
        $totalRevenue = $totals->total_revenue ?? 0;
        $totalQuantity = $totals->total_quantity ?? 0;

        return view('products.revenue', [
            'store' => $store,
            'stores' => $stores,
            'revenueData' => $revenueData,
            'totalRevenue' => $totalRevenue,
            'totalQuantity' => $totalQuantity,
            'month' => $month,
        ]);
    }
}
