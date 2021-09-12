@extends('layouts.app')

@section('search')
    <div class="col-lg-6 col-12 col-sm-12">
        <x-search-params/>
    </div>
@endsection

@section('main-content')
    <section class="section-pagetop bg">
        <div class="container">
            <div class="row ">
                <div class="col-auto d-none d-lg-block">
                    <img src="{{ $shop->images }}" alt="{{ $shop->slug }}"
                         class="bd-placeholder-img" width="150" height="150">
                </div>
                <div class="col p-4 d-flex flex-column position-static">
                    <strong class="d-inline-block mb-2 text-primary">{{ $shop->location->name }}, {{ $shop->area->name }}</strong>
                    <h3 class="mb-0">{{ $shop->name }}</h3>
                    <div class="mb-1 text-bold">{{ $shop->address }}</div>
                    <div class="mb-1 text-muted">{{ $shop->created_at->diffForHumans() }}</div>
                    <div class="mb-1 text-muted">{{ __('Opens at '). date('h:i A', strtotime($shop->opening_time)) .' - '. __('Closed at ') . date('h:i A', strtotime($shop->closing_time))  }}</div>
                    <p class="card-text mb-auto">{{ Str::limit($shop->description, $limit = 100, $end = '...') }}</p>
                </div>

            </div>
        </div> <!-- container //  -->
    </section>

    <section class="section-content padding-y">
        <div class="container">

            <div class="row">
                <x-product-filter shop="{{$shop->slug}}" :products="$products" />
                <main class="col-md-9">

                    <header class="border-bottom mb-4 pb-3">
                        <div class="form-inline">
                            <span class="mr-md-auto">{{ $products->count() }} {{ __('Items found') }} </span>
                        </div>
                    </header>

                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-3 width-resize">
                                <a href="{{ route('shop.product.details', [$shop, $product]) }}" class="card card-product-grid card-padding-css">
                                    <div class="img-wrap shop">
                                        <img src="{{ $product->images }}">
                                    </div>
                                    <div class="info-wrap text-center ">
                                        <p class="title text-truncate">{{ $product->name }}</p>
                                        @if($shop->avgRating($shop->id,$product->id)['avgRating'] != 0)
                                            <ul class="rating-stars">
                                                <li  class="stars-active">
                                                    @for($i =0; $i < $shop->avgRating($shop->id,$product->id)['avgRating']; $i++)
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

                                        @php $productAmount = $productPrices[$product->id] - $productDiscountPrices[$product->id]; @endphp
                                        @if($productDiscountPrices[$product->id] > 0)
                                            <div class="price-wrap mt-2 text-center">
                                                <del class="price price-old">{{ currencyFormat($productPrices[$product->id]) }}</del>
                                            </div>
                                        @endif

                                        <div class="price-wrap mt-2 text-center">
                                            <span class="price">{{ currencyFormat($productAmount) }}</span>
                                        </div>

                                        @if($productDiscountPrices[$product->id] == '' || $productDiscountPrices[$product->id] == 0)
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
                        {{ $products->appends($_GET)->links() }}
                    </nav>
                </main> <!-- col.// -->

            </div>

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
