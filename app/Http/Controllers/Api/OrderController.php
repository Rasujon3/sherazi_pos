<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items'       => 'required|array|min:1',
        ]);

        $order = DB::transaction(function () use ($request) {
            $totalAmount = 0;

            $order = Order::create([
                'customer_id'  => $request->customer_id,
                'total_amount' => 0,
                'status'       => 'pending',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product || $product->stock < $item['quantity']) {
                    return response()->json(['error' => 'Product unavailable'], 422);
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $product->decrement('stock', $item['quantity']);
                $totalAmount += $product->price * $item['quantity'];
            }

             $order->update(['total_amount' => $totalAmount]);
             return $order;
        });

        return response()->json($order, 201);
    }

    public function index()
    {
        $orders = Order::with( 'customer', 'items' )->paginate(15);

        return response()->json($orders);
    }

    public function filterByStatus(Request $request)
    {
        $status = $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ])['status'];

        $orders = Order::with('customer')
            ->where('status', $status)
            ->paginate(15);

        return response()->json($orders);
    }
}
