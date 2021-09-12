@extends('admin.layouts.master')

@section('main-content')

<section class="section">
    <div class="section-header">
        <h1>{{ __('Request Withdraw') }}</h1>
        {{ Breadcrumbs::render('request-withdraw/add') }}
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <form action="{{ route('admin.request-withdraw.store') }}" method="POST" autocomplete="on">
                        @csrf
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col">
                                    <label>{{ __('Amount') }}</label> <span class="text-danger">*</span>
                                    <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" autocomplete="off">
                                    @error('amount')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label>{{ __('Date') }}</label> <span class="text-danger">*</span>
                                    <input type="text" autocomplete="off" name="date" class="form-control datepicker @error('date') is-invalid @enderror" value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-primary mr-1" type="submit">{{ __('levels.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
    <script src="{{ asset('js/requestwithdraw/create.js') }}"></script>
@endsection
