@extends('admin.layouts.master')

@section('main-content')
	
	<section class="section">
        <div class="section-header">
            <h1>{{ __('Customers') }}</h1>
            {{ Breadcrumbs::render('customers/view') }}
        </div>

        <div class="section-body">
        	<div class="row">
	   			<div class="col-4 col-md-4 col-lg-4">
			    	<div class="card">
					    <div class="card-body card-profile">
					        <img class="profile-user-img img-responsive img-circle" src="{{ $user->images }}" alt="User profile picture">
					        <h3 class="text-center">{{ $user->name }}</h3>
					        <p class="text-center">
					        	{{ $user->roles->first()->name ?? '' }}
					        </p>
					    </div>
					    <!-- /.box-body -->
					</div>
				</div>
	   			<div class="col-8 col-md-8 col-lg-8">
			    	<div class="card">
			    		<div class="card-body">
			    			<div class="profile-desc">
			    				<div class="single-profile">
			    					<p><b>{{ __('First Name') }}: </b> {{ $user->first_name}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Last Name') }}: </b> {{ $user->last_name}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Email') }}: </b> {{ $user->email}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Phone') }}: </b> {{ $user->phone}}</p>
			    				</div>
			    				<div class="single-full-profile">
			    					<p><b>{{ __('Address') }}: </b> {{ $user->address}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Credit') }}: </b> {{ currencyFormat($user->balance->balance > 0 ? $user->balance->balance : 0 ) }}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Username') }}: </b> {{ $user->username}}</p>
			    				</div>
			    				<div class="single-profile">
			    					<p><b>{{ __('Status') }}: </b> {{ $user->my_status}}</p>
			    				</div>
			    			</div>
			    		</div>
			    	</div>
				</div>
        	</div>
        </div>
    </section>

@endsection
