<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 5/3/20
 * Time: 11:25 AM
 */

namespace App\Http\Services;

use App\Enums\RatingStatus;
use App\Models\ShopProductRating;

class RatingsService
{

    public function avgRating($shopID, $productID)
    {
        $shopProductRatings = ShopProductRating::where(['shop_id' => $shopID, 'product_id' => $productID, 'status' => RatingStatus::ACTIVE])->get();

        $authID = auth()->id();

        $countUser = 0;
        $avgRating = 0;

        $myRatingArray    = [];
        $myRatingArray[0] = [];
        if (!blank($shopProductRatings)) {
            $sumRating = 0;
            $k         = 1;
            foreach ($shopProductRatings as $rating) {
                if ($rating->user_id == $authID) {
                    $myRatingArray[0] = $rating;
                } else {
                    $myRatingArray[$k] = $rating;
                    $k++;
                }

                $sumRating += $rating->rating;
                $countUser++;
            }
            $avgRating = (int) ($sumRating / $countUser);
        }

        if (blank($myRatingArray[0])) {
            unset($myRatingArray[0]);
        }

        $data['countUser'] = $countUser;
        $data['avgRating'] = $avgRating;
        $data['ratings']   = $myRatingArray;
        return $data;
    }

}
