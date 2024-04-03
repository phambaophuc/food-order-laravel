<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Endpoints for managing categories"
 * )
 */
class CategoryController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/categories",
     *      summary="Get all categories",
     *      tags={"Categories"},
     *      @OA\Response(response="200", description="Get all categories.")
     * )
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/categories/{category}/products",
     *      summary="Find products by category",
     *      tags={"Categories"},
     *      @OA\Parameter(
     *          name="category",
     *          in="path",
     *          required=true,
     *          description="Name of the category",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(response="200", description="List of products in the category."),
     *      @OA\Response(response="404", description="Category not found.")
     * )
     */
    public function findProductsByCategory($category)
    {
        $category = Category::where('name', $category)->firstOrFail();
        $products = $category->products;
        return response()->json(['products' => $products], 200);
    }
}
