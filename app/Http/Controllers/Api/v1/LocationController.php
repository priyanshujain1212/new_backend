<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 16/4/20
 * Time: 8:37 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Enums\AreaStatus;
use App\Enums\LocationStatus;
use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\AreaResource;
use App\Http\Resources\v1\LocationResource;
use App\Http\Resources\v1\ShopResource;
use App\Models\Area;
use App\Models\Location;
use App\Models\Shop;

class LocationController extends Controller
{

    /**
     * @return LocationResource
     */
    public function index()
    {
        $response = Location::where(['status' => LocationStatus::ACTIVE])->get();
        return new LocationResource($response);
    }

    /**
     * @param $id
     *
     * @return AreaResource
     */
    public function area($id)
    {
        $areas = Area::where(['location_id' => $id, 'status' => AreaStatus::ACTIVE])->get();
        $shops = Shop::where(['location_id' => $id, 'status' => ShopStatus::ACTIVE, 'current_status' => ShopStatus::ACTIVE])->get();

        return response()->json([
            'status' => 200,
            'data'   => ['areas' => $areas, 'shops' => ShopResource::collection($shops) ],
        ], 200);
    }
}
