<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ShopProductCrudResource;
use App\Http\Resources\v1\ShopProductResource;
use App\Http\Services\RatingsService;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopProductController extends Controller
{
    /**
     * @param Shop $shopID
     *
     * @return ShopProductResource
     */

    public function __construct()
    {
        $this->middleware('auth:api')->except('action');
    }

    public function action($shopID)
    {
        $shop = Shop::find($shopID);
        if (blank($shop)) {
            return response()->json([
                'status'  => 401,
                'message' => 'The shops product not found',
            ], 401);
        }

        $shop->load(['products']);
        $shopProduct = pluck(ShopProduct::where([ 'shop_id' => $shop->id ])->get(), 'obj', 'product_id');
        $shop->products->map(function( $query ) use ( $shopProduct, $shop ) {
            $query['unit_price']     = isset($shopProduct[ $query->id ]) ? $shopProduct[ $query->id ]->unit_price : 0;
            $query['discount_price'] = isset($shopProduct[ $query->id ]) ? $shopProduct[ $query->id ]->discount_price : 0;
            $query['stock_count']    = $query->stockCount($shop->id);
            $query['in_stock']       = $query->inStock($shop->id);

            if(isset($shopProduct[ $query->id ])) {
                $rating = app(RatingsService::class)->avgRating($shopProduct[$query->id ]->shop_id, $shopProduct[$query->id ]->product_id);
                $query['avgRating']     = isset($rating['avgRating']) ? $rating['avgRating'] : 0;
            } else {
                $query['avgRating']     = 0;
            }

        });
        return new ShopProductResource($shop);
    }

    public function store(Request $request, $shop_id)
    {
        $requestArray = [
            'product_type' => 'required|numeric',
            'product_id'   => 'required|numeric',
        ];

        if ($request->product_type == ProductType::SINGLE) {
            $requestArray['unit_price']     = 'required|numeric';
            $requestArray['quantity']       = 'required|numeric';
            $requestArray['discount_price'] = 'nullable|numeric';
        } else if ($request->product_type == ProductType::VARIATION) {
            $requestArray['variations'] = 'json';
        }

        if ($request->product_type != '') {
            $requestArray['options'] = 'json';
        }

        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->first();
        $validator->after(function ($validator) use ($request, $getShopProduct) {
            $variations = json_decode($request->variations, true);
            if ($request->product_type == ProductType::VARIATION && blank($variations)) {
                $validator->errors()->add('variations', 'This field is required');
            }

            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }

            if (($request->product_type == ProductType::SINGLE) && ($request->discount_price > $request->unit_price)) {
                $validator->errors()->add('discount_price', 'This discount price can\'t be greater than unit price.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        if ($request->product_type == ProductType::SINGLE) {
            $shopProduct                 = new ShopProduct;
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = $request->get('unit_price');
            $shopProduct->quantity       = $request->get('quantity');
            $shopProduct->creator_type   = User::class;
            $shopProduct->editor_type    = User::class;
            $shopProduct->creator_id     = auth()->user()->id;
            $shopProduct->editor_id      = auth()->user()->id;

            if($request->get('discount_price') == '' || $request->get('discount_price') == null) {
                $shopProduct->discount_price = 0;
            } else {
                $shopProduct->discount_price = $request->get('discount_price');
            }

            $shopProduct->save();
        } else if ($request->product_type == ProductType::VARIATION) {
            $shopProduct                 = new ShopProduct;
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = 0;
            $shopProduct->quantity       = 0;
            $shopProduct->discount_price = 0;
            $shopProduct->creator_type   = User::class;
            $shopProduct->editor_type    = User::class;
            $shopProduct->creator_id     = auth()->user()->id;
            $shopProduct->editor_id      = auth()->user()->id;
            $shopProduct->save();

            if (!blank($request->variations)) {
                $variations = json_decode($request->variations, true);

                $key = array_key_first($variations);

                $smallPrice         = isset($variations[$key]) ? $variations[$key]['price'] : 0;
                $smallQuantity      = isset($variations[$key]) ? $variations[$key]['quantity'] : 0;
                $smallDiscountPrice = isset($variations[$key]) ? $variations[$key]['discount_price'] : 0;

                $i                         = 0;
                $shopProductVariationArray = [];
                foreach ($variations as $variation) {
                    if ( $variation['price'] < $smallPrice ) {
                        $smallPrice         = $variation['price'];
                        $smallQuantity      = $variation['quantity'];

                        if($variation['discount_price'] == '' || $variation['discount_price'] == null) {
                            $smallDiscountPrice = 0;
                        } else {
                            $smallDiscountPrice = $variation['discount_price'];
                        }

                    }

                    $shopProductVariationArray[$i]['shop_product_id'] = $shopProduct->id;
                    $shopProductVariationArray[$i]['product_id']      = $request->product_id;
                    $shopProductVariationArray[$i]['shop_id']         = $shop_id;
                    $shopProductVariationArray[$i]['name']            = $variation['name'];
                    $shopProductVariationArray[$i]['price']           = $variation['price'];
                    $shopProductVariationArray[$i]['quantity']        = $variation['quantity'];


                    if($variation['discount_price'] == '' || $variation['discount_price'] == null) {
                        $shopProductVariationArray[$i]['discount_price']  = 0;
                    } else {
                        $shopProductVariationArray[$i]['discount_price']  = $variation['discount_price'];
                    }
                    $i++;
                }
                ShopProductVariation::insert($shopProductVariationArray);

                $shopProduct->unit_price     = $smallPrice;
                $shopProduct->quantity       = $smallQuantity;
                $shopProduct->discount_price = $smallDiscountPrice;
                $shopProduct->save();
            }
        }

        if (!blank($shopProduct) && !blank($request->options)) {
            $i       = 0;
            $options = json_decode($request->options);

            $shopProductOptionArray = [];
            foreach ($options as $option) {
                if ($option->name == '' || $option->price == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option->name;
                $shopProductOptionArray[$i]['price']           = $option->price;
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'The Shop product Successfully Created',
        ], 200);
    }

    public function update(Request $request, $shop_id, $id)
    {
        $shopProduct = ShopProduct::find($id);

        if (blank($shopProduct)) {
            return response()->json([
                'status'  => 404,
                'message' => 'The shop products not found',
            ]);
        }

        $requestArray = [
            'product_type' => 'required|numeric',
            'product_id'   => 'required|numeric',
        ];

        if ($request->product_type == ProductType::SINGLE) {
            $requestArray['unit_price']     = 'required|numeric';
            $requestArray['quantity']       = 'required|numeric';
            $requestArray['discount_price'] = 'required|numeric';
        } else if ($request->product_type == ProductType::VARIATION) {
            $requestArray['variations'] = 'json';
        }

        if ($request->product_type != '') {
            $requestArray['options'] = 'json';
        }
        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->where('id', '!=', $id)->first();
        $validator->after(function ($validator) use ($request, $getShopProduct) {
            $variations = json_decode($request->variations, true);
            if ($request->product_type == ProductType::VARIATION && blank($variations)) {
                $validator->errors()->add('variations', 'This field is required');
            }

            if (($request->product_type == ProductType::SINGLE) && ($request->discount_price > $request->unit_price)) {
                $validator->errors()->add('discount_price', 'This dicount price cann\'t be greater than unit price.');
            }

            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }

        ShopProductOption::where('shop_product_id', $shopProduct->id)->delete();

        if ($request->product_type == ProductType::SINGLE) {
            ShopProductVariation::where('shop_product_id', $shopProduct->id)->delete();

            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = $request->get('unit_price');
            $shopProduct->quantity       = $request->get('quantity');
            $shopProduct->editor_type    = User::class;
            $shopProduct->editor_id      = auth()->user()->id;

            if($request->get('discount_price') == '' || $request->get('discount_price') == null) {
                $shopProduct->discount_price = 0;
            } else {
                $shopProduct->discount_price = $request->get('discount_price');
            }

            $shopProduct->save();
        } else if ($request->product_type == ProductType::VARIATION) {
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = 0;
            $shopProduct->quantity       = 0;
            $shopProduct->discount_price = 0;
            $shopProduct->editor_type    = User::class;
            $shopProduct->editor_id      = auth()->user()->id;
            $shopProduct->save();

            if (!blank($request->variations)) {
                $variations           = json_decode($request->variations, true);
                $key                  = array_key_first($variations);
                $smallPrice           = isset($variations[ $key ]) ? $variations[ $key ]['price'] : 0;
                $smallQuantity        = isset($variations[ $key ]) ? $variations[ $key ]['quantity'] : 0;
                $smallDiscountPrice   = isset($variations[ $key ]) ? $variations[ $key ]['discount_price'] : 0;
                $shopProductVariation = ShopProductVariation::where([
                    'shop_id'    => $shop_id,
                    'product_id' => $shopProduct->product_id
                ])->get()->pluck('id', 'id')->toArray();
                $variationArray       = [];
                foreach ($variations as $variation) {
                    $key = $variation['id'];
                    $variationArray[ $key ] = $key;
                    if ( $variation['price'] < $smallPrice ) {
                        $smallPrice         = $variation['price'];
                        $smallQuantity      = $variation['quantity'];

                        if($variation['discount_price'] == '' || $variation['discount_price'] == null) {
                            $smallDiscountPrice = 0;
                        } else {
                            $smallDiscountPrice = $variation['discount_price'];
                        }
                    }

                    if (isset($shopProductVariation[$key])) {
                        $shopProductVariationItem = ShopProductVariation::where(['shop_id' => $shop_id, 'product_id' => $shopProduct->product_id, 'id' => $key])->first();

                        $shopProductVariationItem->shop_product_id = $shopProduct->id;
                        $shopProductVariationItem->product_id      = $request->product_id;
                        $shopProductVariationItem->shop_id         = $shop_id;
                        $shopProductVariationItem->name            = $variation['name'];
                        $shopProductVariationItem->price           = $variation['price'];
                        $shopProductVariationItem->quantity        = $variation['quantity'];

                        if($variation['discount_price'] == '' || $variation['discount_price'] == null) {
                            $shopProductVariationItem->discount_price  = 0;
                        } else {
                            $shopProductVariationItem->discount_price  = $variation['discount_price'];
                        }

                        $shopProductVariationItem->save();
                    } else {
                        $shopProductVariationArray['shop_product_id'] = $shopProduct->id;
                        $shopProductVariationArray['product_id']      = $request->product_id;
                        $shopProductVariationArray['shop_id']         = $shop_id;
                        $shopProductVariationArray['name']            = $variation['name'];
                        $shopProductVariationArray['price']           = $variation['price'];
                        $shopProductVariationArray['quantity']        = $variation['quantity'];

                        if($variation['discount_price'] == '' || $variation['discount_price'] == null) {
                            $shopProductVariationArray['discount_price']  = 0;
                        } else {
                            $shopProductVariationArray['discount_price']  = $variation['discount_price'];
                        }

                        ShopProductVariation::insert($shopProductVariationArray);
                    }
                }

                $shopProduct->unit_price     = $smallPrice;
                $shopProduct->quantity       = $smallQuantity;
                $shopProduct->discount_price = $smallDiscountPrice;
                $shopProduct->save();

                $deleteArray = array_diff($shopProductVariation, $variationArray);
                if (!blank($deleteArray)) {
                    ShopProductVariation::whereIn('id', $deleteArray)->delete();
                }
            }
        }

        if (!blank($shopProduct) && !blank($request->options)) {
            $i       = 0;
            $options = json_decode($request->options);

            $shopProductOptionArray = [];
            foreach ($options as $option) {
                if ($option->name == '' || $option->price == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option->name;
                $shopProductOptionArray[$i]['price']           = $option->price;
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'The Shop product Successfully Updated',
        ], 200);
    }

    public function product($shop_id)
    {
        $shopProducts = ShopProduct::with('shop')->where(['shop_id' => $shop_id])->orderBy('id', 'desc')->get();
        $shopProducts->map(function( $query ) use ( $shop_id, $shopProducts ) {
            $rating               = app(RatingsService::class)->avgRating($shop_id, $query->product_id);
            $query['stock_count'] = $query->product->stockCount($shop_id);
            $query['in_stock']    = $query->product->inStock($shop_id);
            $query['avgRating']   = isset($rating['avgRating']) ? $rating['avgRating'] : 0;
            return $query;
        });

        $shopProducts->load(['product_variations', 'product_options']);

        if (!blank($shopProducts)) {
            return response()->json([
                'status' => 200,
                'data'   => ShopProductCrudResource::collection($shopProducts),
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'The shop products not found',
        ], 401);
    }

    public function show($shop_id, $id)
    {

        $shopProduct = ShopProduct::where(['shop_id' => $shop_id, 'id' => $id])->first();
        if (!blank($shopProduct)) {
            $rating = app(RatingsService::class)->avgRating($shop_id, $shopProduct->product_id);
            $shopProduct['stock_count'] = $shopProduct->product->stockCount($shop_id);
            $shopProduct['in_stock']    = $shopProduct->product->inStock($shop_id);
            $shopProduct['avgRating']   = isset($rating['avgRating']) ? $rating['avgRating'] : 0;
            $shopProduct->load(['product_variations', 'product_options']);

            return response()->json([
                'status' => 200,
                'data'   => new ShopProductCrudResource($shopProduct),
            ], 200);
        }

        return response()->json([
            'status'  => 401,
            'message' => 'The shop product not found',
        ], 401);
    }

    public function delete($shop_id, $id)
    {
        $shopProduct = ShopProduct::where(['shop_id' => $shop_id, 'id' => $id])->first();
        if (!blank($shopProduct)) {
            $shopProduct->delete();
            ShopProductVariation::where('shop_product_id', $shopProduct->id)->delete();
            ShopProductOption::where('shop_product_id', $shopProduct->id)->delete();
            return response()->json([
                'status'  => 200,
                'message' => 'The shop product deleted successfully',
            ], 200);
        }
        return response()->json([
            'status'  => 401,
            'message' => 'The shop product not found',
        ], 401);
    }

}
