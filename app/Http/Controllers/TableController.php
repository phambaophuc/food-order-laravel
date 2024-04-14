<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Table Controller",
 *     description="Endpoints for managing tables"
 * )
 */
class TableController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/tables",
     *      summary="Get all tables",
     *      tags={"Table Controller"},
     *      @OA\Response(response="200", description="Get all tables.")
     * )
     */
    public function index()
    {
        $tables = Table::all();
        return response()->json(['tables' => $tables], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/tables/{id}",
     *      summary="Get details of a table by ID",
     *      tags={"Table Controller"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the table",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="Table details."),
     *      @OA\Response(response="404", description="Table not found.")
     * )
     */
    public function show($id)
    {
        $table = Table::findOrFail($id);

        if (!$table) {
            return response()->json(['message' => 'Table not found!'], 404);
        }
        return response()->json(['table' => $table], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/tables/{table_id}/orders",
     *      summary="Get orders for a specific table",
     *      tags={"Table Controller"},
     *      @OA\Parameter(
     *          name="table_id",
     *          in="path",
     *          required=true,
     *          description="ID of the table",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response="200", description="List of orders for the table."),
     *      @OA\Response(response="404", description="Table not found.")
     * )
     */
    public function getOrdersForTable($table_id)
    {
        $orders = Order::where('table_id', $table_id)->get();
        return response()->json(['orders' => $orders], 200);
    }
}
