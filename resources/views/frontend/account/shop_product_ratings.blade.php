@extends('frontend.layouts.default')
@section('frontend.content')
    <div class="row">
        @if(!blank($shopProduct))
            <div class="col-md-6">
                <div class="card card-product-grid card-padding-css">
                    <div class="img-wrap">
                        <img src="{{ $shopProduct->product->images }}">
                    </div>
                    <div class="info-wrap text-center">
                        <p class="title text-truncate mb-2 ">{{ $shopProduct->product->name }}</p>
                        @if($shopProduct->shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['avgRating'] !=0)
                            <ul class="rating-stars">
                                <li  class="stars-active">
                                    @for($i =0; $i <$shopProduct->shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['avgRating']; $i++)
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
                            <small class="label-rating text-muted">{{$shopProduct->shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['avgRating']}} | {{$shopProduct->shop->avgRating($shopProduct->shop->id,$shopProduct->product->id)['countUser']}}  {{__('reviews')}}</small>
                        @else
                            <div class="star-css"></div>
                        @endif
                        <div class="mt-2">
                            <span class="price">{{ currencyFormat($shopProduct->unit_price - $shopProduct->discount_price ) }}</span>
                            @if($shopProduct->discount_price>=1)
                                <del class="price-old">{{ currencyFormat($shopProduct->unit_price) }}</del>
                            @endif
                        </div>
                    </div>
                </div> <!-- card // -->
            </div>
        @endif
        <div class="col-sm-6">
            <div class="card">
                <article class="card-body">
                    <header class="mb-4">
                        <h4 class="card-title">{{__('Ratings & Review')}}</h4>
                    </header>

                                <form action="{{ route('account.shop-product-ratings-update') }}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{$shopProduct->product->id}}">
                                    <input type="hidden" name="shop_id" value="{{$shopProduct->shop->id}}">
                                    <input type="hidden" name="status" value="5">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>{{__('Rating')}} </label> <span class="text-danger">*</span>
                                            <div class="container2">
                                                <div class="star-widget">
                                                    @php($rating = isset($shopProductRating->rating) ? $shopProductRating->rating :0)
                                                    <input type="radio" value="5" name="rating" {{ 5 == old('rating',$rating) ? 'checked' : ''}} id="rate-5">
                                                    <label for="rate-5"  class="fas fa-star"></label>
                                                    <input type="radio" value="4" name="rating" {{ 4 == old('rating',$rating) ? 'checked' : ''}} id="rate-4">
                                                    <label for="rate-4"  class="fas fa-star"></label>
                                                    <input type="radio" value="3" name="rating"  {{ 3 == old('rating',$rating) ? 'checked' : ''}} id="rate-3">
                                                    <label for="rate-3" class="fas fa-star"></label>
                                                    <input type="radio" value="2" name="rating" {{ 2 == old('rating',$rating) ? 'checked' : ''}} id="rate-2">
                                                    <label for="rate-2" class="fas fa-star"></label>
                                                    <input type="radio" value="1" name="rating" {{ 1 == old('rating',$rating) ? 'checked' : ''}} id="rate-1">
                                                    <label for="rate-1" class="fas fa-star"></label>
                                                </div>
                                            </div>
                                            @error('rating')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div id="reviewID" class="{{old('rating',$rating >0) ? '':'reviewID'}}">
                                        <div class="form-group">
                                            <label>{{ __('Review') }}</label>  <span class="text-danger">*</span>
                                            <textarea name="review" type="text" rows="10" aria-label="With textarea" placeholder="Write Review" class="form-control @if($errors->has('review')) is-invalid @endif">@if(isset($shopProductRating->review)){{ old('review',$shopProductRating->review)}}@else{{old('review')}}@endif</textarea>
                                            @if($errors->has('review'))
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('review') }}</strong>
                                    </span>
                                 @endif
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block"> {{__('Submit')}} </button>
                                        </div>
                                    </div><!-- form-group// -->
                                </form>
                </article>
            </div>
        </div>
    </div>
@endsection

@section('footer-js')
<script type="text/javascript">
    var session_cart_shop_id =  0;
    $('.cartProtect').on('click', function (event) {
        var current_shop_id = $(this).data('shop_id');
        if (session_cart_shop_id != 0 && (session_cart_shop_id != current_shop_id)) {
            var returnMsg = confirm(
                "If you add this product on your cart then your other cart item will be destroyed be because you are in other shop."
            );
            if (!returnMsg) {
                event.preventDefault();
            }
        }
    });

</script>
<script src="{{ asset('js/frontend/StarRating.js') }}"></script>

@endsection
