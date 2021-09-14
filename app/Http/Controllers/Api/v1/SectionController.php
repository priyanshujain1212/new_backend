<?php

namespace App\Http\Controllers\Api\v1;


use App\Enums\Status;
use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SectionResource;
use App\Http\Resources\v1\AreaResourceCollection;
use App\Models\Section;
use App\Models\Shop;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    /**
     * @return SectionResource
     */
    public function index( Request $request )
    {

        if($request->has('id')) {
            $response = Shop::where(['section_id' => $request->get('id'), 'status' => ShopStatus::ACTIVE ])->get();
            return new AreaResourceCollection($response);
        } elseif($request->headers->has('X-FOOD-LAT') && $request->headers->has('X-FOOD-LONG')) {
            // $response = Shop::where(['status' => ShopStatus::ACTIVE ])
            //     ->select(DB::raw('*, ( 6367 * acos( cos( radians('.$request->headers->get('X-FOOD-LAT').') ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians('.$request->headers->get('X-FOOD-LONG').') ) + sin( radians('.$request->headers->get('X-FOOD-LAT').') ) * sin( radians( `lat` ) ) ) ) AS distance'))
            //     ->having('distance', '<', setting('geolocation_distance_radius'))
            //     ->orderBy('distance')
            //     ->get();
        } else {
            $section = Section::where([ 'status' => Status::ACTIVE ])->get();
            if (!blank($section)) {
                return response()->json([
                    'status' => 200,
                    'data'   => new SectionResource($section),
                ]);
            }
            return response()->json([
                'status'  => 404,
                'message' => 'The data not found',
            ]);
        }
    }
}
