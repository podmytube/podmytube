<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('partials.head')

<body id="authent" style="background-color: #f5f5f5;">
    <div id="app" class="container">
        <!--main container-->
        @include ('partials.flash')

        @yield('content')

        @include ('partials.footer')
    </div>
    <!--/main container-->
</body>

</html>