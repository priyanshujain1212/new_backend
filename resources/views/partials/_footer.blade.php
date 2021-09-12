<!-- ========================= FOOTER ========================= -->
<footer class="section-footer border-top">
    <div class="container">
        <section class="footer-top padding-y">
            <div class="row">
                <aside class="col-md-4">
                    <article class="mr-3">
                        <img src="{{ asset("images/".setting('site_logo')) }}" class="logo-footer">
                        <p class="mt-3">
                            {{ Str::of(strip_tags(setting('site_description')))->limit(140, '..') }}
                        </p>
                        <div>
                            @if(setting('facebook'))
                            <a class="btn btn-icon btn-light" title="Facebook" target="_blank"
                                href="{{ url(setting('facebook')) }}"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if(setting('instagram'))
                            <a class="btn btn-icon btn-light" title="Instagram" target="_blank"
                                href="{{ url(setting('instagram')) }}"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if(setting('youtube'))
                            <a class="btn btn-icon btn-light" title="Youtube" target="_blank"
                                href="{{ url(setting('youtube')) }}"><i class="fab fa-youtube"></i></a>
                            @endif
                            @if(setting('twitter'))
                            <a class="btn btn-icon btn-light" title="Twitter" target="_blank"
                                href="{{ url(setting('twitter')) }}"><i class="fab fa-twitter"></i></a>
                            @endif
                        </div>
                    </article>
                </aside>
                <aside class="col-sm-3 col-md-2">
                    <h6 class="title">{{__('About')}}</h6>
                    <ul class="list-unstyled">
                        @if(!blank($footermenus))
                            @foreach($footermenus as $footermenu)
                                @if($footermenu->template_id != 1)
                                    @continue
                                @endif
                                <li> <a href="{{ route('page', $footermenu) }}">{{ $footermenu->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </aside>
                <aside class="col-sm-3 col-md-2">
                    <h6 class="title">{{__('Services')}}</h6>
                    <ul class="list-unstyled">
                        @if(!blank($footermenus))
                            @foreach($footermenus as $footermenu)
                                @if($footermenu->template_id != 2)
                                    @continue
                                @endif
                                <li> <a href="{{ route('page', $footermenu) }}">{{ $footermenu->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </aside>
                <aside class="col-sm-3  col-md-2">
                    <h6 class="title">{{__('For users')}}</h6>
                    <ul class="list-unstyled">
                        @if(Auth::guest())
                            <li> <a href="{{ route('login') }}">{{ __('Sign in') }} </a></li>
                            <li> <a href="{{ route('register') }}"> {{ __('User Register') }}</a></li>
                        @else
                            <li> <a href="{{ route('account.profile') }}">{{ __('Profile') }} </a></li>
                            <li> <a href="{{ route('account.order') }}"> {{__('My Orders')}} </a></li>
                            <li> <a href="{{ route('account.transaction') }}"> {{__('Transaction')}} </a></li>
                            <li> <a href="{{ route('account.review') }}"> {{__('Review')}} </a></li>
                        @endif
                        @if(!blank($footermenus))
                            @foreach($footermenus as $footermenu)
                                @if($footermenu->template_id != 3)
                                    @continue
                                @endif
                                <li> <a href="{{ route('page', $footermenu) }}">{{ $footermenu->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </aside>
                <aside class="col-sm-2  col-md-2">
                    @if(setting('android_app_link') || setting('ios_app_link'))
                    <h6 class="title">{{ __('Download App') }}</h6>
                    @if(setting('android_app_link'))
                    <a href="{{ setting('android_app_link') }}" class="d-block mb-2"><img
                            src="{{asset('frontend/images/misc/android.png')}}" height="60"
                            style="margin-left: -11px !important;"></a>
                    @endif
                    @if(setting('ios_app_link'))
                    <a href="{{ setting('ios_app_link') }}" class="d-block mb-2"><img
                            src="{{ asset('frontend/images/misc/appstore.png') }}" height="40"></a>
                    @endif
                    @endif
                </aside>
            </div> <!-- row.// -->
        </section> <!-- footer-top.// -->

        <section class="footer-copyright border-top">
            <p class="text-muted"> {{ setting('site_footer') }}</p>
        </section>
    </div><!-- //container -->
</footer>
<!-- ========================= FOOTER END // ========================= -->

<!-- JS Libraies -->
<script src="{{ asset('frontend/plugins/slickslider/slick.min.js') }}"></script>
<script src="{{ asset('assets/modules/izitoast/dist/js/iziToast.min.js') }}"></script>
<!-- custom javascript -->
<script type="application/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const areaUrl = "{{ route('area.index') }}";
</script>
<script src="{{ asset('frontend/js/script.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    @if(session('success'))
        iziToast.success({
            title: 'Success',
            message: '{{ session('success') }}',
            position: 'topRight'
        });
    @endif

    @if(session('error'))
        iziToast.error({
            title: 'Error',
            message: '{{ session('error') }}',
            position: 'topRight'
        });
    @endif
</script>
