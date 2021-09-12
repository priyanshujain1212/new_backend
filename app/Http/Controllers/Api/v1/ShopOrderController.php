<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShopOrderRequest;
use App\Http\Resources\v1\ShopOrderItemResource;
use App\Http\Resources\v1\ShopOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Http\Services\FileService;
use App\Http\Services\OrderService;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\Shop;
use App\Models\ShopProductVariation;
use App\Notifications\NewShopOrderCreated;
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class ShopOrderController extends Controller
{
    /**
     * ShopOrderController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $shop_id = auth()->user()->shop->id;

        $orders = Order::where(['shop_id' => $shop_id])->orderBy('id', 'desc')->get();

        if (!blank($orders)) {
            return response()->json([
                'status' => 200,
                'data'   => ShopOrderResource::collection($orders),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

    public function show($id)
    {
        $shop_id = auth()->user()->shop->id;
        $order   = Order::where(['shop_id' => $shop_id, 'id' => $id])->first();
        if (!blank($order)) {
            return response()->json([
                'status' => 200,
                'data'   => new ShopOrderItemResource($order),
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'The data not found',
        ], 401);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = new ShopOrderRequest(0);
        $validator = Validator::make($request->all(), $validator->rules());

        if (!$validator->fails()) {

            $phone = $request->phone;
            $user  = User::where('phone', 'like', '%' . $phone . '%')->first();

            if (blank($user)) {
                if ($request->get('email') != '') {
                    $getUser = User::where('email', $request->get('email'))->first();
                    if (!blank($getUser)) {
                        return response()->json([
                            'status'  => 422,
                            'message' => ['email' => ['The Email has already been taken.']],
                        ], 422);
                    }
                }

                $firstName = '';
                $lastName  = '';
                if ($request->has('name')) {
                    $parts     = $this->splitName($request->get('name'));
                    $firstName = $parts[0];
                    $lastName  = $parts[1];
                }

                $user             = new User;
                $user->first_name = $firstName;
                $user->last_name  = $lastName;
                $user->email      = $this->generateEmail($request->get('email'), $firstName);
                $user->phone      = $request->get('phone');
                $user->address    = $request->get('address');
                $user->username   = $this->username($user->email);
                $user->password   = Hash::make(123456);
                $user->save();

                $role = Role::find(2);
                if (!blank($user) && !blank($role)) {
                    $user->assignRole($role->name);
                }
            }

            $orderItems = json_decode($request->items);

            $items = [];
            if (!blank($orderItems)) {

                $i                      = 0;
                $shopProductVariationId = 0;
                $options                = [];
                foreach ($orderItems as $item) {
                    $variation = [];
                    if ((int) $item->shop_product_variation_id) {
                        $shopProductVariationId = $item->shop_product_variation_id;
                        $getVariation           = ShopProductVariation::find($item->shop_product_variation_id);

                        if (!blank($getVariation)) {
                            $variation = ['id' => $getVariation->id, 'name' => $getVariation->name, 'price' => $getVariation->price];
                        }
                    }

                    if (isset($item->options) && !empty($item->options)) {
                        $options = json_decode(json_encode($item->options), true);
                    }

                    $items[$i] = [
                        'shop_id'                   => $request->shop_id,
                        'shop_product_variation_id' => $shopProductVariationId,
                        'product_id'                => $item->product_id,
                        'unit_price'                => (float) $item->unit_price,
                        'quantity'                  => (int) $item->quantity,
                        'discounted_price'          => (float) $item->discounted_price,
                        'variation'                 => $variation,
                        'options'                   => $options,
                    ];
                    $i++;
                }
            }

            $request->request->add([
                'items'           => $items,
                'user_id'         => $user->id,
                'shop_id'         => $request->shop_id,
                'total'           => $request->total,
                'delivery_charge' => $request->delivery_charge,
                'paid_amount'     => 0,
                'payment_method'  => PaymentMethod::CASH_ON_DELIVERY,
                'payment_status'  => PaymentStatus::UNPAID,
            ]);

            $orderService = app(OrderService::class)->order($request);

            if ($orderService->status) {
                $order = Order::find($orderService->order_id);
                app(FileService::class)->orderFileApi($order->id, $request);

                try {
                    $request->user()->notify(new OrderCreated($order));
                    $order->shop->user->notify(new NewShopOrderCreated($order));
                } catch(\Exception $e) {

                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'You order completed successfully.',
                    'data'    => ['order_id' => $order->id, 'total_amount' => $order->total ]
                ], 200);
            } else {
                return response()->json([
                    'status'  => 401,
                    'message' => $orderService->message,
                ], 401);
            }
        } else {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }
    }

    private function createShow($id)
    {
        $response = Order::where(['id' => $id, 'shop_id' => auth()->user()->shop->id])->latest()->with('items')->first();
        $response->setAttribute('status_name', trans('order_status.' . $response->status));
        $response->setAttribute('created_at_convert', $response->created_at->format('d M Y, h:i A'));
        $response->setAttribute('updated_at_convert', $response->updated_at->format('d M Y, h:i A'));

        if (isset($response['items'])) {
            foreach ($response['items'] as $itemKey => $item) {
                $response['items'][$itemKey]['created_at_convert'] = food_date_format($item->created_at);
                $response['items'][$itemKey]['updated_at_convert'] = food_date_format($item->updated_at);
                $response['items'][$itemKey]['product']['image']   = $item['product']->images ?? '';
                unset($response['items'][$itemKey]['product']['media']);
            }
        }
        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->myrole != 3) {
            return response()->json([
                'status'  => 401,
                'message' => 'You don\'t have any permission to update order.',
            ], 401);
        }

        $getOrder = Order::where(['id' => $id, 'shop_id' => auth()->user()->shop->id])->first();
        if (!blank($getOrder)) {
            $orderService = app(OrderService::class)->orderUpdate($id, $request->status);
            if($orderService->status) {
                return response()->json([
                    'status'  => 200,
                    'message' => 'The order successfully updated',
                    'data'    => $orderService,
                ], 200);
            }
            return response()->json([
                'status'  => 401,
                'message' => $orderService->message,
            ], 401);
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

    public function search(Request $request)
    {
        $phone = $request->phone;
        if(!blank($phone)) {
            $user  = User::where('phone', 'like', '%' . $phone . '%')->first();
            if (!blank($user)) {
                return response()->json([
                    'status' => 200,
                    'data'   => new UserResource($user),
                ], 200);
            }
            return response()->json([
                'status'  => 401,
                'message' => 'The user not found',
            ], 401);
        } else {
            return response()->json([
                'status'  => 401,
                'message' => 'Give me a phone number',
            ], 401);
        }
    }

    private function splitName($name)
    {
        $name       = trim($name);
        $last_name  = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
        return [$first_name, $last_name];
    }

    private function generateEmail($email = null, $name = null)
    {
        if ($email != '') {
            $user = User::where('email', $email)->first();
            if (!blank($user)) {
                return trim($name) . mt_rand() . '@' . $_SERVER['SERVER_NAME'];
            }
            return $email;
        }
        return trim($name) . mt_rand() . '@' . $_SERVER['SERVER_NAME'];
    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }
}
