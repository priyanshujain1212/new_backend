@extends('admin.layouts.master')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
@endsection

@section('main-content')

<section class="section">
    <div class="section-header">
        <h1>{{ __('Shops') }}</h1>
        {{ Breadcrumbs::render('shop/add') }}
    </div>

    <div class="section-body">
        <form action="{{ route('admin.shop.shopstore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="article-header">
                                <h5>{{ __('Request for shop') }}</h5>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="location">{{ __('levels.location') }}</label> <span
                                        class="text-danger">*</span>
                                    <select name="location_id" id="location"
                                        class="select2 form-control @error('location_id') is-invalid red-border @enderror"
                                        data-url="{{ route('admin.shop.get-area') }}">
                                        <option value="">{{ __('Select Location') }}</option>
                                        @if(!blank($locations))
                                        @foreach($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ (old('location_id') == $location->id) ? 'selected' : '' }}>
                                            {{ $location->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('location_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label for="area">{{ __('levels.area') }}</label> <span class="text-danger">*</span>
                                    <select name="area_id" id="area"
                                        class="select2 form-control @error('area_id') is-invalid red-border @enderror">
                                        <option value="">{{ __('Select Area') }}</option>
                                        @if(!blank($areas))
                                        @foreach($areas as $area)
                                        <option value="{{ $area->id }}"
                                            {{ (old('area_id') == $area->id) ? 'selected' : '' }}>{{ $area->name }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('area_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                    <input id="name" type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <label>{{ __('levels.delivery_charge') }}</label>
                                    <input type="text" name="delivery_charge"
                                        class="form-control @error('delivery_charge') is-invalid @enderror"
                                        value="{{ old('delivery_charge') }}">
                                    @error('delivery_charge')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="lat">{{ __('levels.latitude') }}</label>
                                    <input type="text" name="lat" id="lat"
                                        class="form-control @error('lat') is-invalid @enderror"
                                        value="{{ old('lat') }}">
                                    @error('lat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col">
                                    <label for="long">{{ __('levels.longitude') }}</label>
                                    <input type="text" id="long" name="long"
                                        class="form-control @error('long') is-invalid @enderror"
                                        value="{{ old('long') }}">
                                    @error('long')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('levels.shop_address') }}</label> <span class="text-danger">*</span>
                                <textarea name="shopaddress"
                                    class="form-control small-textarea-height @error('shopaddress') is-invalid @enderror"
                                    id="shopaddress">{{ old('shopaddress') }}</textarea>
                                @error('shopaddress')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>{{ __('levels.description') }}</label>
                                <textarea name="description"
                                    class="form-control small-textarea-height @error('description') is-invalid @enderror"
                                    id="description">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="article-header">
                                <h5>{{ __('Schedule') }}</h5>
                            </div>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="opening_time">{{ __('levels.opening_time') }}</label>
                                    <input id="opening_time" type="text" name="opening_time"
                                        class="form-control timepicker @error('opening_time') is-invalid @enderror"
                                        value="{{ old('opening_time') }}">
                                    @error('opening_time')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label for="closing_time">{{ __('levels.closing_time') }}</label>
                                    <input id="closing_time" type="text" name="closing_time"
                                        class="form-control timepicker @error('closing_time') is-invalid @enderror"
                                        value="{{ old('closing_time') }}">
                                    @error('closing_time')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label>{{ __('levels.current_status') }}</label> <span class="text-danger">*</span>
                                    <select name="current_status"
                                        class="form-control @error('current_status') is-invalid @enderror">
                                        @foreach(trans('current_statuses') as $current_statusKey => $current_status)
                                        <option value="{{ $current_statusKey }}"
                                            {{ (old('current_status') == $current_statusKey) ? 'selected' : '' }}>
                                            {{ $current_status }}</option>
                                        @endforeach
                                    </select>
                                    @error('current_status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="customFile">{{ __('levels.shop_image') }}</label>
                                <div class="custom-file">
                                    <input name="image" type="file"
                                        class="custom-file-input @error('image') is-invalid @enderror" id="customFile"
                                        onchange="readURL(this);">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                                @if ($errors->has('image'))
                                <div class="help-block text-danger">
                                    {{ $errors->first('image') }}
                                </div>
                                @endif
                                <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                    src="{{ asset('assets/img/default/shop.png') }}" alt="your image" />
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary mr-1" type="submit">{{ __('Submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('js/shop/create.js') }}"></script>
@endsection
