<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Area;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Location;
use App\Models\Shop;
use App\Models\ShopProduct;

class ShopController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function __invoke(Shop $shop)
    {
        $this->data['shop']                = $shop;
        $this->data['products']            = $shop->products()->distinct('product_id')->latest()->paginate(15);
        $this->data['products_categories'] = CategoryProduct::whereIn('product_id', $this->data['products']->pluck('id'))->get()->pluck('category_id')->unique();

        $shopProduct = ShopProduct::where(['shop_id' => $shop->id])->get();

        $this->data['productPrices']         = $shopProduct->pluck('unit_price', 'product_id');
        $this->data['productDiscountPrices'] = $shopProduct->pluck('discount_price', 'product_id');

        $this->data['categories'] = Category::whereIn('id', $this->data['products_categories'])->get();
        $this->data['locations']  = Location::orderBy('name', 'desc')->get();
        $this->data['areas']      = Area::orderBy('name', 'desc')->get();

        return view('frontend.shop', $this->data);
    }

}
