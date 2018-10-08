<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('layouts.head')

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5NRPK6V" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div id="app" class="container"> <!--main container-->

	@include ('layouts.navbar')

	@yield('content')

	</div> <!--/main container-->

    @include ('layouts.footer')
	
</body>
</html>
