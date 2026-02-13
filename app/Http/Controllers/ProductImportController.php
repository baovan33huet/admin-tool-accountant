<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImport;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductImportController extends Controller
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

        $query = ProductImport::select(
                'products.id',
                'products.sku',
                'products.name',
                DB::raw('SUM(product_imports.quantity) as total_quantity'),
                DB::raw('SUM(product_imports.quantity * product_imports.import_price) as total_cost')
            )
            ->join('products', 'product_imports.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_imports.import_date', [$startDate, $endDate]);

        // Filter theo sản phẩm nếu có
        if ($productId) {
            $query->where('products.id', $productId);
        }

        $importData = $query->groupBy('products.id', 'products.sku', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->paginate(10);

        // Tính tổng từ tất cả records (không chỉ trang hiện tại)
        $totalQuery = ProductImport::select(
                DB::raw('SUM(product_imports.quantity) as total_quantity'),
                DB::raw('SUM(product_imports.quantity * product_imports.import_price) as total_cost')
            )
            ->join('products', 'product_imports.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->whereBetween('product_imports.import_date', [$startDate, $endDate]);
        
        if ($productId) {
            $totalQuery->where('products.id', $productId);
        }
        
        $totals = $totalQuery->first();
        $totalQuantity = $totals->total_quantity ?? 0;
        $totalCost = $totals->total_cost ?? 0;

        return view('product_imports.index', [
            'store' => $store,
            'stores' => $stores,
            'products' => $products,
            'importData' => $importData,
            'totalQuantity' => $totalQuantity,
            'totalCost' => $totalCost,
            'month' => $month,
            'selectedProductId' => $productId,
        ]);
    }

    public function create(Store $store)
    {
        $stores = Store::orderBy('name')->get();
        $products = Product::where('store_id', $store->id)->orderBy('name')->get();

        return view('product_imports.create', [
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
            'import_date' => ['required', 'date'],
        ]);

        $product = Product::where('store_id', $store->id)
            ->where('id', $data['product_id'])
            ->firstOrFail();

        $data['import_price'] = $product->base_price;

        ProductImport::create($data);

        $product->increment('quantity', $data['quantity']);

        return redirect()->route('products.index', $store)
            ->with('status', 'Nhập hàng thành công.');
    }
}
