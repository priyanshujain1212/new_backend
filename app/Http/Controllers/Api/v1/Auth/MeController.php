<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Enums\OrderStatus;
use App\Enums\RatingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PasswordUpdateRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Resources\v1\MeResource;
use App\Models\ShopProductRating;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->except('refresh');
    }

    public function action(Request $request)
    {
        return new MeResource($request->user());
    }

    public function refresh()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json([
                'status'  => 401,
                'message' => 'Token not provided',
            ], 401);
        }

        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status'  => 401,
                'message' => $e->getMessage(),
            ], 401);
        }

        return response()->json([
            'success'    => true,
            'token'      => $token,
            "token_type" => "bearer",
            'expires_in' => config('jwt.ttl') * 3600000000000,
        ], 200);

    }

    public function update(Request $request)
    {
        $profile = auth()->user();
        if (blank($profile)) {
            return response()->json([
                'status'  => 401,
                'message' => 'You try to using invalid username or password',
            ], 401);
        }

        $validator = new ProfileUpdateRequest($profile->id);
        $validator = Validator::make($request->all(), $validator->rules());
        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $firstName = '';
        $lastName  = '';
        if ($request->has('name')) {
            $parts     = $this->splitName($request->get('name'));
            $firstName = $parts[0];
            $lastName  = $parts[1];
        }

        $profile->first_name = $firstName;
        $profile->last_name  = $lastName;
        $profile->email      = $request->get('email');
        $profile->phone      = $request->get('phone');
        $profile->address    = $request->get('address');
        if ($request->username) {
            $profile->username = $request->username;
        }
        $profile->save();

        if ($request->get('image') != '') {
            $realImage = base64_decode($request->get('image'));
            file_put_contents($request->get('fileName'), $realImage);

            $url = public_path($request->get('fileName'));

            $profile->media()->delete();
            $profile->addMediaFromUrl($url)->toMediaCollection('user');

            File::delete($url);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Successfully Updated Profile',
        ], 200);
    }

    private function splitName($name)
    {
        $name       = trim($name);
        $last_name  = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
        return [$first_name, $last_name];
    }

    public function changePassword(Request $request)
    {
        $validator = new PasswordUpdateRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $profile           = auth()->user();
        $profile->password = bcrypt($request->get('password'));
        $profile->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Successfully Updated Password',
        ], 200);
    }

    public function device(Request $request)
    {
        $validator = Validator::make($request->all(), ['device_token' => 'required']);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $user               = auth()->user();
        $user->device_token = $request->device_token;
        $user->save();

        return response()->json([
            'status'  => 200,
            'message' => 'Successfully device updated',
        ], 200);
    }

    public function review()
    {
        $orderProducts = auth()->user()->orders()->latest()->where('status', OrderStatus::COMPLETED)->get();

        if (blank($orderProducts)) {
            return response()->json([
                'status'  => 401,
                'message' => 'Order not found.',
            ], 401);
        }

        $itemArray      = [];
        $itemCheckArray = [];
        $i              = 0;
        foreach ($orderProducts as $orderProduct) {
            foreach ($orderProduct->items as $orderLineItem) {
                if (isset($itemCheckArray[$orderLineItem->shop_id][$orderLineItem->product_id])) {
                    continue;
                }
                $itemCheckArray[$orderLineItem->shop_id][$orderLineItem->product_id] = true;

                $itemArray[$i]['product_image'] = $orderLineItem->product->images;
                $itemArray[$i]['product_id']    = $orderLineItem->product_id;
                $itemArray[$i]['product_name']  = $orderLineItem->product->name;
                $itemArray[$i]['shop_id']       = $orderLineItem->shop_id;
                $itemArray[$i]['shop_name']     = $orderLineItem->shop->name;

                $i++;
            }
        }

        return response()->json([
            'status' => 200,
            'data'   => $itemArray,
        ], 200);
    }

    public function saveReview(Request $request)
    {
        $validator = Validator::make($request->all(), $this->reviewValidateArray());
        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        $shopProductRating = ShopProductRating::where(['user_id' => auth()->id(), 'product_id' => $request->product_id, 'shop_id' => $request->shop_id])->first();

        if ($shopProductRating) {
            $shopProductRating->rating = $request->rating;
            $shopProductRating->review = $request->review;
            $shopProductRating->status = RatingStatus::ACTIVE;
            $shopProductRating->save();
        } else {
            $shopProductRating             = new ShopProductRating;
            $shopProductRating->user_id    = auth()->id();
            $shopProductRating->product_id = $request->product_id;
            $shopProductRating->shop_id    = $request->shop_id;
            $shopProductRating->rating     = $request->rating;
            $shopProductRating->review     = $request->review;
            $shopProductRating->status     = RatingStatus::ACTIVE;
            $shopProductRating->save();
        }

        return response()->json([
            'status'  => 200,
            'message' => 'You rating successfully saved.',
        ], 200);
    }

    public function reviewValidateArray()
    {
        return [
            'rating'     => 'required|numeric|min:1|max:5',
            'review'     => 'required|string|max:500',
            'product_id' => 'required|numeric',
            'shop_id'    => 'required|numeric',
        ];
    }

}
