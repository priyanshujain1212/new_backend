<!DOCTYPE HTML>
<html lang="en">
@include('partials._head')
<body>


    @include('partials._nav')

    @yield('main-content')

    @include('partials._footer')

    @yield('footer-js')
</body>
</html>
