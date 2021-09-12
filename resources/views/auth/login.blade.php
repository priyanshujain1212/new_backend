@extends('layouts.app')

@section('main-content')
    <section class="section-content padding-y" style="min-height:84vh">
        <div class="card mx-auto" style="max-width: 380px; margin-top:100px;">
            <div class="card-body">
                <h4 class="card-title mb-4">{{ __('Sign in') }}</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <input id="demoemail" type="email" class="form-control @if($errors->has('email') || session('block')) is-invalid @endif"
                               name="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email">
                        @if($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @elseif(session('block'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ session('block') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input placeholder="Password" id="demopassword" type="password" class="form-control @if($errors->has('password')) is-invalid @endif"
                               name="password" autocomplete="current-password">
                        @if($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="float-right">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                        <label class="float-left custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" value="1">
                            <div class="custom-control-label"> {{ __('Remember') }} </div>
                        </label>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"> {{ __('Login') }}  </button>
                    </div>
                </form>
            </div>
        </div>
        @if(env('DEMO'))
            <div class="card mx-auto text-center mt-4" style="max-width: 500px;">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('For Quick Demo Login Click Below...') }}</h4>
                </div>
                <div class="card-body">
                    <div class="buttons">
                        <button id="demoadmin" class="btn btn-primary">{{ __('Admin') }}</button>
                        <button id="democustomer" class="btn btn-info">{{ __('Customer') }}</button>
                        <button id="demoshopowner" class="btn btn-success">{{ __('Shop Owner') }}</button>
                        <button id="demodeliveryboy" class="btn btn-warning">{{ __('Delivery Boy') }}</button>
                    </div>
                </div>
            </div>
        @endif
        <p class="text-center mt-4">{{ __("Don't have account?") }} <a href="{{ route('register') }}">{{ __('Sign up') }}</a></p>
        <br><br>
    </section>
@endsection


@section('footer-js')
    <script src="{{ asset('frontend/js/demo-login.js') }}"></script>
@endsection
