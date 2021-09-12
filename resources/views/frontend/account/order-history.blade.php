@extends('frontend.layouts.default')
@section('frontend.content')
<article class="card mb-3">
    <div class="card-body">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status')}}
            </div>
        @endif
            <div class="table-responsive">
                <table class="table table-striped" id="maintable" data-url="{{ route('account.get-order') }}">
                    <thead>
                    <tr>
                        <th>{{ __('Order Code') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Payment Status') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
    </div> <!-- card-body .// -->
</article> <!-- card.// -->
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/datatables.net-select-bs4/css/select.bootstrap4.min.css') }}">
@endsection
@section('footer-js')
    <script src="{{ asset('assets/modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/frontend/order/index.js') }}"></script>
@endsection
