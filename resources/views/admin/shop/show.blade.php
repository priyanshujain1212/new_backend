@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Shops') }}</h1>
            {{ Breadcrumbs::render('shop/view') }}
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-plus-square"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Total Order') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $total_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="far fa-paper-plane"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Pending') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $pending_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="far fa-star"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Process') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $process_order }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('Order Completed') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $completed_order }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="card-body card-profile shop-edit-button">
                            <img class="profile-user-img img-responsive img-circle" src="{{ $shop->images }}" alt="User profile picture">
                            <h3 class="text-center">{{ $shop->name }}</h3>
                            <p class="text-center">
                                {{ $shop->address }}
                            </p>
                            @isset(auth()->user()->shop->id)
                                <a href="{{ route('admin.shop.shopedit', auth()->user()->shop->id) }}" class="btn btn-sm btn-icon btn-primary shop-edit-icon" data-toggle="tooltip" data-placement="top" data-original-title="Edit"> <i class="far fa-edit"></i></a>
                            @endisset
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body card-profile">
                            <img class="profile-user-img img-responsive img-circle" src="{{ $user->images }}" alt="User profile picture">
                            <h3 class="text-center">{{ $user->name }}</h3>
                            <p class="text-center">
                                {{ $user->roles->first()->name ?? '' }}
                            </p>

                            <ul class="list-group">
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Username') }}</span> <span class="float-right">{{ $user->name }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Phone') }}</span> <span class="float-right">{{ $user->phone }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Email') }}</span> <span class="float-right">{{ $user->email }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Address') }}</span> <span class="float-right profile-list-group-item-addresss">{{ $user->address }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Deposit Amount') }}</span> <span class="float-right profile-list-group-item-addresss">{{ isset($user->deposit->deposit_amount) ? currencyFormat($user->deposit->deposit_amount) : '' }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Limit Amount') }}</span> <span class="float-right profile-list-group-item-addresss">{{ isset($user->deposit->limit_amount) ? currencyFormat($user->deposit->limit_amount) : '' }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Credit') }}</span> <span class="float-right profile-list-group-item-addresss">{{ currencyFormat($user->balance->balance > 0 ? $user->balance->balance : 0 ) }}</span></li>
                                <li class="list-group-item profile-list-group-item"><span class="float-left font-weight-bold">{{ __('Status') }}</span> <span class="float-right profile-list-group-item-addresss">{{ $user->mystatus }}</span></li>
                            </ul>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

                <div class="col-8 col-md-8 col-lg-8">

                    <div class="card">
                        <div class="card-body">
                            <div class="profile-desc">
                                <div class="single-profile">
                                    <p><b>{{ __('Name') }}: </b> {{ $shop->name}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Location') }}: </b> {{ $shop->location->name ?? null}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Area') }}: </b> {{ $shop->area->name ?? null}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Delivery Charge') }}: </b> {{ currencyFormat($shop->delivery_charge) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Latitude') }}: </b> {{ $shop->lat}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Longitude') }}: </b> {{ $shop->long}}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Current Status') }}: </b> {{ trans('current_statuses.'.$shop->current_status) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Status') }}: </b> {{ trans('statuses.'.$shop->status) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Opening Time') }}: </b> {{ date('h:i A', strtotime($shop->opening_time)) }}</p>
                                </div>
                                <div class="single-profile">
                                    <p><b>{{ __('Closing Time') }}: </b> {{ date('h:i A', strtotime($shop->closing_time)) }}</p>
                                </div>
                                <div class="single-full-profile">
                                    <p><b>{{ __('Address') }}: </b> {{ $shop->address}}</p>
                                </div>
                                <div class="single-full-profile">
                                    <p><b>{{ __('Description') }}: </b> {!! $shop->description !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        @can('shop_create')
                            <div class="card-header">
                                <a href="{{ route('admin.shop.products.create', $shop) }}" class="btn btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> {{ __('Add Shop Product') }}
                                </a>
                            </div>
                        @endcan
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="maintable" data-shopid="{{ $shop->id }}" data-url="{{ route('admin.shop.get-shop-product') }}" data-hidecolumn="{{ auth()->user()->can('shop_edit') || auth()->user()->can('shop_delete') }}">
                                    <thead>
                                        <tr>
                                            <th>{{ __('levels.id') }}</th>
                                            <th>{{ __('levels.product') }}</th>
                                            <th>{{ __('levels.price') }}</th>
                                            <th>{{ __('levels.discount') }}</th>
                                            <th>{{ __('levels.quantity') }}</th>
                                            <th>{{ __('levels.actions') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection


@section('css')
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables.net-select-bs4/js/select.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/shop/product.js') }}"></script>
@endsection
