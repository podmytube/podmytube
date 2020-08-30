<!--made with passion by fred-->
<!doctype html>
<html prefix="og: http://ogp.me/ns#" lang="{{ app()->getLocale() }}">

@include ('partials.head')

<body data-spy="scroll" data-target="#navscroll" data-offset="0">
    
    @yield('content')

    @include ('partials.footer')

</body>

</html>