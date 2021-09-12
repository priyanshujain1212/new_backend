<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\FrontendController;
use App\Http\Services\CheckoutService;
use App\Http\Services\FileService;
use App\Http\Services\OrderService;
use App\Http\Controllers\PaytmController;
use App\Http\Services\StripeService;
use App\Libraries\MyString;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\Setting;
use App\Models\Shop;
use App\Notifications\NewShopOrderCreated;
use App\Notifications\OrderCreated;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();

        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        if (blank(Cart::content())) {
            return redirect('/');
        }

        $this->data['shop'] = Shop::find(session('session_cart_shop_id'));
        return view('frontend.checkout', $this->data);
    }

    public function store(Request $request)
    {
        // return $request;
        $payment        = null;
        $sessionShopId = session('session_cart_shop_id');

        if ($sessionShopId > 0) {
            $shop = Shop::find(session('session_cart_shop_id'));

            $validation = [
                'mobile'       => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'address'      => 'required|string',
                'payment_type' => 'required|numeric',
            ];

            if (setting('order_attachment_checking') == 5) {
                $validation['attachment'] = 'required|image|mimes:jpeg,png,jpg|max:5098';
            } else {
                $validation['attachment'] = 'nullable|image|mimes:jpeg,png,jpg|max:5098';
            }

            $validator = Validator::make($request->all(), $validation);
            $validator->after(function ($validator) use ($request, $shop) {
                if($request->payment_type == PaymentMethod::WALLET) {
                    if((float) auth()->user()->balance->balance < (float) (Cart::totalFloat() + $shop->delivery_charge)) {
                        $validator->errors()->add('payment_type', 'The Credit balance does not enough for this payment.');
                    }
                }
            })->validate();

            if ($request->payment_type == PaymentMethod::STRIPE) {
                $stripeService    = new StripeService();
                $stripeParameters = [
                    'amount'      => Cart::totalFloat() + $shop->delivery_charge,
                    'currency'    => 'USD',
                    'token'       => request('stripeToken'),
                    'description' => 'N/A',
                ];

                $payment = $stripeService->payment($stripeParameters);
            }

            if($request->payment_type == PaymentMethod::PAYTM){
                $PaytmController    = new PaytmController();

                $cart_id = Cart::content();
                $amount = Cart::totalFloat() + $shop->delivery_charge;
                
                // return $cart_id;
                  $data_for_request = $PaytmController->handlePaytmRequest($cart_id,$amount );

                $paytm_txn_url = 'https://securegw.paytm.in/theia/processTransaction';
                $paramList = $data_for_request['paramList'];
                $checkSum = $data_for_request['checkSum'];

                return view( 'frontend.paytm-merchant-form', compact( 'paytm_txn_url', 'paramList', 'checkSum' ) );
            }

            if(auth()->check()) {
                $items = [];
                if (!blank(Cart::content())) {
                    $i                      = 0;
                    $shopProductVariationId = 0;
                    $variation              = [];
                    $options                = [];
                    foreach (Cart::content() as $cart) {
                        if(isset($cart->options['variation']) && !empty($cart->options['variation'])) {
                           $shopProductVariationId = $cart->options['variation']['id'];
                           $variation = $cart->options['variation'];
                        }

                        if(isset($cart->options['options']) && !empty($cart->options['options'])) {
                           $options = $cart->options['options'];
                        }

                        $items[$i] = [
                            'shop_id'                   => $cart->options->shop_id,
                            'shop_product_variation_id' => $shopProductVariationId,
                            'product_id'                => $cart->options->product_id,
                            'unit_price'                => (float) $cart->price,
                            'quantity'                  => (int) $cart->qty,
                            'discounted_price'          => (float) $cart->options->discount,
                            'variation'                 => $variation,
                            'options'                   => $options,
                        ];
                        $i++;
                    }
                }

                if ($request->payment_type == PaymentMethod::STRIPE && (is_object($payment) && $payment->isSuccessful())) {
                    $request->request->add([
                        'paid_amount'           => Cart::totalFloat() + $shop->delivery_charge,
                        'payment_method'        => $request->payment_type,
                        'payment_status'        => PaymentStatus::PAID
                    ]);
                } elseif($request->payment_type == PaymentMethod::PAYTM && ($paytm = $PaytmController->paytmCallback->status->isSuccessful())) {
                    $request->request->add([
                        'paid_amount'           => Cart::totalFloat() + $shop->delivery_charge,
                        'payment_method'        => $request->payment_type,
                        'payment_status'        => PaymentStatus::PAID
                    ]);
                }elseif($request->payment_type == PaymentMethod::WALLET) {
                    $request->request->add([
                        'paid_amount'           => Cart::totalFloat() + $shop->delivery_charge,
                        'payment_method'        => $request->payment_type,
                        'payment_status'        => PaymentStatus::PAID
                    ]);
                } else {
                    // $request->request->add([
                    //     'paid_amount'           => 0,
                    //     'payment_method'        => PaymentMethod::CASH_ON_DELIVERY,
                    //     'payment_status'        => PaymentStatus::UNPAID
                    // ]);
                }

                $request->request->add([
                    'items'                 => $items,
                    'shop_id'               => session('session_cart_shop_id'),
                    'user_id'               => auth()->user()->id,
                    'total'                 => Cart::totalFloat() ,
                    'delivery_charge'       => $shop->delivery_charge,
                ]);

                $orderService = app(OrderService::class)->order($request);

                if($orderService->status) {
                    $order = Order::find($orderService->order_id);

                    Cart::destroy();
                    session()->put('session_cart_shop_id', 0);
                    app(FileService::class)->orderFile($order->id, $request);

                    try {
                        $request->user()->notify(new OrderCreated($order));
                        $order->shop->user->notify(new NewShopOrderCreated($order));
                    } catch (\Exception $e) { }

                    return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
                } else {
                    return redirect(route('checkout.index'))->withError($orderService->message);
                }
            } else {
                return redirect()->route('login');
            }
        } else {
            return redirect(route('checkout.index'))->withError('The shop not found');
        }
    }
}
