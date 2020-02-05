<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('partials.head')

<body>
    <div id="app" class="container">
        <!--main container-->

        @include ('partials.navbar')

        @yield('breadcrumbs')

        @include ('partials.flash')

        @yield('content')

        @include ('partials.footer')
    </div>
    <!--/main container-->


    @include ('partials.footer-javascript')
</body>

</html>