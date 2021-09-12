<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RatingStatus;
use App\Http\Controllers\BackendController;
use App\Models\ShopProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class RatingController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Ratings';

        $this->middleware(['permission:rating']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.rating.index', $this->data);
    }

    public function update(Request $request, $id)
    {
        $rating         = ShopProductRating::findOrFail($id);
        $rating->status = $rating->status == 10 ? RatingStatus::ACTIVE : RatingStatus::INACTIVE;
        $rating->save();

        return redirect(route('admin.rating.index'))->withSuccess('The Data Updated Successfully');
    }

    public function getRating(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status) && (int) $request->status) {
                $ratings = ShopProductRating::where(['status' => $request->status])->latest()->get();
            } else {
                $ratings = ShopProductRating::latest()->get();
            }

            $i           = 1;
            $ratingArray = [];
            if (!blank($ratings)) {
                foreach ($ratings as $rating) {
                    $ratingArray[$i]                 = $rating;
                    $ratingArray[$i]['image']        = $rating->user->images;
                    $ratingArray[$i]['product_name'] = Str::limit($rating->product->name, 30);
                    $ratingArray[$i]['shop_name']    = Str::limit($rating->shop->name, 30);
                    $ratingArray[$i]['rating']       = number_format($rating->rating, 1);
                    $ratingArray[$i]['review']       = Str::limit($rating->review, 30);
                    $ratingArray[$i]['status']       = trans('rating_statuses.' . $rating->status);
                    $ratingArray[$i]['setID']        = $i;
                    $i++;
                }
            }

            return Datatables::of($ratingArray)
                ->addColumn('action', function ($rating) {
                    $retAction = '';

                    if (auth()->user()->can('rating')) {
                        $retAction .= '<form class="float-left pl-2" action="' . route('admin.rating.update', $rating) . '" method="POST">' . method_field('PUT') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Active / Inactive"><i class="fa fa-trash"></i></button></form>';
                    }
                    return $retAction;
                })
                ->addColumn('image', function ($rating) {
                    return '<figure class="avatar mr-2"><img src="' . $rating->image . '" alt=""></figure>';
                })
                ->editColumn('id', function ($rating) {
                    return $rating->setID;
                })
                ->escapeColumns([])
                ->make(true);
        }
    }
}
