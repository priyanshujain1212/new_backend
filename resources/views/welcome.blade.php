@extends('layouts.app')

@section('search')
<div class="col-lg-6 col-12 col-sm-12">
    <x-search-params />
</div>
@endsection

@section('main-content')

<!-- ========================= SECTION INTRO ========================= -->
@if(!blank($banners))
<section class="section-intro padding-y-sm">
    <div class="container">
        <div class="main-banner slider-banner-slick">
            @foreach($banners as $banner)
                <a href="{{ $banner->link ? url($banner->link) : '#' }}">
                    <div class="item-slide">
                        <img src="{{ $banner->images }}" class="img-fluid rounded">
                        @if($banner->title != '' || $banner->short_description)
                            <div class="carousel-caption d-none d-md-block">
                                @if($banner->title)
                                    <h4>{{ Str::limit($banner->title, 120, '..') }}</h4>
                                @endif
                                @if($banner->short_description)
                                    <p>
                                        {{ Str::of(strip_tags($banner->short_description))->limit(150, '..') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div> <!-- container //  -->
</section>
@endif
<!-- ========================= SECTION INTRO END// ========================= -->


<!-- ========================= SECTION FEATURE ========================= -->
<section class="section-content padding-y-sm">
    <div class="container">
        <article class="card card-body">
            <div class="row">
                <div class="col-md-4">
                    <figure class="item-feature">
                        <span class="text-primary"><i
                                class="{{ setting('step_one_icon', 'fa fa-2x fa-truck') }}"></i></span>
                        <figcaption class="pt-3">
                            <h5 class="title">{{ setting('step_one_title', 'Fast delivery') }}</h5>
                            <p>{{ setting('step_one_description','Fast Deliver is a fast growing and promising courier and parcel service in Bangladesh.') }}
                            </p>
                        </figcaption>
                    </figure> <!-- iconbox // -->
                </div><!-- col // -->
                <div class="col-md-4">
                    <figure class="item-feature">
                        <span class="text-primary"><i
                                class="{{ setting('step_two_icon', 'fa fa-2x fa-landmark') }}"></i></span>
                        <figcaption class="pt-3">
                            <h5 class="title">{{ setting('step_two_title', 'Creative Strategy') }}</h5>
                            <p>{{ setting('step_two_description','A creative strategy is made to help explain to all concerned') }}</p>
                        </figcaption>
                    </figure> <!-- iconbox // -->
                </div><!-- col // -->
                <div class="col-md-4">
                    <figure class="item-feature">
                        <span class="text-primary"><i
                                class="{{ setting('step_three_icon', 'fa fa-2x fa-lock') }}"></i></span>
                        <figcaption class="pt-3">
                            <h5 class="title">{{ setting('step_three_title', 'High secured') }}</h5>
                            <p>{{ setting('step_three_description','A high secured strategy is made to help explain to all concerned') }}
                            </p>
                        </figcaption>
                    </figure> <!-- iconbox // -->
                </div> <!-- col // -->
            </div>
        </article>

    </div> <!-- container .//  -->
</section>


<!-- ========================= SECTION CONTENT ========================= -->
<section class="section-content">
    <div class="container">
        <header class="section-heading">
            <h3 class="section-title">{{ __('Best Selling Shops') }}</h3>
        </header>
        <div class="row">
            @if(!blank($bestSellingShops))
                @foreach($bestSellingShops as $bestSellingShop)
                    <div class="col-md-3 width-resize">
                        <a href="{{ route('shop.show', [$bestSellingShop->shop->slug]) }}" class="card card-product-grid card-padding-css">
                            <div class="img-wrap home">
                                <img src="{{ $bestSellingShop->shop->images  }}" alt="{{ $bestSellingShop->shop->slug }}">
                            </div>
                            <div class="info-wrap text-center ">
                                <p class="title text-truncate">{{  $bestSellingShop->shop->name }}</p>
                            </div>
                        </a> <!-- card // -->
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

@if(!blank($BestSellingCategories))
<section class="section-content">
    <div class="container">
        <header class="section-heading">
            <h3 class="section-title">{{ __('Best Selling Categories') }}</h3>
        </header>
        <div class="row">
            @foreach($BestSellingCategories as $BestSellingCategory)
                <div class="col-md-3 width-resize">
                    <a href="{{ route('search-product', [$BestSellingCategory->shop]) }}"
                        class="card card-product-grid card-padding-css">
                        <div class="img-wrap home">
                            <img src="{{ $BestSellingCategory->category->images  }}"
                                alt="{{ $BestSellingCategory->category->name}}">
                        </div>
                        <div class="info-wrap text-center ">
                            <p class="title text-truncate">{{  $BestSellingCategory->category->name }}</p>
                        </div>
                    </a> <!-- card // -->
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif



@if(!blank($BestSellingProducts))
<section class="section-content">
    <div class="container">
        <header class="section-heading">
            <h3 class="section-title">{{ __('Best Selling Products') }}</h3>
        </header>
        <div class="row">
            @foreach($BestSellingProducts as $BestSellingProduct)
            <div class="col-md-3 width-resize">
                <a href=" {{ route('shop.product.details', [$BestSellingProduct->shop->slug, $BestSellingProduct->product->slug]) }}"
                    class="card card-product-grid card-padding-css">
                    <div class="img-wrap home">
                        <img src="{{ $BestSellingProduct->product->images }}">
                    </div>
                    <div class="info-wrap text-center">
                        <p class="title text-truncate mb-2">{{ $BestSellingProduct->product->name }}</p>
                        @if($BestSellingProduct->shop->avgRating($BestSellingProduct->shop->id,$BestSellingProduct->product->id)['avgRating']
                        !=0)
                        <ul class="rating-stars">
                            <li class="stars-active">
                                @for($i =0; $i <$BestSellingProduct->
                                    shop->avgRating($BestSellingProduct->shop->id,$BestSellingProduct->product->id)['avgRating'];
                                    $i++)
                                    <i class="fa fa-star"></i>
                                    @endfor
                            </li>
                            <li>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </li>
                        </ul>
                        @else
                        <div class="star-css"></div>
                        @endif

                        @if($BestSellingProduct->discount_price >= 1)
                            <div class="price-wrap mt-2 text-center">
                                <del class="price price-old">{{ currencyFormat($BestSellingProduct->unit_price) }}</del>
                            </div>
                        @endif

                        <div class="price-wrap mt-2 text-center">
                            <span
                                class="price">{{ currencyFormat($BestSellingProduct->unit_price - $BestSellingProduct->discount_price ) }}</span>
                        </div>

                        @if($BestSellingProduct->discount_price == '' || $BestSellingProduct->discount_price == 0)
                            <div class="price-wrap mt-2 text-center">
                                <del class="price price-old"></del>
                            </div>
                        @endif
                    </div>
                </a> <!-- card // -->
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


<section class="section-content">
    <div class="container">
        <header class="section-heading">
            <h3 class="section-title">{{ __('Latest products') }}</h3>
        </header>
        <div class="row">
            @if(!blank($shopProducts))
            @foreach($shopProducts as $shopProduct)
            <div class="col-md-3 width-resize">
                <a href=" {{ route('shop.product.details', [$shopProduct->shop->slug, $shopProduct->product->slug]) }}"
                    class="card card-product-grid card-padding-css">
                    <div class="img-wrap home">
                        <img src="{{ $shopProduct->product->images }}">
                    </div>
                    <div class="info-wrap text-center">
                        <p class="title text-truncate mb-2 ">{{ $shopProduct->product->name }}</p>
                        @if($shopProduct->shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['avgRating']
                        !=0)
                        <ul class="rating-stars">
                            <li class="stars-active">
                                @for($i =0; $i <$shopProduct->
                                    shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['avgRating'];
                                    $i++)
                                    <i class="fa fa-star"></i>
                                    @endfor
                            </li>
                            <li>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </li>
                        </ul>
                        @else
                            <div class="star-css"></div>
                        @endif

                        @if($shopProduct->discount_price >= 1)
                            <div class="price-wrap mt-2 text-center">
                                <del class="price price-old">{{ currencyFormat($shopProduct->unit_price) }}</del>
                            </div>
                        @endif

                        <div class="price-wrap mt-2 text-center">
                            <span
                                class="price">{{ currencyFormat($shopProduct->unit_price - $shopProduct->discount_price ) }}</span>
                        </div>

                        @if($shopProduct->discount_price == '' || $shopProduct->discount_price == 0)
                            <div class="price-wrap mt-2 text-center">
                                <del class="price price-old"></del>
                            </div>
                        @endif
                    </div>
                </a> <!-- card // -->
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>
@endsection


@section('style')
<link href="{{ asset('frontend/plugins/slickslider/slick.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('frontend/plugins/slickslider/slick-theme.css') }}" rel="stylesheet" type="text/css" />
@endsection
