<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::all();
        return response()->json(['orders' => $orders], 200);
    }

    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found!'], 404);
        }
        return response()->json(['order' => $order], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string',
            'total_price' => 'required|numeric',
            'status' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'customer_name' => $validatedData['customer_name'],
            'total_price' => $validatedData['total_price'],
            'status' => $validatedData['status'],
        ]);

        foreach ($validatedData['products'] as $productData) {
            $product = Product::find($productData['id']);
            $order->products()->attach($product->id, ['quantity' => $productData['quantity']]);
        }

        return response()->json(['order' => $order->load('products')], 201);
    }
}
