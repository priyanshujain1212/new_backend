@extends('layouts.app')

@section('main-content')
    <section class="section-content padding-y">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <header class="section-heading mt-0">
                        <h2 class="section-title">{{ __('My account') }}</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <aside class="col-md-3">
                @include('frontend.layouts.partials._navigation')
                </aside>
                <main class="col-md-9">
                    @yield('frontend.content')
                </main> <!-- col.// -->
            </div>
        </div> <!-- container .//  -->
    </section>
@endsection

@section('footer-js')
    <script src="{{ asset('js/profile/index.js') }}"></script>
@endsection
