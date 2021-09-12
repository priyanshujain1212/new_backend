<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        $this->data['shop'] = Shop::find(session('session_cart_shop_id'));
        return view('frontend.cart', $this->data);
    }

    private function cartInfo( $productId, $variationId = null )
    {
        $product = [];
        $carts = Cart::content()->toArray();
        if(is_array($carts)) {
            foreach($carts as $cart) {
                if ( count($cart['options']['variation']) > 0 ) {
                    if ( isset($product[ $cart['options']['product_id'] ]['single']) ) {
                        $product[ $cart['options']['product_id'] ]['single'] += $cart['qty'];
                    } else {
                        $product[ $cart['options']['product_id'] ]['single'] = $cart['qty'];
                    }
                    if ( isset($product[ $cart['options']['product_id'] ]['variation'][ $cart['options']['variation']['id'] ]) ) {
                        $product[ $cart['options']['product_id'] ]['variation'][ $cart['options']['variation']['id'] ] += $cart['qty'];
                    } else {
                        $product[ $cart['options']['product_id'] ]['variation'][ $cart['options']['variation']['id'] ] = $cart['qty'];
                    }
                } else {
                    if ( isset($product[ $cart['options']['product_id'] ]['single']) ) {
                        $product[ $cart['options']['product_id'] ]['single'] += $cart['qty'];
                    } else {
                        $product[ $cart['options']['product_id'] ]['single'] = $cart['qty'];
                    }
                    $product[ $cart['options']['product_id'] ]['variation'] = [];
                }
            }
        }

        if ( $variationId ) {
            $quantity = isset($product[$productId]['variation'][ $variationId ]) ? $product[$productId]['variation'][ $variationId ] : 0;
        } else {
            $quantity = isset($product[$productId]['single']) ? $product[$productId]['single'] : 0;
        }

        return $quantity;
    }

    public function store( Request $request )
    {
        $requestArray = [
            'shop_product_id' => 'required|numeric',
            'variations'      => 'nullable|numeric',
            'options.*'       => 'nullable',
        ];
        $validator    = Validator::make($request->all(), $requestArray);
        if ( !$validator->fails() ) {
            $shop_product_id = $request->shop_product_id;
            $shopProduct     = ShopProduct::findOrfail($shop_product_id);
            if ( !blank($shopProduct) ) {
                if ( session('session_cart_shop_id') != $shopProduct->shop_id ) {
                    Cart::destroy();
                }
                session()->put('session_cart_shop_id', $shopProduct->shop_id);
                $product        = Product::find($shopProduct->product_id);
                $variationArray = [];
                $variationId    = null;
                if ( (int)$request->variations ) {
                    $variations              = ShopProductVariation::find($request->variations);
                    $variationArray['id']    = $variations->id;
                    $variationArray['name']  = $variations->name;
                    $variationArray['price'] = $variations->price - $variations->discount_price;
                    $variationId             = $variations->id;
                    $totalPrice              = $variationArray['price'];
                    $discount                = $variations->discount_price;
                } else {
                    $totalPrice = $shopProduct->unit_price - $shopProduct->discount_price;
                    $discount   = $shopProduct->discount_price;
                }

                if (($this->cartInfo($product->id, $variationId) + 1) > $product->stockCount($shopProduct->shop_id, $variationId)) {
                    return back()->withError('The item is stock out');
                }

                $optionArray = [];
                if ( !blank($request->options) ) {
                    $options = ShopProductOption::whereIn('id', $request->options)->get();
                    $i       = 0;
                    foreach ( $options as $option ) {
                        $optionArray[ $i ]['id']    = $option->id;
                        $optionArray[ $i ]['name']  = $option->name;
                        $optionArray[ $i ]['price'] = $option->price;
                        $i++;
                        $totalPrice += $option->price;
                    }
                }
                $cartItem = [
                    'id'      => $shop_product_id,
                    'name'    => $product->name,
                    'qty'     => 1,
                    'price'   => $totalPrice,
                    'weight'  => 0,
                    'options' => [
                        'options'    => $optionArray,
                        'variation'  => $variationArray,
                        'discount'   => $discount,
                        'shop_id'    => $shopProduct->shop_id,
                        'images'     => $product->images,
                        'product_id' => $shopProduct->product_id
                    ]
                ];
                Cart::add($cartItem);
            }
        }
        return back();
    }

    public function remove($id)
    {
        Cart::remove($id);
        if(blank(Cart::content())) {
            session()->put('session_cart_shop_id', 0);
        }
        return back()->withSuccess("The Cart Item Remove Successfully");
    }

    public function quantity( Request $request )
    {
        $validation = [
            'rowId'          => 'required',
            'quantity'       => 'required|numeric',
            'deliveryCharge' => 'required',
        ];
        $validator = Validator::make($request->all(), $validation);
        if ( !$validator->fails() ) {
            $carts = Cart::content()->toArray();
            if ( isset($carts[ $request->rowId ]) ) {
                $productId   = $carts[ $request->rowId ]['options']['product_id'];
                $variationId = (isset($carts[ $request->rowId ]['options']['variation']['id']) ? $carts[ $request->rowId ]['options']['variation']['id'] : null);
                $shopId      = $carts[ $request->rowId ]['options']['shop_id'];
                $cartQuantity =  $carts[ $request->rowId ]['qty'];
                $product     = Product::find($productId);
                if ( !blank($product) ) {
                    if ( (($this->cartInfo($productId, $variationId) - $cartQuantity) + $request->quantity) > $product->stockCount($shopId, $variationId) ) {
                        echo json_encode([
                            'status'  => false,
                            'message' => 'The item is stock out'
                        ]);
                    } else {
                        Cart::update($request->rowId, $request->quantity);
                        echo json_encode([
                            'status'     => true,
                            'price'      => currencyFormat(Cart::get($request->rowId)->price * Cart::get($request->rowId)->qty),
                            'totalPrice' => currencyFormat(Cart::totalFloat()),
                            'total'      => currencyFormat(Cart::totalFloat() + $request->deliveryCharge)
                        ]);
                    }
                }
            } else {
                echo json_encode([
                    'status'  => false,
                    'message' => 'cart not found.'
                ]);
            }
        } else {
            echo json_encode([
                'status'  => false,
                'message' => 'something wrong'
            ]);
        }
    }
}
