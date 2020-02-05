<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('layouts.head')

<body id="authent">
    <div id="app" class="container">
        <!--main container-->
        @include ('layouts.flash')

        @yield('content')

        @include ('partials.footer')
    </div>
    <!--/main container-->
  
    
    @include ('partials.footer-javascript')
</body>

</html>