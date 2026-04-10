<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);

        $products = Cache::tags(['products'])->remember("products:list:page:{$page}", 300, function () {
            return Product::with('category')->paginate(15);
        });

        return ProductResource::collection($products);
    }

    public function salesReport()
    {
        $orders = Order::with([ 'customer', 'items.product' ])->paginate(15);

        return response()->json($orders);
    }

    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        $categories    = Category::all();

        $topProducts = Product::orderByDesc('sold_count') ->limit(5)->get();

        return response()->json([
            'total_products' => $totalProducts,
            'total_orders'   => $totalOrders,
            'total_revenue'  => $totalRevenue,
            'categories'     => $categories,
            'top_products'   => $topProducts,
        ]);
    }

    public function search(Request $request)
    {
        $keyword  = $request->input('q');
        $products = Product::where('name', 'LIKE', '%' . $keyword . '%')
                           ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                           ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($request->all());

        Cache::tags(['products'])->flush();

        return response()->json($product, 201);
    }
}
