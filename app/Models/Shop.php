<?php

namespace App\Models;

use App\Http\Services\RatingsService;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Shop extends BaseModel implements HasMedia
{
    use WatchableTrait, HasMediaTrait, HasSlug;
    protected $table       = 'shops';
    protected $guarded     = ['id'];
    protected $auditColumn = true;

    protected $fakeColumns = [];

    public function getRouteKeyName()
    {
        return request()->segment(1) === 'admin' ? 'id' : 'slug';
    }

    public function avgRating($shopID,$productId){
        $rating = new RatingsService();
        return $rating->avgRating($shopID,$productId);
    }
    public function creator()
    {
        return $this->morphTo();
    }

    public function editor()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'shop_products');
    }

    public function shopproducts()
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('shops'))) {
            return asset($this->getFirstMediaUrl('shops'));
        }
        return asset('assets/img/default/shop.png');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function scopeShopowner($query)
    {
        if (isset(auth()->user()->roles) && auth()->user()->myrole != 1) {
            $query->where('user_id', auth()->id());
        }
    }

}
