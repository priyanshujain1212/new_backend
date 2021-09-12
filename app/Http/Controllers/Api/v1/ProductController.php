<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProductResource;
use App\Http\Services\RatingsService;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $products = Product::where(['status' => ProductStatus::ACTIVE])->get();
        if (!blank($products)) {
            return response()->json([
                'status' => 200,
                'data'   => new ProductResource($products),
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'The data not found',
        ], 401);
    }

}
