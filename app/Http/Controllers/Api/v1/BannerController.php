<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\BannerStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\BannerResource;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', BannerStatus::ACTIVE)->orderBy('sort', 'desc')->get();
        if (!blank($banners)) {
            return response()->json([
                'status' => 200,
                'data'   => BannerResource::collection($banners),
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'The data not found',
        ], 401);
    }

}
