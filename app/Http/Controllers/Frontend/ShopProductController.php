<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Http\Services\RatingsService;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;

class ShopProductController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function __invoke(Shop $shop, Product $product)
    {
        if (!blank($shop) && !blank($product)) {
            $this->data['shop']        = $shop;
            $this->data['product']     = $product;
            $this->data['site_title']  = $product->name;
            $this->data['shopProduct'] = ShopProduct::where(['shop_id' => $shop->id, 'product_id' => $product->id])->first();

            $rating      = new RatingsService();
            $ratingArray = $rating->avgRating($shop->id, $product->id);

            $this->data['ratingArray'] = $ratingArray['ratings'];
            $this->data['countUser']   = $ratingArray['countUser'];
            $this->data['avgRating']   = $ratingArray['avgRating'];

            if ($shop->shopproducts->where('product_id', '!=', $product->id)->count() > 8) {
                $this->data['shopRelatedProduct'] = $shop->shopproducts->where('product_id', '!=', $product->id)->random(8);
            } else {
                $this->data['shopRelatedProduct'] = $shop->shopproducts->where('product_id', '!=', $product->id);
            }

            return view('frontend.shop_product', $this->data);
        }

        return abort(404);
    }

}
