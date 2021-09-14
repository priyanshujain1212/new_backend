@extends('admin.layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
@endsection

@section('main-content')

	<section class="section">
        <div class="section-header">
            <h1>{{ __('Sections') }}</h1>
            {{ Breadcrumbs::render('sections/add') }}
        </div>

        <div class="section-body">
		<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
				    <div class="card">
					<form action="{{ route('admin.section.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						    <div class="card-body">
								<div class="form-row">
									<div class="form-group col">
										<label>{{ __('Name') }}</label> <span class="text-danger">*</span>
										<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
										@error('name')
										<div class="invalid-feedback">
											{{ $message }}
										</div>
										@enderror
									</div>

									<div class="form-group col">
										<label>{{ __('Status') }}</label> <span class="text-danger">*</span>
										<select name="status" class="form-control @error('status') is-invalid @enderror">
											@foreach(trans('statuses') as $key => $status)
												<option value="{{ $key }}" {{ (old('status') == $key) ? 'selected' : '' }}>{{ $status }}</option>
											@endforeach
										</select>
										@error('status')
										<div class="invalid-feedback">
											{{ $message }}
										</div>
										@enderror
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col">
										<label for="customFile">{{ __('Section Image') }}</label>
										<div class="custom-file">
											<input name="image" type="file" class="custom-file-input @error('image') is-invalid @enderror" id="customFile" onchange="readURL(this);">
											<label  class="custom-file-label" for="customFile">Choose file</label>
										</div>
										@if ($errors->has('image'))
											<div class="help-block text-danger">
												{{ $errors->first('image') }}
											</div>
										@endif
										<img class="img-thumbnail mt-4 mb-3" id="previewImage" src="{{ asset('assets/img/default/section.png') }}" alt="your image" style="width: 120px"/>
									</div>

								</div>
						    </div>

					        <div class="card-footer">
					<button class="btn btn-primary mr-1" type="submit">{{ __('Submit') }}</button>
					</div>
		                </form>
					</div>
				</div>
			</div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/section/create.js') }}"></script>
@endsection
