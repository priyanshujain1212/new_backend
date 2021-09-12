<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class BestSellingProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->product->avgRating = $this->avgRating;
        return [
            'id'         => $this->id,
            'counter'    => $this->counter,
            'shop_id'    => $this->shop_id,
            'product_id' => $this->product_id,
            'products'   => new ProductResource($this->product),
            'shop'       => new ShopResource($this->shop),
        ];
    }
}
