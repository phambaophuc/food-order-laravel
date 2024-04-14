<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Review Controller",
 *     description="Endpoints for managing reviews"
 * )
 */
class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/reviews",
     *      summary="Get all reviews",
     *      tags={"Review Controller"},
     *      @OA\Response(response="200", description="Get all reviews.")
     * )
     */
    public function index()
    {
        $reviews = Review::all();
        return response()->json(['reviews' => $reviews], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/reviews",
     *      summary="Create a new review",
     *      tags={"Review Controller"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"customer", "rating", "comment"},
     *              @OA\Property(property="customer", type="string"),
     *              @OA\Property(property="rating", type="number", format="int", example="4"),
     *              @OA\Property(property="comment", type="string")
     *          )
     *      ),
     *      @OA\Response(response="201", description="Review created successfully."),
     *      @OA\Response(response="422", description="Validation error.")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer' => 'required|string',
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|string',
        ]);

        $review = Review::create($validatedData);

        return response()->json(['review' => $review], 201);
    }
}
