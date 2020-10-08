<!DOCTYPE html>
<html lang="en">

@include ('partials.head')

<body class="leading-normal tracking-normal text-white gradient" style="font-family: 'Source Sans Pro', sans-serif;">

    @include ('partials.navbar')

    <div id="app" class="container" style="padding-top: 120px;">
        @include ('partials.flash')

        @yield('content')

        @include ('partials.footer')
    </div>
    <!--/main container-->
</body>

</html>