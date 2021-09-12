<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\v1\RatingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray( $request )
    {

        $result = [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => strip_tags($this->description),
            'unit_price'      => $this->shop_product_unit_price,
            'discount_price' => $this->shop_product_discount_price,
            'image'           => $this->image(),
            'variations'      => ShopProductVariationResource::collection($this->variations),
            'options'         => ShopProductOptionResource::collection($this->options),
            'ratings'         => new RatingResource($this->ratings),
            'shop'            => new ShopResource(isset($this->shops[0]) ? $this->shops[0] : null),
        ];
        if ( isset($this->pivot->shop_id) ) {
            $result['stock_count'] = $this->stockCount($this->pivot->shop_id, 0, true);
            $result['in_stock']    = $this->inStock($this->pivot->shop_id, 0, true);
        }
        if ( isset($this->shops[0]) ) {
            $result['stock_count'] = $this->stockCount($this->shops[0]->id,  0, true);
            $result['in_stock']    = $this->inStock($this->shops[0]->id, 0, true);
        }
        return $result;
    }

    private function image()
    {
        if ( !blank($this->getMedia('products')) ) {
            $images   = $this->getMedia('products');
            $response = [];
            foreach ( $images as $image ) {
                $response[] = asset('storage/' . $image->id . '/' . $image->file_name);
            }
            return $response;
        }
        return [ 0 => asset('assets/img/default/product.png') ];
    }
}
