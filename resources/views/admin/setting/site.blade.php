@extends('admin.setting.index')

@section('admin.setting.breadcrumbs')
{{ Breadcrumbs::render('site-setting') }}
@endsection

@section('admin.setting.layout')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.site-update') }}"
                enctype="multipart/form-data">
                @csrf
                <fieldset class="setting-fieldset">
                    <legend class="setting-legend">{{ __('General Setting') }}</legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="site_name">{{ __('levels.site_name') }}</label>
                                <span class="text-danger">*</span>
                                <input name="site_name" id="site_name" type="text"
                                    class="form-control @error('site_name') is-invalid @enderror"
                                    value="{{ old('site_name', setting('site_name')) }}">
                                @error('site_name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label
                                    for="site_phone_number">{{ __('Site Phone Number') }}</label>
                                <span class="text-danger">*</span>
                                <input name="site_phone_number" id="site_phone_number" type="text"
                                    
                                    class="form-control @error('site_phone_number') is-invalid @enderror"
                                    value="{{ old('site_phone_number', setting('site_phone_number')) }}">
                                @error('site_phone_number')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="currency_name">{{ __('levels.currency_name') }}</label>
                                <span class="text-danger">*</span>
                                <input name="currency_name" id="currency_name" type="text"
                                    class="form-control @error('currency_name') is-invalid @enderror"
                                    value="{{ old('currency_name', setting('currency_name')) }}">
                                @error('currency_name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="site_footer">{{ __('levels.site_footer') }}</label> <span
                                    class="text-danger">*</span>
                                <input name="site_footer" id="site_footer"
                                    class="form-control @error('site_footer') is-invalid @enderror"
                                    value="{{ old('site_footer', setting('site_footer')) }}">
                                @error('site_footer')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="customFile">{{ __('levels.site_logo') }}</label>
                                <div class="custom-file">
                                    <input name="site_logo" type="file"
                                        class="file-upload-input custom-file-input @error('site_logo') is-invalid @enderror"
                                        id="customFile" onchange="readURL(this);">
                                    <label class="custom-file-label" for="customFile">{{ __('Choose
                                                                    file') }}</label>
                                </div>
                                @error('site_logo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                                @if(setting('site_logo'))
                                <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                    src="{{ asset('images/'.setting('site_logo')) }}"
                                    alt="{{ __('Food Express Logo') }}" />
                                @else
                                <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                    src="{{ asset('images/logo.png') }}" alt="{{ __('Food Express Logo') }}" />
                                @endif
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="site_email">{{ __('levels.site_email') }}</label> <span
                                    class="text-danger">*</span>
                                <input name="site_email" id="site_email"
                                    class="form-control @error('site_email') is-invalid @enderror"
                                    value="{{ old('site_email', setting('site_email')) }}">
                                @error('site_email')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="currency_code">{{ __('levels.currency_code') }}</label> <span
                                    class="text-danger">*</span>
                                <input name="currency_code" id="currency_code" type="text"
                                    class="form-control @error('currency_code') is-invalid @enderror"
                                    value="{{ old('currency_code', setting('currency_code')) }}">
                                @error('currency_code')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="timezone">{{ __('levels.timezone') }}</label> <span
                                    class="text-danger">*</span>
                                <?php
                                        $className = 'form-control';
                                        if($errors->first('timezone')) {
                                            $className = 'form-control is-invalid';
                                        }
                                        echo Timezonelist::create('timezone', setting('timezone') , ['class'=> $className]); ?>
                                @error('timezone')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="site_description">{{ __('levels.description') }}</label> <span
                                    class="text-danger">*</span>
                                <textarea name="site_description" id="site_description" cols="30" rows="3"
                                    class="form-control small-textarea-height @error('site_description') is-invalid @enderror">{{ old('site_description', setting('site_description')) }}</textarea>
                                @error('site_description')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="setting-fieldset">
                    <legend class="setting-legend">{{ __('Order Setting') }}</legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="order_commission_percentage">{{ __('levels.order_commission_percentage') }}</label>
                                <span class="text-danger">*</span>
                                <input name="order_commission_percentage" id="order_commission_percentage" type="number"
                                    min="0" max="100"
                                    class="form-control @error('order_commission_percentage') is-invalid @enderror"
                                    value="{{ old('order_commission_percentage', setting('order_commission_percentage')) }}">
                                @error('order_commission_percentage')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label
                                    for="delivery_boy_order_amount_limit">{{ __('levels.delivery_boy_order_amount_limit') }}</label>
                                <input name="delivery_boy_order_amount_limit" id="delivery_boy_order_amount_limit"
                                    type="number" min="0"
                                    class="form-control @error('delivery_boy_order_amount_limit') is-invalid @enderror"
                                    value="{{ old('delivery_boy_order_amount_limit', setting('delivery_boy_order_amount_limit')) }}">
                                @error('delivery_boy_order_amount_limit')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                    for="geolocation_distance_radius">{{ __('levels.geolocation_distance_radius') }}</label>
                                <span class="text-danger">*</span>
                                <input name="geolocation_distance_radius" id="geolocation_distance_radius" type="text"
                                    class="form-control @error('geolocation_distance_radius') is-invalid @enderror"
                                    value="{{ old('geolocation_distance_radius', setting('geolocation_distance_radius')) }}">
                                @error('geolocation_distance_radius')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>{{ __('levels.order_attachment_checking') }}</label> <span
                                    class="text-danger">*</span>
                                <select name="order_attachment_checking" id="order_attachment_checking"
                                    class="form-control @error('order_attachment_checking') is-invalid @enderror">
                                    @foreach(trans('order_attachment_checking_statuses') as $key =>
                                    $order_attachment_checking)
                                    <option value="{{ $key }}"
                                        {{ (old('order_attachment_checking', setting('order_attachment_checking')) == $key) ? 'selected' : '' }}>
                                        {{ $order_attachment_checking }}</option>
                                    @endforeach
                                </select>
                                @error('order_attachment_checking')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="setting-fieldset">
                    <legend class="setting-legend">{{ __('App Setting') }}</legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="ios_app_link">{{ __('levels.ios_app_link') }}</label>
                                <input name="ios_app_link" id="ios_app_link" type="text"
                                    class="form-control @error('ios_app_link') is-invalid @enderror"
                                    value="{{ old('ios_app_link', setting('ios_app_link')) }}">
                                @error('ios_app_link')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="android_app_link">{{ __('levels.android_app_link') }}</label>
                                <input name="android_app_link" id="android_app_link" type="text"
                                    class="form-control @error('android_app_link') is-invalid @enderror"
                                    value="{{ old('android_app_link', setting('android_app_link')) }}">
                                @error('android_app_link')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="form-group col-md-6">
                        <button class="btn btn-primary">
                            <span>{{ __('Update Site Setting') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
