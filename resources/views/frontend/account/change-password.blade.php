@extends('frontend.layouts.default')
@section('frontend.content')
<article class="card mb-3">
    <div class="card-body">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status')}}
            </div>
        @endif
            <div>
                <div>
                    <form method="post" action="{{ route('account.password.update') }}">
                        @csrf
                        @method('put')

                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-12 col-12">
                                    <label for="old_password">{{ __('Old Password') }}</label> <span
                                        class="text-danger">*</span>
                                    <input id="old_password" name="old_password" type="password"
                                           class="form-control @error('old_password') is-invalid @enderror">
                                    @error('old_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12 col-12">
                                    <label for="password">{{ __('Password') }}</label> <span
                                        class="text-danger">*</span>
                                    <input id="password" name="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" />
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12 col-12">
                                    <label
                                        for="password_confirmation">{{ __('Password Confirmation') }}</label>
                                    <span class="text-danger">*</span>
                                    <input id="password_confirmation" name="password_confirmation"
                                           type="password"
                                           class="form-control @error('password_confirmation') is-invalid @enderror" />
                                    @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-12 col-12">
                                        <button class="btn btn-primary">{{ __('Save Password') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div> <!-- card-body .// -->
</article> <!-- card.// -->
@endsection
