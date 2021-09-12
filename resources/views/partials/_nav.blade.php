<header class="section-header">

    @if(setting('site_email'))
        <nav class="navbar  navbar-expand p-0 bg-selver">
            <div class="container">
                <ul class="navbar-nav">
                    @if(setting('site_phone_number'))
                        <li  class="nav-item-a"><a href="#" class="nav-link">
                                <i class="fa fa-phone">
                                </i> {{ setting('site_phone_number') }} </a>
                        </li>
                    @endif
                    @if(setting('site_email'))
                        <li  class="nav-item-a"><a href="#" class="nav-link">
                                <i class="fa fa-envelope">
                                </i> {{ setting('site_email') }} </a>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav ">
                    @if(setting('android_app_link') || setting('ios_app_link'))
                        <li  class="nav-item-a">
                            @if(setting('android_app_link'))
                            <a href="{{setting('android_app_link')}}" target="_blank" class="nav-link">
                                <i class="fa fa-mobile">
                                </i> {{ __('Save big on our app!') }}
                            </a>
                                @else
                                <a href="{{setting('ios_app_link')}}" target="_blank" class="nav-link">
                                    <i class="fa fa-mobile">
                                    </i> {{ __('Save big on our app!') }}
                                </a>
                            @endif
                        </li>
                    @endif
                </ul>

            </div>
        </nav>
    @endif

    <section class="header-main border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 col-6">
                    <a href="{{ route('home') }}" class="brand-wrap">
                        <img class="logo" src="{{ asset("images/".setting('site_logo')) }}" alt="logo">
                    </a>
                </div>
                @yield('search')
                <div class="col-md">
                    <div class="widgets-wrap float-md-right">
                        <?php
                            $myrole  = auth()->user()->myrole ?? 0;
                            $permissionBackend = [1,3,4];
                            if(in_array($myrole, $permissionBackend)) { ?>
                                <div class="widget-header mr-3">
                                    <a href="{{ route('admin.dashboard.index') }}" class="icon icon-sm rounded-circle border" data-toggle="tooltip" data-placement="bottom" title="Go to dashboard"><i class="fas fa-tachometer-alt"></i></a>
                                </div>
                        <?php } ?>

                        <div class="widget-header  mr-3">
                            <a href="{{ route('cart.index') }}" class="icon icon-sm rounded-circle border"><i class="fa fa-shopping-cart"></i></a>
                            <span class="badge badge-pill badge-danger notify">{{ Cart::count() }}</span>
                        </div>
                        <div class="widget-header icontext">
                            @if(auth()->user())
                                <a href="{{ route('account.profile') }}">
                                    <img src="{{ auth()->user()->images }}" alt="" class="icon icon-sm rounded-circle border">
                                </a>
                            @endif
                            <div class="text">
                                @if(Auth::guest())
                                    <div>
                                        <a href="{{ route('login') }}">{{ __('Sign in') }}</a> |
                                        <a href="{{ route('register') }}"> {{ __('Register') }}</a>
                                    </div>
                                @else
                                    <span class="text-muted">{{ __('Welcome!') }}</span>
                                    <div>
                                        <a href="{{ route('account.profile') }}">{{ Str::of(auth()->user()->name)->limit(10, '..') }}</a>|
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                        <form class="d-none" id="logout-form" action="{{ route('logout') }}" method="POST">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</header>

@yield('nav')

