<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'countUser' => $this['countUser'],
            'avgRating' => $this['avgRating'],
            'reviews'   => $this->reviews($this['ratings']),
        ];
    }

    private function reviews($ratings)
    {
        $returnArray = [];
        if (!blank($ratings)) {
            $i = 0;
            foreach ($ratings as $rating) {
                $returnArray[$i]['id']         = $rating->id;
                $returnArray[$i]['user_id']    = $rating->user_id;
                $returnArray[$i]['product_id'] = $rating->product_id;
                $returnArray[$i]['shop_id']    = $rating->shop_id;
                $returnArray[$i]['rating']     = $rating->rating;
                $returnArray[$i]['review']     = $rating->review;
                $returnArray[$i]['status']     = $rating->status;
                $returnArray[$i]['name']       = $rating->user->name;
                $returnArray[$i]['image']      = $rating->user->images;
                $returnArray[$i]['date']       = food_date_format($rating->created_at);
                $i++;
            }
        }
        return $returnArray;
    }
}
