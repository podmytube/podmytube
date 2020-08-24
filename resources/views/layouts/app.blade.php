<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('partials.head')

<body>
    
    @include ('partials.navbar')

    <div id="app" class="container">
        @include ('partials.flash')

        @yield('content')

        @include ('partials.footer')
    </div>
    <!--/main container-->
</body>

</html>