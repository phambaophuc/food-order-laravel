<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }

    public function findProductsByCategory($category)
    {
        $category = Category::where('name', $category)->firstOrFail();
        $products = $category->products;
        return response()->json(['products' => $products], 200);
    }
}
