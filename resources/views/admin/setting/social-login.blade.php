@extends('admin.setting.index')

@section('admin.setting.breadcrumbs')
    {{ Breadcrumbs::render('social-login-setting') }}
@endsection

@section('admin.setting.layout')
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="paymentheader text-center">{{ __('Social Login Setting') }}</h4>
                        <hr>
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ ((old('settingtypesocial', setting('settingtypesocial')) == 'facebook') || (old('settingtypesocial', setting('settingtypesocial')) == '')) ? 'active' : '' }}"
                                    id="facebook" data-toggle="pill" href="#facebooktab" role="tab" aria-controls="facebooktab"
                                    aria-selected="true">{{ __('Facebook') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ (old('settingtypesocial', setting('settingtypesocial')) == 'google') ? 'active' : '' }}"
                                    id="google" data-toggle="pill" href="#googletab" role="tab" aria-controls="googletab"
                                    aria-selected="false">{{ __('Google') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade {{ ((old('settingtypesocial', setting('settingtypesocial')) == 'facebook') || (old('settingtypesocial', setting('settingtypesocial')) == '')) ? 'show active' : '' }}"
                                id="facebooktab" role="tabpanel" aria-labelledby="facebook">
                                <form class="form-horizontal" role="form" method="POST"
                                    action="{{ route('admin.setting.social-login-update') }}">
                                    @csrf
                                    <fieldset class="setting-fieldset">
                                        <legend class="setting-legend">{{ __('Facebook Setting') }}</legend>
                                        <input type="hidden" name="settingtypesocial" value="facebook">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="facebook_key">{{ __('Facebook Client ID') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input name="facebook_key" id="facebook_key" type="text"
                                                        class="form-control @error('facebook_key') is-invalid @enderror"
                                                        value="{{ old('facebook_key', setting('facebook_key') ?? '') }}">
                                                    @error('facebook_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="facebook_secret">{{ __('Facebook Client Secret') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input name="facebook_secret" id="facebook_secret" type="text"
                                                        class="form-control @error('facebook_secret') is-invalid @enderror"
                                                        value="{{ old('facebook_secret', setting('facebook_secret') ?? '') }}">
                                                    @error('facebook_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="facebook_url">{{ __('Facebook url') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input name="facebook_url" id="facebook_url" type="text"
                                                        class="form-control @error('facebook_url') is-invalid @enderror"
                                                        value="{{ old('facebook_url', setting('facebook_url') ?? '') }}">
                                                    @error('facebook_url')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <button class="btn btn-primary"><span>{{ __('Update Facebook Setting') }}</span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>

                            <div class="tab-pane fade {{ (old('settingtypesocial', setting('settingtypesocial')) == 'google') ? 'show active' : '' }}" id="googletab" role="tabpanel" aria-labelledby="google">
                                <form class="form-horizontal" role="form" method="POST"
                                    action="{{ route('admin.setting.social-login-update') }}">
                                    @csrf
                                    <fieldset class="setting-fieldset">
                                        <legend class="setting-legend">{{ __('Google Setting') }}</legend>
                                        <input type="hidden" name="settingtypesocial" value="google">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="google_key">{{ __('Googel Client ID') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input name="google_key" id="google_key" type="text"
                                                        class="form-control @error('google_key')is-invalid @enderror"
                                                        value="{{ old('google_key', setting('google_key') ?? '') }}">
                                                    @error('google_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="google_secret">{{ __('Googel Client Sceret') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input name="google_secret" id="google_secret" type="text"
                                                        class="form-control @error('google_secret') is-invalid @enderror"
                                                        value="{{ old('google_secret', setting('google_secret') ?? '') }}">
                                                    @error('google_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="google_url">{{ __('Googel Url') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input name="google_url" id="google_url" type="text"
                                                        class="form-control @error('google_url') is-invalid @enderror"
                                                        value="{{ old('google_url', setting('google_url') ?? '') }}">
                                                    @error('google_url')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <button class="btn btn-primary">
                                                        <span>{{ __('Update Google  Setting') }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection