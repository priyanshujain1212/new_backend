@extends('frontend.layouts.default')
@section('frontend.content')

<div id="invoice-print">
<article class="card">
    <header class="card-header"> {{__('My Orders / Tracking')}} </header>
    <div class="card-body">
        <h6>{{ __('Order') }} #{{ $order->order_code }}  <span class="float-right"><strong > {{ __('Order Date : ') }} </strong>{{ $order->created_at->format('d M Y, h:i A') }}</span></h6>
        <article class="card">
            <div class="card-body row no-gutters">
                <div class="col">
                    <strong>{{ __('Billing To') }}:</strong>
                    <br> {{__('Name')}}:  {{ $order->user->name ?? '' }}
                    <br> {{__('Phone')}}:  {{ $order->user->phone ?? '' }}
                    <br> {{__('Address')}}:  {{ $order->user->address ?? '' }}<br>
                </div>
                <div class="col">
                    <strong>{{__('Shipping To:')}}</strong>
                    <br> {{__('Phone')}}:  {{ $order->mobile ?? '' }}
                    <br> {{__('Address')}}:  {{ $order->address ?? '' }}
                </div>
                <div class="col">
                    <strong>{{__('Status:')}}</strong>
                    <br> {{__('Payment Status')}}:  {{ trans('payment_status.' . $order->payment_status) ?? null }}
                    <br> {{__('Payment Method')}}:  {{  trans('payment_method.' . $order->payment_method) ?? null }}<br>
                </div>
            </div>
        </article>

        <div class="tracking-wrap">
            @if($order->status == \App\Enums\OrderStatus::CANCEL)
                <div class="step active">
                    <span class="icon"> <i class="fa fa-times"></i> </span>
                    <span class="text">{{__('Order Cancel')}}</span>
                </div>
            @else
                <div class="step {{ $order->status >= \App\Enums\OrderStatus::PENDING ? 'active' : ''}}">
                    <span class="icon"> <i class="fa fa-circle-notch"></i> </span>
                    <span class="text">{{__('Order Pending')}}</span>
                </div>
            @endif

            @if($order->status == \App\Enums\OrderStatus::REJECT)
                <div class="step active">
                    <span class="icon"> <i class="fa fa-times"></i> </span>
                    <span class="text">{{__('Order Reject')}}</span>
                </div>
            @else
                <div class="step {{ $order->status >= \App\Enums\OrderStatus::ACCEPT ? 'active' : ''}}">
                    <span class="icon"> <i class="fa fa-check"></i> </span>
                    <span class="text">{{__('Order Accept')}}</span>
                </div>
            @endif


            <div class="step {{  $order->status >= \App\Enums\OrderStatus::PROCESS ? 'active' : ''}}">
                <span class="icon"> <i class="fa fa-shopping-bag"></i> </span>
                <span class="text">{{__('Order Process')}}</span>
            </div> <!-- step.// -->
            <div class="step {{  $order->status >= \App\Enums\OrderStatus::ON_THE_WAY ? 'active' : ''}}">
                <span class="icon"> <i class="fa fa-truck"></i> </span>
                <span class="text"> {{__('On The Way')}} </span>
            </div> <!-- step.// -->
            <div class="step {{  $order->status == \App\Enums\OrderStatus::COMPLETED ? 'active' : ''}}">
                <span class="icon"> <i class="fa fa-box"></i> </span>
                <span class="text">{{__('Order Completed')}}</span>
            </div> <!-- step.// -->
        </div>

        <hr>
        <ul class="row">
            @foreach($order->items as $item)
            <li class="col-md-4">
                <figure class="itemside  mb-3">
                    <div class="aside"><img src="{{ $item->product->images }}" class="img-sm border"></div>
                    <figcaption class="info align-self-center">
                        <p class="title">{{$item->product->name}}</p>
                        <span class="text-muted">{{ currencyFormat($item->unit_price) }} </span>
                    </figcaption>
                </figure>
            </li>
            @endforeach
        </ul>
    </div> <!-- card-body.// -->
</article>
<article class="card mt-3">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status')}}
                </div>
            @endif

            <div class="tab-content" id="v-pills-tabContent">
                <table class="table">
                    <thead style="background-color:#3167eb">
                    <tr style="color:white">
                        <th>{{ __('Item') }}</th>
                        <th class="text-center">{{ __('Price') }}</th>
                        <th class="text-center">{{ __('Quantity') }}</th>
                        <th class="text-right">{{ __('Totals') }}</th>
                    </tr>
                    </thead>
                    @foreach($order->items as $item)
                        <tbody>
                        <tr>
                            <th scope="row">{{ $item->product->name }}</th>
                            <td class="text-center">{{ currencyFormat($item->unit_price) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ currencyFormat($item->item_total) }}</td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>

                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <th class="text-right">{{ __('Subtotal') }}</th>
                    </tr>
                    <tr>
                        <th scope="col"> {{ __('Order Status') }} : {{ trans('order_status.'.$order->status) }}</th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <td class="text-right">{{ currencyFormat($order->sub_total) }}</td>
                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <th class="text-right">{{ __('Delivery Charge') }}</th>
                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <td class="text-right">{{ currencyFormat($order->delivery_charge) }}</td>
                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <th class="text-right">{{ __('Total') }}</th>
                    </tr>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th class="text-right"></th>
                        <td class="text-right">{{ currencyFormat($order->total) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div> <!-- card-body .// -->
    </article> <!-- card.// -->
<!-- container .//  -->
</div>
<div class="container">
    <div class="row">
        @if($order->status == \App\Enums\OrderStatus::PENDING)
            <div class="col">
                <a href="{{ route('account.order.cancel', $order) }}" class="btn btn-danger m-2"
                   onclick="return confirm('You are about to cancel the order. This cannot be undo. are you sure?')"><i
                        class="fa fa-times"></i> {{ __('Cancel Order') }}</a>
            </div>
        @endif

        @if($order->attachment)
            <div class="text-right">
                <a class="btn btn-info m-2" href="{{ route('account.order.file', $order->id) }}"><i
                        class="fa fa-arrow-circle-down"></i> {{ __('Download') }}</a>
            </div>
        @endif
        <div class="@if(!$order->attachment) col @endif text-right">
            <button onclick="printDiv('invoice-print')" class="btn btn-warning m-2"><i
                    class="fa fa-print"></i> {{ __('Print') }}</button>
        </div>
    </div>
</div>
@endsection
