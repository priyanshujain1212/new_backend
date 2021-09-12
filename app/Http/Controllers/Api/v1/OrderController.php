<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderStoreRequest;
use App\Http\Resources\v1\OrderApiResource;
use App\Http\Resources\v1\OrderResource;
use App\Http\Services\FileService;
use App\Http\Services\OrderService;
use App\Http\Services\TransactionService;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\ShopProductVariation;
use App\Notifications\NewShopOrderCreated;
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public $adminBalanceId = 1;
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return OrderResource
     */
    public function index()
    {
        $response = Order::where(['user_id' => auth()->user()->id])->orderBy('id', 'desc')->with('items')->get();
        $response->map(function ($post) {
            $post['status_name']        = trans('order_status.' . $post->status);
            $post['created_at_convert'] = food_date_format($post->created_at);
            $post['updated_at_convert'] = food_date_format($post->updated_at);

            foreach ($post['items'] as $itemKey => $item) {
                $post['items'][$itemKey]['created_at_convert'] = food_date_format($post->created_at);
                $post['items'][$itemKey]['updated_at_convert'] = food_date_format($post->updated_at);
                $post['items'][$itemKey]['product']['image']   = $item['product']->images;
            }
            return $post;
        });

        return new OrderResource($response);
    }

    public function show($id)
    {
        $response = Order::where(['id' => $id, 'user_id' => auth()->user()->id])->latest()->with('items', 'invoice.transactions')->first();
        return new OrderApiResource($response);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = new OrderStoreRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($request->get('image') != '') {
            $validType      = ['jpg', 'png', 'jpeg', 'pdf', 'docx'];
            $fileName       = $request->get('fileName');
            $validExtension = false;
            if (empty($fileName)) {
                $validExtension = true;
            } else {
                $extension = explode('.', $fileName);
                $extension = strtolower(end($extension));

                if (!in_array($extension, $validType)) {
                    $validExtension = true;
                }
            }

            $validator->after(function ($validator) use ($validExtension) {
                if ($validExtension) {
                    $validator->errors()->add('image', 'This image type was invalid.');
                }
            });
        }

        if (!$validator->fails()) {
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
                'shop_id'         => $request->shop_id,
                'user_id'         => auth()->user()->id,
                'total'           => $request->total,
                'delivery_charge' => $request->delivery_charge,
            ]);

            if(($request->paid_amount == '' || $request->paid_amount == 0) || $request->payment_method == PaymentMethod::CASH_ON_DELIVERY) {
                $request->request->add([
                    'paid_amount'           => 0,
                    'payment_method'        => PaymentMethod::CASH_ON_DELIVERY,
                    'payment_status'        => PaymentStatus::UNPAID
                ]);
            } else {
                $request->request->add([
                    'paid_amount'           => $request->paid_amount,
                    'payment_method'        => $request->payment_type,
                    'payment_status'        => PaymentStatus::PAID
                ]);
            }

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
                    'data'    => $this->orderResponse($order),
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

    private function orderResponse($order)
    {
        return ['order_id' => $order->id, 'total_amount' => $order->total ];
    }

    private function createShow($id)
    {
        $response = Order::where(['id' => $id, 'user_id' => auth()->user()->id])->latest()->with('items', 'invoice.transactions')->first();

        $response->setAttribute('status_name', trans('order_status.' . $response->status));
        $response->setAttribute('created_at_convert', $response->created_at->format('d M Y, h:i A'));
        $response->setAttribute('updated_at_convert', $response->updated_at->format('d M Y, h:i A'));
        $response->setAttribute('attachment', $response->image);

        if (isset($response['invoice'])) {
            $response['invoice']['created_at_convert'] = food_date_format($response['invoice']->created_at);
            $response['invoice']['updated_at_convert'] = food_date_format($response['invoice']->updated_at);
        }

        if (isset($response['invoice']) && isset($response['invoice']['transactions'])) {
            foreach ($response['invoice']['transactions'] as $transactionKey => $transaction) {
                $response['invoice']['transactions'][$transactionKey]['created_at_convert'] = food_date_format($transaction->created_at);
                $response['invoice']['transactions'][$transactionKey]['updated_at_convert'] = food_date_format($transaction->updated_at);
            }
        }

        if (isset($response['items'])) {
            foreach ($response['items'] as $itemKey => $item) {
                $response['items'][$itemKey]['created_at_convert'] = food_date_format($item->created_at);
                $response['items'][$itemKey]['updated_at_convert'] = food_date_format($item->updated_at);
                $response['items'][$itemKey]['options']            = json_decode($item->options);
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
        $status = [
            OrderStatus::CANCEL => 'Cancel'
        ];

        if((int) $id) {
            $order = Order::find($id);
            if(!blank($order)) {
                if(isset($status[$request->status])) {
                    $orderService = app(OrderService::class)->orderUpdate($id, $request->status);

                    if($orderService->status) {
                        return response()->json([
                            'status'  => 200,
                            'message' => 'You order update successfully completed.',
                            'data'    => $orderService
                        ], 200);
                    } else {
                        return response()->json([
                            'status'  => 422,
                            'message' => $orderService->message
                        ], 422);
                    }
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => 'The status not found',
                    ], 422);
                }
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => 'The order not found',
                ], 422);
            }
        } else {
            return response()->json([
                'status'  => 422,
                'message' => 'The order id not found',
            ], 422);
        }
    }

    public function orderPayment(Request $request)
    {
        if ( (int)$request->order_id ) {
            $order = Order::find($request->order_id);
            if ( !blank($order) ) {
                if($request->payment_method != PaymentMethod::CASH_ON_DELIVERY && $order->payment_status != PaymentStatus::PAID) {
                    if ( $request->payment_method != PaymentMethod::WALLET) {
                        app(TransactionService::class)->addFund(0, $order->user->balance_id, $order->payment_method, $order->total, $order->id);
                    }
                    if ( $this->adminBalanceId != $order->user->balance_id ) {
                        app(TransactionService::class)->payment($order->user->balance_id, $this->adminBalanceId, $order->total, $order->id);
                    }

                    $order->paid_amount    = $order->total;
                    $order->payment_method = $request->payment_method;
                    $order->payment_status = PaymentStatus::PAID;
                    $order->save();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Payment successfully complete',
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => 'Select your correct payment method',
                    ], 422);
                }
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => 'The order not found',
                ], 422);
            }
        } else {
            return response()->json([
                'status'  => 422,
                'message' => 'The order id not found',
            ], 422);
        }
    }

    public function orderCancel( $id )
    {
        if ( $id ) {
            $order = Order::where([
                'user_id' => auth()->id(),
                'status'  => OrderStatus::PENDING
            ])->find($id);
            if ( !blank($order) ) {
                $orderService = app(OrderService::class)->cancel($id);
                if ( $orderService->status ) {
                    return response()->json([
                        'status'  => 200,
                        'message' => 'You order cancel successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => $orderService->message
                    ], 422);
                }
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => 'The order not found',
                ], 422);
            }
        } else {
            return response()->json([
                'status'  => 422,
                'message' => 'The order id not found',
            ], 422);
        }
    }

    public function attachment($id)
    {
        $order = Order::where(['id' => $id, 'user_id' => auth()->user()->id])->first();
        if (!blank($order)) {
            return response()->json([
                'data'    => $order->image,
                'status'  => 200,
                'message' => 'Success',
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'Bad Request',
        ], 401);
    }
}
