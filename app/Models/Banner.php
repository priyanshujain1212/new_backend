<?php

namespace App\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Banner extends BaseModel implements HasMedia
{

    use HasMediaTrait;

    protected $table       = 'banners';
    protected $auditColumn = true;
    protected $fillable    = ['title', 'short_description', 'link', 'sort', 'status'];

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('banners'))) {
            return asset($this->getFirstMediaUrl('banners'));
        }
        return asset('assets/img/default/banner.jpg');
    }

}
