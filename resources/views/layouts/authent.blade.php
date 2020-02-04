<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include ('layouts.head')

<body id="authent">
    <div id="app" class="container"> <!--main container-->
        @include ('layouts.flash')

        @yield('content')

	</div> <!--/main container-->

    @include ('layouts.footer')
	
</body>
</html>
