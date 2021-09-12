@extends('admin.layouts.master')

@section('main-content')
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Withdraw') }}</h1>
            {{ Breadcrumbs::render('withdraw/add') }}
        </div>

        <div class="section-body">
        	<div class="row">
	   			<div class="col-12 col-md-6 col-lg-6">
				    <div class="card">
				    	<form action="{{ route('admin.withdraw.store') }}" method="POST">
				    		@csrf
						    <div class="card-body">
						        <div class="form-group">
                                    <input name="request_withdraw_id" type="hidden" value="{{ $requestWithdraw->id }}">
						            <label>{{ __('User') }}</label> <span class="text-danger">*</span>
						            <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" data-url="{{ route('admin.withdraw.get-user-info') }}">
						            	<option value="0">{{ __('Select User') }}</option>
                                        <?php $selectUser = []; ?>
						            	@if(!blank($users))
							            	@foreach($users as $user)
                                                @if($user->id == old('user_id'))
                                                    <?php  $selectUser = $user; ?>
                                                @endif
							                	<option value="{{ $user->id }}" {{ (old('user_id', $requestWithdraw->user_id) == $user->id || auth()->user()->id == $user->id) ? 'selected' : '' }}>{{ $user->name }} {{ !blank($user->phone)  ? ' ('.$user->phone.')' : '' }}</option>
							                @endforeach
							            @endif
						            </select>
						            @error('user_id')
				                        <div class="invalid-feedback">
				                          	{{ $message }}
				                        </div>
				                    @enderror
						        </div>
                                <div class="form-group">
                                    <label>{{ __('Amount') }}</label> <span class="text-danger">*</span>
                                    <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $requestWithdraw->amount) }}">
                                    @error('amount')
	                                    <div class="invalid-feedback">
	                                        {{ $message }}
	                                    </div>
                                    @enderror
                                </div>
						    </div>

					        <div class="card-footer">
		                    	<button class="btn btn-primary mr-1" type="submit">{{ __('Submit') }}</button>
		                  	</div>
		                </form>
					</div>
				</div>


                <div id="userInfo" class="col-12 col-md-12 col-lg-4">
                    @if(!blank($selectUser))
                        <div class="card profile-widget margin-hidden">
                            <div class="profile-widget-header">
                                <img alt="image" src="{{ $selectUser->images }}" class="rounded-circle profile-picture center ">
                            </div>
                            <div class="profile-widget-description">
                                <dl class="row">
                                    <dt class="col-sm-5">{{ __('Name') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ $selectUser->name }}</dd>
                                    <dt class="col-sm-5">{{ __('Phone') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ $selectUser->phone }}</dd>
                                    <dt class="col-sm-5">{{ __('Email') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ $selectUser->email }}</dd>
                                    <dt class="col-sm-5">{{ __('Order Balance') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ currencyFormat($selectUser->deliveryBoyAccount->balance) }}</dd>
                                    <dt class="col-sm-5">{{ __('Credit') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ currencyFormat($selectUser->balance->balance > 0 ? $selectUser->balance->balance : 0 ) }}</dd>
                                    <dt class="col-sm-5">{{ __('Address') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ $selectUser->address }}</dd>
                                    <dt class="col-sm-5">{{ __('Status') }} <strong class="float-right">:</strong></dt>
                                    <dd class="col-sm-7">{{ $selectUser->mystatus }}</dd>
                                </dl>
                            </div>
                        </div>
                    @endif
                </div>



			</div>
        </div>
    </section>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/modules/select2/dist/css/select2.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/withdraw/create.js') }}"></script>
@endsection
