<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Area;
use App\Models\Location;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class SearchController extends FrontendController
{
    public function filter(Request $request)
    {
        $this->data['locations'] = Location::orderBy('name', 'desc')->get();
        $this->data['areas']     = Area::orderBy('name', 'desc')->get();

        $shops = (new Shop)->newQuery();
        if (!blank($request->get('name'))) {
            $shops->where('name', 'like', '%' . $request->get('name') . '%');
            session()->put('query', $request->get('name'));
        } else {
            session()->forget('query');
        }

        if (!blank($request->get('location'))) {
            $shops->where('location_id', $request->get('location'));
            session()->put('location', $request->get('location'));
        } else {
            session()->forget('location');
        }

        if (!blank($request->get('area'))) {
            $shops->where('area_id', $request->get('area'));
            session()->put('area', $request->get('area'));
        } else {
            session()->forget('area');
        }

        $products  = [];
        $shopArray = $shops->get()->pluck('id')->toArray();
        if (!blank($shopArray)) {
            $shopProduct = ShopProduct::whereIn('shop_id', $shopArray);
            if (!blank($request->get('name'))) {
                $shopProduct->with('product')->whereHas('product', function ($query) {
                    $query->where('name', 'like', '%' . request('name') . '%');
                });
            }
            $products = $shopProduct->with('product')->paginate(10);
        }
        $this->data['products'] = $products;
        $this->data['shops']    = $shops->paginate(10);

        return view('frontend.shop-listing', $this->data);
    }

    public function filterProduct(Request $request, Shop $shop)
    {
        $mainShopProduct = ShopProduct::where(['shop_id' => $shop->id])->get();
        $shopProduct     = $mainShopProduct->pluck('unit_price', 'product_id');

        $this->data['productPrices']         = $shopProduct;
        $this->data['productDiscountPrices'] = $mainShopProduct->pluck('discount_price', 'product_id');

        $products = $shop->products();

        if (!blank($shop)) {
            $products->with(['shops'])->whereHas('shops', function ($query) use ($shop) {
                $query->where('shop_id', $shop->id);
            });
        }

        if (!blank($request->get('categories'))) {
            $products->with(['categories'])->whereHas('categories', function ($query) use ($request) {
                $query->whereIn('category_id', $request->get('categories'));
            });

            session()->put('shop-categories', $request->get('categories'));
        } else {
            session()->forget('shop-categories');
        }

        if (!blank($request->has('name'))) {
            $products->where('name', 'like', '%' . $request->get('name') . '%');
            session()->put('product-query', $request->get('name'));
        } else {
            session()->forget('product-query');
        }

        $mainProduct = $products->get()->map(function ($query) use ($shopProduct, $shop) {
            $query['unit_price']  = isset($shopProduct[$query->id]) ? $shopProduct[$query->id] : 0;
            $query['stock_count'] = $query->stockCount($shop);
            $query['in_stock']    = $query->inStock($shop);
            return $query;
        });

        $this->data['shop']     = $shop;
        $this->data['products'] = $this->paginate($mainProduct, 10, null, ['path' => route('search-product', $shop)]);

        return view('frontend.shop', $this->data);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {

        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}
