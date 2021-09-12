 <ul class="list-group">
        <a class=" list-group-item {{ Request::segment(2) == 'profile' ? 'active' : '' }}" href="{{ route('account.profile')  }}">{{ __('Account overview') }}</a>
        <a class=" list-group-item {{ Request::segment(2) == 'update' ? 'active' : '' }}" href="{{ route('account.profile.index')  }}">{{ __('Account Update') }}</a>
        <a class=" list-group-item {{ Request::segment(2) == 'password' ? 'active' : '' }}" href="{{ route('account.password')  }}">{{ __('Change Password') }}</a>
        <a class=" list-group-item {{ Request::segment(2) == 'order' ? 'active' : '' }}" href="{{ route('account.order')  }}">{{ __('Order History') }}</a>
        <a class=" list-group-item {{ Request::segment(2) == 'transaction' ? 'active' : '' }}" href="{{ route('account.transaction')  }}">{{ __('Transaction') }}</a>
        <a class=" list-group-item {{ Request::segment(2) == 'review' ? 'active' : '' }}" href="{{ route('account.review')  }}">{{ __('Review') }}</a>
    </ul>
    <br>
 <a class="btn btn-light btn-block" href="{{ route('logout') }}"
    onclick="event.preventDefault();   document.getElementById('logout-form-sidebar').submit();"><i class="fa fa-power-off"></i> <span class="text">{{__('Logout')}}</span></a>
 <form class="d-none" id="logout-form-sidebar" action="{{ route('logout') }}" method="POST">
     {{ csrf_field() }}
 </form>
