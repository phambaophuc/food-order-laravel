<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderUpdate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Order Controller",
 *     description="Endpoints for managing orders"
 * )
 */
class OrderController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/orders",
     *      summary="Get all orders",
     *      tags={"Order Controller"},
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
     *      tags={"Order Controller"},
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
     *      tags={"Order Controller"},
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

            event(new OrderCreated($order));
            return response()->json(['order' => $order->load('products')], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->__toString()], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/orders/{orderId}/change-status",
     *      summary="Update status of an order",
     *      tags={"Order Controller"},
     *      @OA\Parameter(
     *          name="orderId",
     *          in="path",
     *          required=true,
     *          description="ID of the order",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="string", enum={"pending", "preparing", "completed", "cancelled"})
     *          )
     *      ),
     *      @OA\Response(response="200", description="Order status updated successfully."),
     *      @OA\Response(response="404", description="Order not found.")
     * )
     */
    public function updateStatus(Request $request, $orderId)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string|in:pending,preparing,completed,cancelled',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'The status must be one of: pending, preparing, completed, cancelled'], 422);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        $order->status = $validatedData['status'];
        $order->save();

        event(new OrderUpdate($order));
        return response()->json(['message' => 'Order status updated successfully', 'order' => $order], 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/orders/{id}",
     *      summary="Delete an order by ID",
     *      tags={"Order Controller"},
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
