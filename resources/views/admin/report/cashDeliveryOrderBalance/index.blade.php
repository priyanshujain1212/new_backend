@extends('admin.layouts.master')

@section('main-content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('Cash On delivery Order Balance Report') }}</h1>
            {{ Breadcrumbs::render('cash-on-delivery-order-balance-report') }}
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <form action="<?=route('admin.cash-on-delivery-order-balance-report.index')?>" method="POST">
                        @csrf
                        <div class="row">
                                <div class="col-sm-3">
                                <div class="form-group">
                                    <label>{{ __('Delivery Boy') }}</label>
                                    <select name="user_id" id="users" class="form-control @error('user_id') is-invalid @enderror select2">
                                        <option value="">{{ __('Select delivery boy') }}</option>
                                        @if(!blank($users))
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ (old('user_id', $set_user_id) == $user->id) ? 'selected' : '' }}>{{ $user->name }} @if($user->phone) ({{ $user->phone }}) @endif</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('user_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="">&nbsp;</label>
                                <button class="btn btn-primary form-control" type="submit">{{ __('Get Report') }}</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            @if($showView)
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Cash On delivery Order Balance Report') }}</h5>
                        <button class="btn btn-success btn-sm report-print-button" onclick="printDiv('printablediv')">{{ __('Print') }}</button>
                    </div>
                    <div class="card-body" id="printablediv">
                        @if(!blank($deliveryBoyAccounts))
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Phone') }}</th>
                                            <th>{{ __('Delivery Commission') }}</th>
                                            <th>{{ __('Order Amount') }}</th>

                                        </tr>
                                        @php $i=0; @endphp
                                        @foreach($deliveryBoyAccounts as $deliveryBoyAccount)
                                            <tr>
                                                <td>{{ $i+=1 }}</td>
                                                <td>{{ $deliveryBoyAccount->user->name }}</td>
                                                <td>{{ $deliveryBoyAccount->user->phone }}</td>
                                                <td>{{ currencyFormat($deliveryBoyAccount->delivery_charge > 0 ? $deliveryBoyAccount->delivery_charge : 0 ) }}</td>
                                                <td>{{ currencyFormat($deliveryBoyAccount->balance > 0 ? $deliveryBoyAccount->balance : 0 ) }}</td>
                                            </tr>
                                        @endforeach
                                    </thead>
                                </table>
                            </div>
                        @else
                            <h4 class="text-danger">{{ __('The Cash On delivery Order Balance Report data not found') }}</h4>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/cashDeliveryOrderBalance/index.js') }}"></script>
@endsection
