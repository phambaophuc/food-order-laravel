<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Endpoints for managing orders"
 * )
 */
class OrderController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/orders",
     *      summary="Get all orders",
     *      tags={"Orders"},
     *      @OA\Response(response="200", description="Get all orders.")
     * )
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json(['orders' => $orders], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/{id}",
     *      summary="Get an order by ID",
     *      tags={"Orders"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the order",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Order details."),
     *      @OA\Response(response="404", description="Order not found.")
     * )
     */
    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found!'], 404);
        }
        return response()->json(['order' => $order], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders",
     *      summary="Create a new order",
     *      tags={"Orders"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"customer_name", "total_price", "status", "products"},
     *              @OA\Property(property="customer_name", type="string"),
     *              @OA\Property(property="table_id", type="integer"),
     *              @OA\Property(property="products", type="array",
     *                  @OA\Items(
     *                      required={"id", "quantity"},
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="quantity", type="integer")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response="201", description="Order created successfully."),
     *      @OA\Response(response="422", description="Validation error.")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'customer_name' => 'required|string',
                'table_id' => 'required|integer',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $totalPrice = 0;
            foreach ($validatedData['products'] as $productData) {
                $product = Product::find($productData['id']);
                $totalPrice += $product->price * $productData['quantity'];
            }

            $order = Order::create([
                'customer_name' => $validatedData['customer_name'],
                'total_price' => $totalPrice,
                'table_id' => $validatedData['table_id'],
            ]);

            foreach ($validatedData['products'] as $productData) {
                $product = Product::find($productData['id']);
                $order->products()->attach($product->id, ['quantity' => $productData['quantity']]);
            }
            return response()->json(['order' => $order->load('products')], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->__toString()], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/orders/{id}",
     *      summary="Delete an order by ID",
     *      tags={"Orders"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the order",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Order deleted successfully."),
     *      @OA\Response(response="404", description="Order not found.")
     * )
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found!'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully!'], 200);
    }
}
