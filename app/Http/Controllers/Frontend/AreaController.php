<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Status;
use App\Http\Controllers\FrontendController;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends FrontendController
{
    /**
     * serarch area by location id
     * @return array
     */
    public function index(Request $request)
    {
        $queryArray['status'] = Status::ACTIVE;
        if ($request->post('id')) {
            $queryArray['location_id'] = $request->post('id');
        }
        $areas = Area::where($queryArray)->get();

        echo '<option value="">' . __('Select area') . '</option>';
        if (!blank($areas)) {
            foreach ($areas as $area) {
                echo '<option value="' . $area->id . '">' . $area->name . '</option>';
            }
        }
    }
}
