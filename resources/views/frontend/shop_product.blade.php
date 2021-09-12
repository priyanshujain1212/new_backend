@extends('layouts.app')

@section('main-content')
    <section class="section-content padding-y bg">
        <div class="container">
            <div class="card">
                <form action="<?=route('cart.store')?>" method="POST">
                    @csrf
                    <div class="row no-gutters">
                        <aside class="col-sm-6 border-right">
                            <article class="gallery-wrap">
                                <div class="img-big-wrap">
                                    <a href="#">
                                        <img id="mainthumnail" src="{{ $shopProduct->product->images }}">
                                    </a>
                                </div> <!-- img-big-wrap.// -->
                                @if(!blank($shopProduct->product->thumimages))
                                    <div class="thumbs-wrap">
                                        @foreach($shopProduct->product->thumimages as $thumimage)
                                            <a href="#" class="item-thumb"> <img
                                                    onclick="event.preventDefault();myFunction(this);"
                                                    src="{{ $thumimage }}"></a>
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        </aside>
                        <main class="col-sm-6">
                            <article class="content-body">
                                <h3 class="title">{{ $shopProduct->product->name }}</h3>
                                @if($avgRating !=0)
                                <div class="rating-wrap mb-2">
                                    <span class="badge badge-warning"> <i class="fa fa-star"></i>{{$avgRating}}</span>
                                    <small class="text-muted ml-2">{{$countUser}} {{__('reviews')}}</small>
                                </div>
                                @endif
                                <div class="mb-3">
                                    <a href="{{ route('shop.show', $shopProduct->shop) }}"
                                       class="text-success"> <i class="fas fa-store"></i>
                                        {{ $shopProduct->shop->name }} </a>
                                </div>
                                <p>{!! $shopProduct->product->description !!} </p>
                                @if(!blank($shopProduct->product_variations))
                                    <div class="item-option-select">
                                        <h6>{{ __('Variations') }}</h6>
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            @foreach($shopProduct->product_variations as $productvariation)
                                                <label data-price="{{ $productvariation->price - $productvariation->discount_price }}"
                                                       class="btn btn-light variations {{ ($productvariation->price == $shopProduct->unit_price) ? 'active' : '' }} ">
                                                    <input type="radio" value="{{ $productvariation->id }}"
                                                           name="variations" {{ ($productvariation->price == $shopProduct->unit_price) ? 'checked' : '' }} />
                                                    {{ $productvariation->name }} -
                                                    {{ currencyFormat($productvariation->price - $productvariation->discount_price) }} </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!blank($shopProduct->product_options))
                                    <div class="options">
                                        <h6>{{ __('Options') }}</h6>
                                        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                            @foreach($shopProduct->product_options as $product_option)
                                                <label data-price="{{ $product_option->price }}" class="btn btn-light options">
                                                    <input type="checkbox" value="{{ $product_option->id }}" name="options[]" />
                                                    {{ $product_option->name }} -
                                                    {{ currencyFormat($product_option->price) }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <input type="hidden" value="{{ $shopProduct->id }}" name="shop_product_id">

                                <div class="row mt-3 align-items-center">
                                    <div class="col">
                                        <span class="price h4" id="productPrice" data-unit-price="{{ $shopProduct->unit_price - $shopProduct->discount_price }}" data-currency-code="{{ setting('currency_code') }}">{{ currencyFormat($shopProduct->unit_price - $shopProduct->discount_price) }}</span>
                                    </div> <!-- col.// -->
                                    <div class="col text-right">
                                        <button  class="btn  btn-primary cartProtect" type="submit" data-shop_id="{{ $shopProduct->shop_id }}"> <span class="text">{{ __('Add to cart') }}</span> <i class="icon fas fa-shopping-cart"></i>  </button>
                                    </div> <!-- col.// -->
                                </div> <!-- row.// -->

                            </article> <!-- product-info-aside .// -->
                        </main> <!-- col.// -->
                    </div> <!-- row.// -->
                </form>
            </div> <!-- card.// -->
            <br>
            @if(!blank($shopRelatedProduct))
            <div class="card card-body">
                <div class="row">
                    @foreach($shopRelatedProduct as $reletedProduct)
                        <div class="col-md-3">
                            <a href="{{ route('shop.product.details', [$reletedProduct->shop->slug, $reletedProduct->product->slug]) }}">
                                <figure class="itemside mb-4">
                                    <div class="aside"><img src="{{ $reletedProduct->product->images}}" class="border img-sm"></div>
                                    <figcaption class="info align-self-center">
                                        <a href="{{ route('shop.product.details', [$reletedProduct->shop->slug, $reletedProduct->product->slug]) }}" class="title">{{ $reletedProduct->product->name }}</a>
                                        <a href="{{ route('shop.product.details', [$reletedProduct->shop->slug, $reletedProduct->product->slug]) }}" class="btn btn-light text-primary btn-sm"> {{ currencyFormat($reletedProduct->unit_price - $reletedProduct->discount_price) }} </a>
                                    </figcaption>
                                </figure>
                            </a>
                        </div> <!-- col.// -->
                    @endforeach
                </div> <!-- row.// -->
            </div> <!-- card // -->
            @endif
            <br>

            @if(!blank($ratingArray))
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <header class="section-heading">
                                <h3>{{__('Review')}} </h3>
                                <div class="rating-wrap">
                                    <ul class="rating-stars stars-lg">

                                        <li  class="stars-active">
                                            @for($i =0; $i <$avgRating; $i++)
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
                                    <strong class="label-rating text-lg"> {{$avgRating}} <span class="text-muted">| {{$countUser}} {{__('reviews')}}</span></strong>
                                </div>
                            </header>

                            @foreach($ratingArray as $rating)
                                <article class="box mb-3">
                                    <div class="icontext w-100">
                                        <img src="{{ $rating->user->images }}" class="img-xs icon rounded-circle">
                                        <div class="text">
                                            <span class="date text-muted float-md-right">  {{ $rating->updated_at->format('d M Y, h:i A') }} </span>
                                            <h6 class="mb-1">{{$rating->user->name}} </h6>
                                            <ul class="rating-stars">

                                                <li  class="stars-active">
                                                    @for($i =0; $i < $rating->rating; $i++)
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
                                        </div>
                                    </div> <!-- icontext.// -->
                                    <div class="mt-3">
                                        <p>
                                            {{ $rating->review }}
                                        </p>
                                    </div>
                                </article>
                            @endforeach
                        </div> <!-- col.// -->
                    </div> <!-- row.// -->
                </div> <!-- card // -->
            @endif
        </div>
    </section>
@endsection

@section('footer-js')
    <script type="text/javascript">
        var session_cart_shop_id = {{ session('session_cart_shop_id') ?? 0 }};
        $('.cartProtect').on('click', function(event) {
            var current_shop_id = $(this).data('shop_id');
            if(session_cart_shop_id != 0 && (session_cart_shop_id != current_shop_id)) {
                var returnMsg = confirm("If you add this product on your cart then your other cart item will be destroyed be because you are in other shop.");
                if(!returnMsg) {
                    event.preventDefault();
                }
            }
        });
    </script>
@endsection
