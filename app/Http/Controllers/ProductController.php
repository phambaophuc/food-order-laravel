<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Product Controller",
 *     description="Endpoints for managing products"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/products",
     *      summary="Get all products",
     *      tags={"Product Controller"},
     *      @OA\Response(response="200", description="Get all products.")
     * )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json(['products' => $products], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products/{id}",
     *      summary="Get a product by ID",
     *      tags={"Product Controller"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the product",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Product details."),
     *      @OA\Response(response="404", description="Product not found.")
     * )
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found!'], 404);
        }
        return response()->json(['product' => $product], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/products/search",
     *      summary="Search products by name",
     *      tags={"Product Controller"},
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="Keyword to search for",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response="200", description="List of products matching the search.")
     * )
     */
    public function search(Request $request)
    {
        $keyword = $request->input('name');
        $products = Product::where('name', 'like', "%{$keyword}%")->get();
        return response()->json(['products' => $products], 200);
    }
}
