<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\BestSellingCategoryResource;
use App\Http\Resources\v1\BestSellingProductResource;
use App\Http\Resources\v1\SearchShopProductResource;
use App\Http\Resources\v1\SearchShopResource;
use App\Http\Services\RatingsService;
use App\Models\BestSellingCategory;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{

    public function shops($shop)
    {
        $shops = Shop::where(['status' => ShopStatus::ACTIVE])->where('name', 'like', '%' . $shop . '%')->get();

        return response()->json([
            'status' => 200,
            'data'   => SearchShopResource::collection($shops),
        ], 200);
    }

    public function shopProducts($shopID, $product)
    {
        $shopProduct  = pluck(ShopProduct::where(['shop_id' => $shopID])->get(), 'obj', 'product_id');
        $shopProducts = Shop::find($shopID)->products()->where(function (Builder $query) use ($product) {
            $query->where('products.name', 'like', '%' . $product . '%');
        })->get();

        $shopProducts->map(function ($query) use ($shopProduct, $shopID) {
            $rating = app(RatingsService::class)->avgRating($shopID, $query->id);
            $query['unit_price']  = isset($shopProduct[$query->id]) ? $shopProduct[$query->id]->unit_price : 0;
            $query['discount_price']  = isset($shopProduct[$query->id]) ? $shopProduct[$query->id]->discount_price : 0;
            $query['stock_count'] = $query->stockCount($shopID);
            $query['in_stock']    = $query->inStock($shopID);
            $query['avgRating']   = $rating['avgRating'];
            return $query;
        });

        return response()->json([
            'status' => 200,
            'data'   => SearchShopProductResource::collection($shopProducts),
        ], 200);
    }

    public function bestSellingProducts()
    {
        $bestSellingProducts = ShopProduct::where('counter', '!=', 0)->orderBy('counter', 'desc')->get()->take(8);
        $bestSellingProducts->map(function ($query) {
            $rating = app(RatingsService::class)->avgRating($query->shop_id, $query->product_id);
            $query['avgRating']   = $rating['avgRating'];
            return $query;
        });

        return response()->json([
            'status' => 200,
            'data'   => BestSellingProductResource::collection($bestSellingProducts),
        ], 200);
    }

    public function bestSellingCategories()
    {
        $bestSellingCategories = BestSellingCategory::orderBy('counter', 'desc')->get()->take(8);
        return response()->json([
            'status' => 200,
            'data'   => BestSellingCategoryResource::collection($bestSellingCategories),
        ], 200);
    }

}
