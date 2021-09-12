<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Status;
use App\Http\Controllers\FrontendController;
use App\Models\Area;
use App\Models\Banner;
use App\Models\BestSellingCategory;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;

class WebController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        $this->data['banners']   = Banner::where(['status' => Status::ACTIVE])->orderBy('sort', 'asc')->get();
        $this->data['locations'] = Location::orderBy('name', 'desc')->get();
        $this->data['areas']     = Area::orderBy('name', 'desc')->get();

        $this->data['bestSellingShops'] = ShopProduct::with('shop')->selectRaw('shop_products.*, SUM(counter) as qty')->groupBy('shop_id')->orderBy('qty', 'desc')->get()->take(5);

        $this->data['categories']            = Category::pluck('name', 'id');
        $this->data['shopProducts']          = ShopProduct::orderBy('id', 'desc')->get()->take(10);
        $this->data['BestSellingCategories'] = BestSellingCategory::orderBy('counter', 'desc')->get()->take(5);
        $this->data['BestSellingProducts']   = ShopProduct::where('counter', '!=', 0)->orderBy('counter', 'desc')->get()->take(10);

        return view('welcome', $this->data);
    }

    public function shopProduct($shopName, $productSlug)
    {
        $this->data['locations'] = Location::orderBy('name', 'desc')->get();
        $this->data['areas']     = Area::orderBy('name', 'desc')->get();
        $shop                    = Shop::where(['slug' => $shopName])->first();
        $product                 = Product::where(['slug' => $productSlug])->first();
        if (!blank($shop) && !blank($product)) {
            $this->data['site_title']  = $product->name;
            $this->data['shopProduct'] = ShopProduct::where(['shop_id' => $shop->id, 'product_id' => $product->id])->first();
            return view('frontend.shop_product', $this->data);
        }
        return abort(404);
    }
}
