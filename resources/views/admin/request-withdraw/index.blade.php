@extends('admin.layouts.master')

@section('main-content')

<section class="section">
    <div class="section-header">
        <h1>{{ __('Request Withdraw') }}</h1>
        {{ Breadcrumbs::render('request-withdraw') }}
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @can('request-withdraw_create')
                        <div class="card-header">
                            <a href="{{ route('admin.request-withdraw.create') }}" class="btn btn-icon icon-left btn-primary"><i class="fas fa-plus"></i> {{ __('Add Request Withdraw') }}</a>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3">
                                <div class="input-group input-daterange" id="date-picker">
                                    <select class="form-control" id="status" name="status" id="">
                                        @foreach(trans('request_withdraw_statuses') as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="refresh">
                                            {{ __('Refresh') }}</button>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="date-search">{{ __('Search') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped" id="maintable"
                                data-url="{{ route('admin.request-withdraw.get-request-withdraw') }}"
                                data-status="{{ \App\Enums\RequestWithdrawStatus::PENDING }}"
                                data-hidecolumn="{{ auth()->user()->can('request-withdraw_edit') || auth()->user()->can('request-withdraw_delete')}}">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.name') }}</th>
                                        <th>{{ __('levels.amount') }}</th>
                                        <th>{{ __('levels.date') }}</th>
                                        <th>{{ __('levels.status') }}</th>
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
    <script src="{{ asset('js/requestwithdraw/index.js') }}"></script>
@endsection
