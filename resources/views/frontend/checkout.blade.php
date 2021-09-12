@extends('layouts.app')

@section('main-content')
	<section class="section-content padding-y bg">
		<div class="container">
            <div class="row">
                <main class="col-md-8">
                    <article class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Review cart</h4>
                            <div class="row">
                                @if(!blank(Cart::content()))
                                    @foreach(Cart::content() as $content)
                                <div class="col-md-6">
                                    <figure class="itemside  mb-4">
                                        <div class="aside"><img src="{{ $content->options->images }}" class="border img-sm"></div>
                                        <figcaption class="info">
                                            <p>{{ $content->name }}</p>
                                            <p class="text-muted small">
                                                @if(isset($content->options->variation['name']) && isset($content->options->variation['price']))
                                                    Variation: {{ $content->options->variation['name'] }} - {{ currencyFormat($content->options->variation['price']) }}
                                                    <br>
                                                @endif

                                                @if(!blank($content->options->options))
                                                    Options <br/>
                                                    @foreach($content->options->options as $option)
                                                        {!!  $option['name'] .' - '. currencyFormat($option['price']) . '<br/>' !!}
                                                    @endforeach
                                                @endif
                                            </p>
                                            <span class="text-muted">{{$content->qty}} x {{ currencyFormat($content->price) }}  = {{ currencyFormat($content->price * $content->qty) }} </span>
                                        </figcaption>
                                    </figure>
                                </div>
                                @endforeach
                            @endif<!-- col.// -->
                            </div> <!-- row.// -->
                        </div> <!-- card-body.// -->
                    </article> <!-- card.// -->


                    <article class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{__('Contact info')}}</h4>
                            <form id="payment-form" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label>{{ __('Phone') }}</label> <span class="text-danger">*</span>
                                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" placeholder="{{ __('Phone') }}" name="mobile" value="{{ old('mobile') }}">
                                        @error('mobile')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>{{ __('Attachment') }}</label> @if(setting('order_attachment_checking') == \App\Enums\OrderAttachmentCheckingStatus::ENABLE) <span class="text-danger">*</span> @endif
                                        <div class="custom-file form-control @error('attachment') is-invalid @enderror">
                                            <input name="attachment" type="file" class=" " id="customFile" onchange="readURL(this);">
                                            <label class="custom-file-label" for="customFile"> {{ __('Choose file') }}</label>
                                        </div>

                                        @error('attachment')
                                        <div class="invalid-feedback">
                                            {{ $errors->first('attachment') }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label>{{ __('Delivery Address') }}</label> <span class="text-danger">*</span>
                                        <textarea name="address" cols="30" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="{{ __('Address') }}">{{ old('address') }}</textarea>
                                        @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>{{ __('Payment Type') }}</label> <span class="text-danger">*</span>
                                        <select name="payment_type" id="payment_type" class="form-control @error('payment_type') is-invalid @enderror ">
                                            <option value="{{ App\Enums\PaymentMethod::CASH_ON_DELIVERY }}" @if (old('payment_type') == App\Enums\PaymentMethod::CASH_ON_DELIVERY) selected="selected" @endif>{{ __('Cash on delivery') }}</option>
                                            @if(auth()->user()->balance->balance >= (Cart::totalFloat() + $shop->delivery_charge))
                                                <option value="{{ App\Enums\PaymentMethod::WALLET }}" @if (old('payment_type') == App\Enums\PaymentMethod::WALLET) selected="selected" @endif>{{ __('Pay with credit balance ') . currencyFormatWithName(auth()->user()->balance->balance) }}</option>
                                            @endif
                                            <!-- @if(setting('stripe_key') && setting('stripe_secret'))
                                                <option value="{{ App\Enums\PaymentMethod::STRIPE }}" @if (old('payment_type') == App\Enums\PaymentMethod::STRIPE) selected="selected" @endif>{{ __('Stripe') }}</option>
                                            @endif -->
                                            @if(setting('paytm_merchant_ID') && setting('paytm_merchant_key'))
                                                <option value="{{ App\Enums\PaymentMethod::PAYTM }}" @if (old('payment_type') == App\Enums\PaymentMethod::PAYTM) selected="selected" @endif>{{ __('Paytm') }}</option>
                                            @endif
                                        </select>
                                        @error('payment_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div> <!-- row.// -->
                                <div class="form-group stripe-payment-method-div">
                                    <label>{{ __('Credit or debit card') }}</label> <span class="text-danger">*</span>
                                    <div id="card-element"></div>
                                    <div id="card-errors" class="text-danger" role="alert"></div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block" @if(Cart::totalFloat() <= 0) disabled @endif> {{ __('Submit Order') }}</button>
                                </div>
                            </form>
                        </div> <!-- card-body.// -->
                    </article> <!-- card.// -->
                </main> <!-- col.// -->
                <aside class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h4 class="mb-3">{{__('Overview')}}</h4>
                            <dl class="dlist-align">
                                <dt class="text-muted">{{ __('Total price') }}: </dt>
                                <dd class="text-right"> {{ currencyFormat(Cart::totalFloat()) }}</dd>
                            </dl>
                            <dl class="dlist-align">
                                <dt class="text-muted">{{ __('Delivery Charge') }}: </dt>
                                <dd class="text-right"> {{ currencyFormat($shop->delivery_charge ?? 0) }}</dd>
                            </dl>
                            <dl class="dlist-align">
                                <dt class="text-muted">{{ __('Discount') }}: </dt>
                                <dd class="text-right"> {{__('- $0.00')}}</dd>
                            </dl>
                            <hr>
                            <dl class="dlist-align">
                                <dt>{{ __('Total') }}:</dt>
                                <dd class="h5 text-right">{{ currencyFormat(Cart::totalFloat() + $shop->delivery_charge) }}</dd>
                            </dl>
                            <hr>
                        </div> <!-- card-body.// -->
                    </div> <!-- card.// -->
                </aside> <!-- col.// -->
            </div>
		</div>
	</section>
@endsection

@section('footer-js')
	<script>
    	const stripeKey = "{{ setting('stripe_key') }}";
        const paytm_merchant_ID = "{{ setting('paytm_merchant_ID') }}"
        const paytm_merchant_key = "{{ setting('paytm_merchant_key') }}"
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('frontend/js/checkout/stripe.js') }}"></script>
    <script src="{{ asset('frontend/js/image-upload.js') }}"></script>
@endsection
