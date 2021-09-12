<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Product;
use App\Models\Shop;
use App\User;

class ShopProductRating extends BaseModel
{

    protected $table       = 'shop_product_ratings';
    protected $auditColumn = true;
    protected $fillable    = ['user_id', 'product_id', 'shop_id', 'rating', 'review', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


}
