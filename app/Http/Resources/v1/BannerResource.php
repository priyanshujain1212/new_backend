<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'link'        => $this->link,
            'sort'        => $this->sort,
            'status'      => $this->status,
            'description' => strip_tags($this->short_description),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'image'       => $this->image(),
        ];
    }

    private function image()
    {
        if (!blank($this->getMedia('banners'))) {
            return asset($this->getFirstMediaUrl('banners'));
        }
        return asset('assets/img/default/banner.jpg');
    }
}
