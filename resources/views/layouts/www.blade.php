<!--made with passion by fred-->
<!doctype html>
<html prefix="og: http://ogp.me/ns#" lang="{{ app()->getLocale() }}">

@include ('partials.head')

<body data-spy="scroll" data-target="#navscroll" data-offset="0">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WKBZ7JJ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    @yield('content')

    @include ('partials.footer')

</body>

</html>