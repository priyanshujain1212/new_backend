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
                <form action="{{ route('account.profile.update', $user) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="form-group col">
                            <label>{{ __('First Name') }}</label> <span
                                class="text-danger">*</span>
                            <input type="text" name="first_name"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name', $user->first_name) }}">
                            @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col">
                            <label>{{ __('Last Name') }}</label> <span
                                class="text-danger">*</span>
                            <input type="text" name="last_name"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name', $user->last_name) }}">
                            @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label>{{ __('Email') }}</label> <span class="text-danger">*</span>
                            <input type="text" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group col">
                            <label>{{ __('Phone') }}</label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label>{{ __('Username') }}</label>
                            <input type="text" name="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $user->username) }}">
                            @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label for="customFile">{{ __('Image') }}</label>
                            <div class="custom-file">
                                <input name="image" type="file"
                                       class="custom-file-input @error('image') is-invalid @enderror"
                                       id="customFile" onchange="readURL(this);">
                                <label class="custom-file-label"
                                       for="customFile">{{ __('Choose file') }}</label>
                            </div>
                            @if ($errors->has('image'))
                                <div class="help-block text-danger">
                                    {{ $errors->first('image') }}
                                </div>
                            @endif
                            <img class="img-thumbnail image-width mt-4 mb-3" id="previewImage"
                                 src="{{ $user->images }}"
                                 alt="{{ $user->name }} profile image" />
                        </div>
                        <div class="form-group col">
                            <label>{{ __('Address') }}</label>
                            <textarea name="address" class="form-control small-textarea-height"
                                      id="address" cols="30"
                                      rows="10">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <button class="btn btn-primary" type="submit">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
    </div> <!-- card-body .// -->
</article> <!-- card.// -->
@endsection
