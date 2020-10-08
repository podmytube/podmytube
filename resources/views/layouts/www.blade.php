<!--made with passion by fred-->
<!DOCTYPE html>
<html lang="en">

@include ('partials.head')

<body class="leading-normal tracking-normal text-white gradient" style="font-family: 'Source Sans Pro', sans-serif;">
    
    @include ('partials.navbar')

    @yield('content')

    @include ('partials.footer')

</body>

</html>