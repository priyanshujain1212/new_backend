@extends('layouts.app')

@section('main-content')
    <section class="section-content padding-y">
        <div class="container">

            <div class="row">
                <x-shop-filter/>
                <main class="col-md-9">
                    <header class="border-bottom mb-4 pb-3">
                        <div class="form-inline">
                            <span class="mr-md-auto">{{ $shops->total() }} {{__('Shop found ')}}</span>
                        </div>
                    </header>

                    <div class="row">
                        @foreach($shops as $shop)
                            <div class="col-md-3 width-resize">
                                <a href="{{ route('shop.show', [$shop->slug]) }}" class="card card-product-grid card-padding-css">
                                    <div class="img-wrap home">
                                        <img src="{{ $shop->images  }}" alt="{{ $shop->slug }}">
                                    </div>
                                    <div class="info-wrap text-center ">
                                        <p class="title text-truncate">{{  $shop->name }}</p>
                                    </div>
                                </a> <!-- card // -->
                            </div>
                        @endforeach
                    </div>

                    <nav class="mt-4" aria-label="{{ __('Page navigation sample') }}">
                        {{ $shops->appends($_GET)->links() }}
                    </nav>

                    <hr>

                    <header class="border-bottom mb-4 pb-3">
                        <div class="form-inline">
                            <span class="mr-md-auto">{{ $products ? $products->total() : 0 }} {{__('Items found ')}}</span>
                        </div>
                    </header>

                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-3 width-resize">
                                <a href=" {{ route('shop.product.details', [$product->shop, $product->product]) }}"
                                    class="card card-product-grid card-padding-css">
                                    <div class="img-wrap home">
                                        <img src="{{ $product->product->images }}">
                                    </div>
                                    <div class="info-wrap text-center">
                                        <p class="title text-truncate mb-2">{{ $product->product->name }}</p>
                                        @if($product->shop->avgRating($product->shop->id,$product->product_id)['avgRating']
                                        !=0)
                                        <ul class="rating-stars">
                                            <li class="stars-active">
                                                @for($i =0; $i < $product->shop->avgRating($product->shop_id,$product->product_id)['avgRating'];
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

                                        @if($product->discount_price >= 1)
                                            <div class="price-wrap mt-2 text-center">
                                                <del class="price price-old">{{ currencyFormat($product->unit_price) }}</del>
                                            </div>
                                        @endif

                                        <div class="price-wrap mt-2 text-center">
                                            <span
                                                class="price">{{ currencyFormat($product->unit_price - $product->discount_price ) }}</span>
                                        </div>

                                        @if($product->discount_price == '' || $product->discount_price == 0)
                                            <div class="price-wrap mt-2 text-center">
                                                <del class="price price-old"></del>
                                            </div>
                                        @endif
                                    </div>
                                </a> <!-- card // -->
                            </div>
                        @endforeach
                    </div>

                    <nav class="mt-4" aria-label="Page navigation sample">
                    </nav>
                    <nav class="mt-4" aria-label="{{ __('Page navigation sample') }}">
                        {{ $products ? $products->appends($_GET)->links() : '' }}
                    </nav>
                </main> <!-- col.// -->
            </div>
        </div>
    </section>
@endsection
