<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 20/4/20
 * Time: 2:41 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings     = Setting::whereNotIn('key', $this->removeSettingItem())->get()->pluck('value', 'key');

        return response()->json([
            'status' => 200,
            'data'   => $settings,
        ], 200);
    }

    public function removeSettingItem()
    {
        return [
            'purchase_code',
            'purchase_username',
        ];
    }
}
