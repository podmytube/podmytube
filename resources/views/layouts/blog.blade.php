<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('partials.head')

<body>

    @include ('partials.navbar')

    @include ('partials.flash')

    @yield('content')

    @include ('partials.footer')

    <!--/main container-->
</body>

</html>