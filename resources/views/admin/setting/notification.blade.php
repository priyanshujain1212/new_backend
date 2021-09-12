@extends('admin.setting.index')

@section('admin.setting.breadcrumbs')
    {{ Breadcrumbs::render('notification-setting') }}
@endsection

@section('admin.setting.layout')
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.setting.notification-update') }}">
                     @csrf
                     <fieldset class="setting-fieldset">
                        <legend class="setting-legend">{{ __('Notification Setting') }}</legend>
                         <div class="row">
                             <div class="col-sm-12">
                                 <div class="form-group">
                                     <label for="fcm_secret_key">{{ __('levels.firebase_secret_key') }}</label>
                                     <span class="text-danger">*</span>
                                     <input name="fcm_secret_key" id="fcm_secret_key" type="text"
                                         class="form-control @error('fcm_secret_key') is-invalid @enderror"
                                         value="{{ old('fcm_secret_key', setting('fcm_secret_key')) }}">
                                     @error('fcm_secret_key')
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
                            <button type="submit" class="btn btn-primary btn-sm">
                                <span>{{ __('Update Notification Setting') }}</span>
                            </button>
                        </div>
                     </div>
                 </form>       
            </div>
        </div>   
    </div>
@endsection