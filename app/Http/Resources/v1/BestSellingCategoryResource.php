<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class BestSellingCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'counter'     => $this->counter,
            'shop_id'     => $this->shop_id,
            'category_id' => $this->category_id,
            'categories'  => new ShopCategoryResource($this->category),
        ];
    }
}
