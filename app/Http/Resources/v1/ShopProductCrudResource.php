<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ShopProductCrudResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Collection) {
            return ShopProductResource::collection($this->resource);
        }

        $result = [
            "id"             => $this->id,
            "unit_price"     => $this->unit_price,
            "discount_price" => $this->discount_price,
            "quantity"       => $this->quantity,
            "variations"     => $this->variations,
            "options"        => $this->options,
        ];

        if (isset($this->stock_count) && $this->in_stock) {
            $result['stock_count'] = $this->stock_count;
            $result['in_stock']    = $this->in_stock;
        }

        if(isset($this->avgRating)) {
            $this->product->avgRating = $this->avgRating;
        }

        $result['product'] = new ProductResource($this->product);

        return $result;
    }
}
