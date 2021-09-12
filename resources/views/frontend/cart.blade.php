@extends('layouts.app')

@section('main-content')
	<section class="section-content padding-y bg">
		<div class="container">
			<div class="row">
			    <aside class="col-lg-9">
			        <div class="card">
			            <table class="table table-borderless table-shopping-cart table-responsive">
			                <thead class="text-muted">
			                    <tr class="small text-uppercase">
			                        <th scope="col">{{ __('Product') }}</th>
			                        <th scope="col" width="120">{{ __('Quantity') }}</th>
			                        <th scope="col" width="120">{{ __('Price') }}</th>
			                        <th scope="col" class="text-right" width="200"></th>
			                    </tr>
			                </thead>
			                <tbody>

			                	@if(!blank(Cart::content()))
									@foreach(Cart::content() as $content)
					                    <tr>
					                        <td>
					                            <figure class="itemside align-items-center">
					                                <div class="aside"><img src="{{ $content->options->images }}" class="img-sm" /></div>
					                                <figcaption class="info">
					                                    <a href="#" class="title text-dark">{{ $content->name }}</a>
					                                    <p class="text-muted small">
                                                            @if(isset($content->options->variation['name']) && isset($content->options->variation['price']))
                                                                Variation:
                                                                <br>
                                                                {{ $content->options->variation['name'] }}
                                                                - {{ currencyFormat($content->options->variation['price']) }}
                                                                <br>
                                                            @endif

                                                            @if(!blank($content->options->options))
                                                                Options<br>
                                                                @foreach ($content->options->options as $option)
                                                                    {{ $option['name'] .' - '. currencyFormat($option['price']) }}
                                                                    <br>
                                                                @endforeach
                                                            @endif
                                                            <br>
					                                	</p>
					                                </figcaption>
					                            </figure>
					                        </td>
					                        <td>
                                                <div class="input-group input-spinner size-change">
                                                    <div class="input-group-prepend">
                                                        <button class="btn btn-light quantity-change-btn" type="button" id="button-minus"> <i class="fa fa-minus"></i> </button>
                                                    </div>
                                                    <input type="text" class="form-control size-change quantity-change" id="{{ $content->rowId }}" value="{{ $content->qty }}">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-light quantity-change-btn" type="button" id="button-plus"> <i class="fa fa-plus"></i> </button>
                                                    </div>
                                                </div>
					                        </td>
					                        <td>
					                            <div class="price-wrap">
					                                <var class="price price-{{ $content->rowId }}">{{ currencyFormat($content->price * $content->qty) }}</var>
					                                <small class="text-muted"> {{ currencyFormat($content->price) }} each </small>
					                            </div>
					                            <!-- price-wrap .// -->
					                        </td>
					                        <td class="text-right">
					                            <a href="{{ route('cart.remove', $content->rowId) }}" class="btn btn-light"> {{ __('Remove') }}</a>
					                        </td>
					                    </tr>
					                @endforeach
					            @endif
			                </tbody>
			            </table>

			            <!-- <div class="card-body border-top">
			                <p class="icontext"><i class="icon text-success fa fa-truck"></i> {{ __('Free Delivery within 1-2 weeks') }}</p>
			            </div> -->
			            <!-- card-body.// -->
			        </div>
			        <!-- card.// -->
			    </aside>
			    <!-- col.// -->
			    <aside class="col-lg-3">
			        <div class="card">
			            <div class="card-body">
			                <dl class="dlist-align">
			                    <dt>{{ __('Total price') }}:</dt>
			                    <dd class="text-right total-price-js">{{ currencyFormat(Cart::totalFloat()) }}</dd>
			                </dl>
			                <dl class="dlist-align">
			                    <dt>{{ __('Delivery Charge') }}:</dt>
			                    <dd class="text-right">{{ currencyFormat($shop->delivery_charge ?? 0 ) }}</dd>
			                </dl>
			                <dl class="dlist-align">
			                    <dt>{{ __('Discount') }}:</dt>
			                    <dd class="text-right text-danger">- $0.00</dd>
			                </dl>
			                <dl class="dlist-align">
			                    <dt>{{ __('Total') }}:</dt>
			                    <dd class="text-right text-dark b total-js"><strong>{{ currencyFormat(Cart::totalFloat() + ($shop->delivery_charge ?? 0)) }}</strong></dd>
			                </dl>
			                <hr />
			                <p class="text-center mb-3">
			                    <img src="{{ asset('frontend/images/misc/payments.png') }}" height="26" />
			                </p>
			                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block @if(Cart::totalFloat() <= 0) disabled @endif"> {{ __('Make Purchase ') }}</a>
			                <a href="{{ !blank($shop) ? route('shop.show', $shop) : route('home') }}" class="btn btn-light btn-block">{{ __('Continue Shopping') }}</a>
			            </div>
			            <!-- card-body.// -->
			        </div>
			        <!-- card.// -->
			    </aside>
			    <!-- col.// -->
			</div>
		</div>
	</section>
@endsection

@section('footer-js')
	<script>
		const deliveryCharge 	= "{{ $shop->delivery_charge ?? 0 }}";
		const url 				= "{{ route('cart.quantity') }}";
	</script>
	<script src="{{ asset('frontend/js/cart/cart.js') }}"></script>
@endsection
