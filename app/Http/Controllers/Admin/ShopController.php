<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CurrentStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductType;
use App\Enums\ShopStatus;
use App\Enums\Status;
use App\Enums\UserStatus;
use App\Http\Controllers\BackendController;
use App\Http\Requests\ShopRequest;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Services\DepositService;
use App\Models\Area;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\Datatables\Datatables;

class ShopController extends BackendController
{
    /**
     * ShopController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Shops';
        $this->middleware(['permission:shop'])->only('index');
        $this->middleware(['permission:shop_create'])->only('create', 'store');
        $this->middleware(['permission:shop_edit'])->only('edit', 'update');
        $this->middleware(['permission:shop_delete'])->only('destroy');
        $this->middleware(['permission:shop_show'])->only('show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->myrole == 3) {
            $shopID = auth()->user()->shop->id ?? 0;
            if ($shopID == 0) {
                $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
                $this->data['areas']     = [];

                $queryArray['status'] = Status::ACTIVE;
                if (old('location_id')) {
                    $queryArray['location_id'] = old('location_id');
                }
                $this->data['areas'] = Area::where($queryArray)->get();
                return view('admin.shop.shopcreate', $this->data);
            }
            return $this->show($shopID);
        }
        return view('admin.shop.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        $this->data['areas']     = [];
        $location_id             = old('location_id');
        if ($location_id) {
            $this->data['areas'] = Area::where([
                'location_id' => $location_id,
                'status'      => Status::ACTIVE,
            ])->get();
        }
        return view('admin.shop.create', $this->data);
    }

    /**
     * @param ShopRequest $request
     *
     * @return mixed
     */
    public function store(ShopRequest $request)
    {
        $user             = new User;
        $user->first_name = $request->get('first_name');
        $user->last_name  = $request->get('last_name');
        $user->email      = $request->get('email');
        $user->username   = $request->username ?? $this->username($request->email);
        $user->phone      = $request->get('phone');
        $user->address    = $request->get('address');
        $user->status     = $request->get('userstatus');
        $user->password   = bcrypt($request->get('password'));
        $user->save();
        $role = Role::find(3);
        $user->assignRole($role->name);
        $shop                  = new Shop;
        $shop->user_id         = $user->id;
        $shop->location_id     = $request->location_id;
        $shop->area_id         = $request->area_id;
        $shop->name            = $request->name;
        $shop->description     = $request->description;
        $shop->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
        $shop->lat             = $request->lat;
        $shop->long            = $request->long;
        $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $shop->address         = $request->shopaddress;
        $shop->current_status  = $request->current_status;
        if ($user->status == UserStatus::INACTIVE) {
            $shop->status = ShopStatus::INACTIVE;
        } else {
            $shop->status = $request->status;
        }
        $shop->applied = false;
        $shop->save();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $shop->addMediaFromRequest('image')->toMediaCollection('shops');
        }
        $depositAmount = $request->deposit_amount;
        if (blank($depositAmount)) {
            $depositAmount = 0;
        }
        $limitAmount = $request->limit_amount;
        if (blank($limitAmount)) {
            $limitAmount = 0;
        }
        $depositService = app(DepositService::class)->depositAdjust($user->id, $depositAmount, $limitAmount);
        if ($depositService->status) {
            return redirect(route('admin.shop.index'))->withSuccess('The Data Inserted Successfully');
        } else {
            return redirect(route('admin.shop.index'))->withError($depositService->message);
        }
    }

    public function shopstore(ShopStoreRequest $request)
    {
        $shop                  = new Shop;
        $shop->user_id         = auth()->id();
        $shop->location_id     = $request->location_id;
        $shop->area_id         = $request->area_id;
        $shop->name            = $request->name;
        $shop->description     = $request->description;
        $shop->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
        $shop->lat             = $request->lat;
        $shop->long            = $request->long;
        $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $shop->address         = $request->shopaddress;
        $shop->current_status  = $request->current_status;
        $shop->status          = ShopStatus::INACTIVE;
        $shop->applied         = true;
        $shop->save();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $shop->addMediaFromRequest('image')->toMediaCollection('shops');
        }
        return redirect(route('admin.shop.index'))->withSuccess('The data inserted successfully.');
    }

    public function shopedit($id)
    {
        $this->data['shop']      = Shop::shopowner()->findOrFail($id);
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        $this->data['areas']     = [];

        $queryArray['status'] = Status::ACTIVE;
        if (old('location_id')) {
            $queryArray['location_id'] = old('location_id');
        }
        $this->data['areas'] = Area::where($queryArray)->get();
        return view('admin.shop.shopedit', $this->data);
    }

    public function shopupdate(ShopStoreRequest $request, $id)
    {
        $shop = Shop::shopowner()->findOrFail($id);

        $shop->user_id         = auth()->id();
        $shop->location_id     = $request->location_id;
        $shop->area_id         = $request->area_id;
        $shop->name            = $request->name;
        $shop->description     = $request->description;
        $shop->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
        $shop->lat             = $request->lat;
        $shop->long            = $request->long;
        $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $shop->address         = $request->shopaddress;
        $shop->current_status  = $request->current_status;
        $shop->applied         = true;
        $shop->save();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $shop->addMediaFromRequest('image')->toMediaCollection('shops');
        }
        return redirect(route('admin.shop.index'))->withSuccess('The data updated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['shop']      = Shop::shopowner()->findOrFail($id);
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        $this->data['areas']     = [];
        $location_id             = old('location_id', $this->data['shop']->location_id);
        if ($location_id) {
            $this->data['areas'] = Area::where([
                'location_id' => $location_id,
                'status'      => Status::ACTIVE,
            ])->get();
        }
        return view('admin.shop.edit', $this->data);
    }

    /**
     * @param ShopRequest $request
     * @param Shop $shop
     *
     * @return mixed
     */
    public function update(ShopRequest $request, Shop $shop)
    {
        if (!blank($shop->user)) {
            $user          = $shop->user;
            $depositAmount = $request->deposit_amount;
            if (blank($depositAmount)) {
                $depositAmount = 0;
            }
            $limitAmount = $request->limit_amount;
            if (blank($limitAmount)) {
                $limitAmount = 0;
            }
            $depositService = app(DepositService::class)->depositAdjust($user->id, $depositAmount, $limitAmount);
            if ($depositService->status) {
                $user             = $shop->user;
                $user->first_name = $request->get('first_name');
                $user->last_name  = $request->get('last_name');
                $user->email      = $request->get('email');
                $user->username   = $request->username ?? $this->username($request->email);
                $user->phone      = $request->get('phone');
                $user->address    = $request->get('address');
                $user->status     = $request->get('userstatus');
                if (!blank($request->get('password')) && (strlen($request->get('password')) >= 4)) {
                    $user->password = bcrypt($request->get('password'));
                }
                $user->save();
                $role = Role::find(3);
                $user->assignRole($role->name);
                $shop->location_id     = $request->location_id;
                $shop->area_id         = $request->area_id;
                $shop->name            = $request->name;
                $shop->description     = $request->description;
                $shop->delivery_charge = empty($request->delivery_charge) ? 0 : $request->delivery_charge;
                $shop->lat             = $request->lat;
                $shop->long            = $request->long;
                $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
                $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
                $shop->current_status  = $request->current_status;
                $shop->address         = $request->shopaddress;
                if ($user->status == UserStatus::INACTIVE) {
                    $shop->status = ShopStatus::INACTIVE;
                } else {
                    $shop->status = $request->status;
                }
                $shop->save();
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $shop->media()->delete($shop->id);
                    $shop->addMediaFromRequest('image')->toMediaCollection('shops');
                }
                return redirect(route('admin.shop.index'))->withSuccess('The Data Updated Successfully');
            } else {
                return redirect(route('admin.shop.index'))->withError($depositService->message);
            }
        } else {
            return redirect(route('admin.shop.index'))->withError('The User Not Found');
        }
    }

    public function show($id)
    {
        $shop                          = Shop::shopowner()->findOrFail($id);
        $orders                        = Order::where(['shop_id' => $id])->whereDate('created_at', Carbon::today())->orderOwner()->get();
        $this->data['total_order']     = $orders->count();
        $this->data['pending_order']   = $orders->where('status', OrderStatus::PENDING)->count();
        $this->data['process_order']   = $orders->where('status', OrderStatus::PROCESS)->count();
        $this->data['completed_order'] = $orders->where('status', OrderStatus::COMPLETED)->count();
        if (blank($shop->user)) {
            return redirect(route('admin.shop.index'))->withError('The user not found.');
        }
        $this->data['shop'] = $shop;
        $this->data['user'] = $shop->user;
        return view('admin.shop.show', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::shopowner()->findOrFail($id)->delete();
        return redirect(route('admin.shop.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getshop(Request $request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            if (!empty($request->status) && (int) $request->status) {
                $queryArray['status'] = $request->status;
            }
            if ($request->applied != '') {
                $queryArray['applied'] = $request->applied;
            }
            if (!blank($queryArray)) {
                $shops = Shop::where($queryArray)->orderBy('id', 'desc')->shopowner()->get();
            } else {
                $shops = Shop::orderBy('id', 'desc')->shopowner()->get();
            }
            $i         = 1;
            $shopArray = [];
            if (!blank($shops)) {
                foreach ($shops as $shop) {
                    $shopArray[$i]          = $shop;
                    $shopArray[$i]['name']  = Str::limit($shop->name, 20);
                    $shopArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($shopArray)->addColumn('action', function ($shop) {
                $retAction = '';
                if (auth()->user()->can('shop_show')) {
                    $retAction .= '<a href="' . route('admin.shop.products', $shop) . '" class="btn btn-sm btn-icon float-left btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Add Product"> <i class="far fa-list-alt"></i></a>';
                    $retAction .= '<a href="' . route('admin.shop.show', $shop) . '" class="btn btn-sm btn-icon float-left btn-info mr-2" data-toggle="tooltip" data-placement="top" title="View"> <i class="far fa-eye"></i></a>';
                }
                if (auth()->user()->can('shop_edit')) {
                    $retAction .= '<a href="' . route('admin.shop.edit', $shop) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                }
                if (auth()->user()->can('shop_delete')) {
                    $retAction .= '<form class="float-left pl-2" action="' . route('admin.shop.destroy', $shop) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                }
                return $retAction;
            })->editColumn('user_id', function ($shop) {
                return Str::limit($shop->user->name ?? null, 20);
            })->editColumn('location_id', function ($shop) {
                return Str::limit($shop->location->name ?? null, 20);
            })->editColumn('status', function ($shop) {
                return ($shop->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
            })->editColumn('current_status', function ($shop) {
                return ($shop->current_status == 5 ? trans('current_statuses.' . CurrentStatus::YES) : trans('current_statuses.' . CurrentStatus::NO));
            })->editColumn('id', function ($shop) {
                return $shop->setID;
            })->make(true);
        }
    }

    public function getArea(Request $request)
    {
        echo "<option value=''>" . __('Select Area') . "</option>";
        $location_id = $request->location_id;
        if (is_numeric($location_id)) {
            $areas = Area::where([
                'location_id' => $location_id,
                'status'      => Status::ACTIVE,
            ])->get();
            if (!blank($areas)) {
                foreach ($areas as $area) {
                    echo "<option value='" . $area->id . "'>" . $area->name . "</option>";
                }
            }
        }
    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @param Shop $shop
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function products(Shop $shop)
    {
        $this->data['shop'] = $shop;
        return view('admin.shop.products', $this->data);
    }

    public function productAdd(Shop $shop)
    {
        $this->data['shop']     = $shop;
        $this->data['products'] = Product::where(['status' => Status::ACTIVE])->get();
        return view('admin.shop.productAdd', $this->data);
    }

    public function productStore(Request $request, $shop_id)
    {

        $requestArray = [
            'product_type' => 'required',
            'product_id'   => 'required',
        ];
        if ($request->product_type == ProductType::SINGLE) {
            $requestArray['unit_price']     = 'required|numeric|gt:0|between:1,999999999';
            $requestArray['quantity']       = 'required|numeric|min:1|between:1,999999999';
            $requestArray['discount_price'] = 'nullable|numeric|min:0|between:0,999999999';
        } else if ($request->product_type == ProductType::VARIATION) {
            $requestArray['variation.*.name']           = 'required';
            $requestArray['variation.*.price']          = 'required|numeric|gt:0|between:1,999999999';
            $requestArray['variation.*.quantity']       = 'required|numeric|min:1|between:1,999999999';
            $requestArray['variation.*.discount_price'] = 'nullable|numeric|min:0|between:0,999999999';
        }
        if ($request->product_type != '') {
            $requestArray['option.*.name']  = 'nullable';
            $requestArray['option.*.price'] = 'nullable|gt:0|between:1,999999999';
        }
        $validator      = Validator::make($request->all(), $requestArray);
        $getShopProduct = ShopProduct::where([
            'shop_id'    => $shop_id,
            'product_id' => $request->product_id,
        ])->first();
        $validator->after(function ($validator) use ($getShopProduct, $request) {
            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
            if (($request->product_type == ProductType::SINGLE) && ($request->discount_price > $request->unit_price)) {
                $validator->errors()->add('discount_price', 'This dicount price cann\'t be greater than unit price.');
            }
        });
        if ($validator->fails()) {
            $request->session()->flash('variation', array_keys($request->variation));
            $request->session()->flash('option', array_keys($request->option));
            return redirect(route('admin.shop.products.create', $shop_id))->withErrors($validator)->withInput();
        }
        if ($request->product_type == ProductType::SINGLE) {
            $shopProduct                 = new ShopProduct;
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = $request->get('unit_price');
            $shopProduct->quantity       = $request->get('quantity');
            $shopProduct->discount_price = $request->get('discount_price') != null ? $request->get('discount_price') : 0;
            $shopProduct->save();
        } else if ($request->product_type == ProductType::VARIATION) {

            $shopProduct                 = new ShopProduct;
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = 0;
            $shopProduct->quantity       = 0;
            $shopProduct->discount_price = 0;
            $shopProduct->save();
            $key                       = array_key_first($request->variation);
            $smallPrice                = isset($request->variation[$key]) ? $request->variation[$key]['price'] : 0;
            $smallQuantity             = isset($request->variation[$key]) ? $request->variation[$key]['quantity'] : 0;
            $smallDiscountPrice        = isset($request->variation[$key]) ? $request->variation[$key]['discount_price'] : 0;
            $i                         = 0;
            $shopProductVariationArray = [];
            foreach ($request->variation as $variation) {
                if ($variation['price'] < $smallPrice) {
                    $smallPrice         = $variation['price'];
                    $smallDiscountPrice = $variation['discount_price'];
                    $smallQuantity      = $variation['quantity'];
                }

                $shopProductVariationArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductVariationArray[$i]['product_id']      = $request->product_id;
                $shopProductVariationArray[$i]['shop_id']         = $shop_id;
                $shopProductVariationArray[$i]['name']            = $variation['name'];
                $shopProductVariationArray[$i]['price']           = $variation['price'];
                $shopProductVariationArray[$i]['discount_price']  = isset($variation['discount_price']) ? $variation['discount_price'] : 0;
                $shopProductVariationArray[$i]['quantity']        = $variation['quantity'];
                $i++;
            }
            ShopProductVariation::insert($shopProductVariationArray);
            $shopProduct->unit_price     = $smallPrice;
            $shopProduct->quantity       = $smallQuantity;
            $shopProduct->discount_price = !is_null($smallDiscountPrice) ? $smallDiscountPrice : 0;
            $shopProduct->save();
        }
        if (!blank($shopProduct) && !blank($request->option)) {
            $i                      = 0;
            $shopProductOptionArray = [];
            foreach ($request->option as $option) {
                if ($option['name'] == '' || $option['price'] == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option['name'];
                $shopProductOptionArray[$i]['price']           = $option['price'];
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }
        return redirect(route('admin.shop.products', $shop_id))->withSuccess("The Data Inserted Successfully");
    }

    /**
     * @param Request $request
     * @param Shop $shop
     * @param Product $product
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shopProductDelete(Request $request, Shop $shop, ShopProduct $shopproduct)
    {
        if (!blank($shopproduct)) {
            ShopProduct::findOrFail($shopproduct->id)->delete();
            ShopProductVariation::where('shop_product_id', $shopproduct->id)->delete();
            ShopProductOption::where('shop_product_id', $shopproduct->id)->delete();
        }
        return redirect()->route('admin.shop.products', $shop)->withSuccess('The Shop Product Deleted Successfully');
    }

    public function shopProductEdit(Shop $shop, ShopProduct $shopproduct)
    {
        $this->data['shop']               = $shop;
        $this->data['products']           = Product::where(['status' => Status::ACTIVE])->get();
        $this->data['shopproduct']        = $shopproduct;
        $this->data['product_type']       = !blank($shopproduct->product_variations) ? ProductType::VARIATION : ProductType::SINGLE;
        $this->data['product_variations'] = $shopproduct->product_variations;
        $this->data['product_options']    = $shopproduct->product_options;
        return view('admin.shop.productEdit', $this->data);
    }

    public function shopProductUpdate(Request $request, Shop $shop, $shop_product_id)
    {
        $shopProduct  = ShopProduct::findOrFail($shop_product_id);
        $shop_id      = $shop->id;
        $requestArray = [
            'product_type' => 'required',
            'product_id'   => 'required',
        ];

        if ($request->product_type == ProductType::SINGLE) {
            $requestArray['unit_price']     = 'required|numeric|gt:0|between:1,999999999';
            $requestArray['quantity']       = 'required|numeric|min:1|between:1,999999999';
            $requestArray['discount_price'] = 'nullable|numeric|min:0|between:0,999999999';
        } else if ($request->product_type == ProductType::VARIATION) {
            $requestArray['variation.*.name']           = 'required';
            $requestArray['variation.*.price']          = 'required|numeric|gt:0|between:1,999999999';
            $requestArray['variation.*.quantity']       = 'required|numeric|min:1|between:1,999999999';
            $requestArray['variation.*.discount_price'] = 'nullable|numeric|min:0|between:0,999999999';
        }

        if ($request->product_type != '') {
            $requestArray['option.*.name']  = 'nullable';
            $requestArray['option.*.price'] = 'nullable|gt:0|between:1,999999999';
        }
        $validator      = Validator::make($request->all(), $requestArray);
        $getShopProduct = ShopProduct::where([
            'shop_id'    => $shop_id,
            'product_id' => $request->product_id,
        ])->where('id', '!=', $shop_product_id)->first();
        $validator->after(function ($validator) use ($getShopProduct, $request) {
            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
            if (($request->product_type == ProductType::SINGLE) && ($request->discount_price > $request->unit_price)) {
                $validator->errors()->add('discount_price', 'This dicount price cann\'t be greater than unit price.');
            }
        });

        if ($validator->fails()) {
            $request->session()->flash('variation', array_keys($request->variation));
            $request->session()->flash('option', array_keys($request->option));
            return redirect(route('admin.shop.shopproduct.edit', [
                $shop,
                $shopProduct,
            ]))->withErrors($validator)->withInput();
        }

        ShopProductOption::where('shop_product_id', $shopProduct->id)->delete();
        if ($request->product_type == ProductType::SINGLE) {
            ShopProductVariation::where('shop_product_id', $shopProduct->id)->delete();
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = $request->get('unit_price');
            $shopProduct->quantity       = $request->get('quantity');
            $shopProduct->discount_price = $request->get('discount_price') != null ? $request->get('discount_price') : 0;
            $shopProduct->save();
        } else if ($request->product_type == ProductType::VARIATION) {
            $shopProduct->shop_id        = $shop_id;
            $shopProduct->product_id     = $request->get('product_id');
            $shopProduct->unit_price     = 0;
            $shopProduct->quantity       = 0;
            $shopProduct->discount_price = 0;
            $shopProduct->save();
            $key                  = array_key_first($request->variation);
            $smallPrice           = isset($request->variation[$key]) ? $request->variation[$key]['price'] : 0;
            $smallQuantity        = isset($request->variation[$key]) ? $request->variation[$key]['quantity'] : 0;
            $smallDiscountPrice   = isset($request->variation[$key]) ? $request->variation[$key]['discount_price'] : 0;
            $shopProductVariation = ShopProductVariation::where([
                'shop_id'    => $shop_id,
                'product_id' => $shopProduct->product_id,
            ])->get()->pluck('id', 'id')->toArray();
            $variationArray = [];
            foreach ($request->variation as $key => $variation) {
                $variationArray[$key] = $key;
                if ($variation['price'] < $smallPrice) {
                    $smallPrice         = $variation['price'];
                    $smallDiscountPrice = $variation['discount_price'];
                    $smallQuantity      = $variation['quantity'];
                }

                if (isset($shopProductVariation[$key])) {
                    $shopProductVariationItem = ShopProductVariation::where([
                        'shop_id'    => $shop_id,
                        'product_id' => $shopProduct->product_id,
                        'id'         => $key,
                    ])->first();
                    $shopProductVariationItem->shop_product_id = $shopProduct->id;
                    $shopProductVariationItem->product_id      = $request->product_id;
                    $shopProductVariationItem->shop_id         = $shop_id;
                    $shopProductVariationItem->name            = $variation['name'];
                    $shopProductVariationItem->price           = $variation['price'];
                    $shopProductVariationItem->quantity        = $variation['quantity'];
                    $shopProductVariationItem->discount_price  = $variation['discount_price'];
                    $shopProductVariationItem->discount_price  = isset($variation['discount_price']) ? $variation['discount_price'] : 0;
                    $shopProductVariationItem->save();
                } else {
                    $shopProductVariationArray['shop_product_id'] = $shopProduct->id;
                    $shopProductVariationArray['product_id']      = $request->product_id;
                    $shopProductVariationArray['shop_id']         = $shop_id;
                    $shopProductVariationArray['name']            = $variation['name'];
                    $shopProductVariationArray['price']           = $variation['price'];
                    $shopProductVariationArray['quantity']        = $variation['quantity'];
                    $shopProductVariationArray['discount_price']  = isset($variation['discount_price']) ? $variation['discount_price'] : 0;
                    ShopProductVariation::insert($shopProductVariationArray);
                }
            }
            $shopProduct->unit_price     = $smallPrice;
            $shopProduct->quantity       = $smallQuantity;
            $shopProduct->discount_price = !is_null($smallDiscountPrice) ? $smallDiscountPrice : 0;
            $shopProduct->save();
            $deleteArray = array_diff($shopProductVariation, $variationArray);
            if (!blank($deleteArray)) {
                ShopProductVariation::whereIn('id', $deleteArray)->delete();
            }
        }

        if (!blank($shopProduct) && !blank($request->option)) {
            $i                      = 0;
            $shopProductOptionArray = [];
            foreach ($request->option as $option) {

                if ($option['name'] == '' || $option['price'] == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option['name'];
                $shopProductOptionArray[$i]['price']           = $option['price'];
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }
        return redirect(route('admin.shop.products', $shop_id))->withSuccess("The Data Updated Successfully");
    }

    public function getShopProduct()
    {
        if (request()->ajax()) {
            $queryArray['id'] = request('shop_id');
            $shop             = Shop::where($queryArray)->latest()->shopowner()->first();
            $i                = 1;
            $shopProductArr   = [];
            if (!blank($shop->shopproducts)) {
                foreach ($shop->shopproducts->sortByDesc('id') as $shopproduct) {
                    $shopProductArr[$i]          = $shopproduct;
                    $shopProductArr[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($shopProductArr)->addColumn('action', function ($shopProduct) {
                $retAction = '';
                if (auth()->user()->can('shop_edit')) {
                    $retAction .= '<a href="' . route('admin.shop.shopproduct.edit', [
                        $shopProduct->shop_id,
                        $shopProduct,
                    ]) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
                }
                if (auth()->user()->can('shop_delete')) {
                    $retAction .= '<form class="float-left pl-2" action="' . route('admin.shop.shopproduct.delete', [
                        $shopProduct->shop_id,
                        $shopProduct,
                    ]) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                }
                return $retAction;
            })->editColumn('product', function ($shopProduct) {
                return optional($shopProduct->product)->name;
            })->editColumn('id', function ($shopProduct) {
                return $shopProduct->setID;
            })->make(true);
        }
    }

}
