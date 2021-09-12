<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends BaseModel implements HasMedia
{
    use HasSlug, WatchableTrait, HasMediaTrait;

    protected $table       = 'products';
    protected $guarded     = ['id'];
    protected $auditColumn = true;

    protected $fakeColumns = [];

    public function getRouteKeyName()
    {
        return request()->segment(1) === 'admin' ? 'id' : 'slug';
    }

    public function creator()
    {
        return $this->morphTo();
    }

    public function editor()
    {
        return $this->morphTo();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_products');
    }

    public function orders()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function scopeIsLive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFromCategory($query, Category $category)
    {
        return $query->whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('products'))) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset('assets/img/default/product.png');
    }

    public function getThumimagesAttribute()
    {
        $retArray = [];
        $products = $this->getMedia('products');
        if (!blank($products)) {
            foreach ($products as $key => $product) {
                $retArray[$key] = asset($product->getUrl());
            }
        }
        return $retArray;
    }

    public function inStock($shop_id, $variation_id = 0, $smallVariationStatus = false)
    {
        return $this->stockCount($shop_id, $variation_id, $smallVariationStatus) > 0;
    }

    public function variations()
    {
        return $this->hasMany(ShopProductVariation::class);
    }

    public function shopProductWithVariation($shop_id, $variation_id = 0)
    {
        return $this->variations()->where(['shop_id' => $shop_id, 'id' => $variation_id])->first()->quantity;
    }

    public function shopProductWithSingle($shop_id)
    {
        return $this->shops()->where(['shop_id' => $shop_id])->sum('quantity');
    }

    public function productOrderSum( $shop_id, $variation_id = 0, $total = false)
    {
        if($total) {
            return $this->orders()->where(['shop_id' => $shop_id ])->whereHas('order', function($query) use($shop_id) {
                $query->where(['shop_id' => $shop_id]);
                $query->where('status', '!=', OrderStatus::CANCEL);
                $query->where('status', '!=', OrderStatus::REJECT);
            })->sum('quantity');
        } else {
            return $this->orders()->where(['shop_id' => $shop_id, 'shop_product_variation_id' => $variation_id])->whereHas('order', function($query) use($shop_id) {
                $query->where(['shop_id' => $shop_id]);
                $query->where('status', '!=', OrderStatus::CANCEL);
                $query->where('status', '!=', OrderStatus::REJECT);
            })->sum('quantity');
        }

    }

    public function stockCount($shop_id, $variation_id = 0, $smallVariationStatus = false)
    {
        $variations = $this->variations()->where(['shop_id' => $shop_id])->get()->toArray();
        if(!blank($variations)) {
            if($smallVariationStatus) {
                $key = array_key_first($variations);
                $smallVariation         = isset($variations[ $key ]) ? $variations[ $key ]['price'] : 0;
                $smallVariationId       = isset($variations[ $key ]) ? $variations[ $key ]['id'] : 0;
                foreach ($variations as $variation) {
                    if($variation['price'] < $smallVariation) {
                        $smallVariation = $variation['price'];
                        $smallVariationId = $variation['id'];
                    }
                }
                return $this->shopProductWithVariation($shop_id, $smallVariationId) - $this->productOrderSum($shop_id, $smallVariationId);
            } else {
                if($variation_id) {
                    return $this->shopProductWithVariation($shop_id, $variation_id) - $this->productOrderSum($shop_id, $variation_id);
                } else {
                    $sumOfVariationQuantity = 0;
                    foreach ($variations as $variation) {
                        $quantity = $this->shopProductWithVariation($shop_id, $variation['id']);
                        $sumOfVariationQuantity += $quantity;
                    }
                    return $sumOfVariationQuantity - $this->productOrderSum($shop_id, 0, true);
                }
            }
        } else {
            return $this->shopProductWithSingle($shop_id) - $this->productOrderSum($shop_id);
        }
    }

    public function deleteMedia($product, $mediaName, $mediaId)
    {
        $media = Media::where([
            'file_name' => $mediaName,
            'collection_name' => 'products',
            'model_id' => $mediaId,
            'model_type' => Product::class,
        ])->first();

        if($media) {
            $media->delete();
        }

    }

}
