<!--made with passion by fred-->
<!DOCTYPE html>
<html lang="en">

@include ('partials.head')

<body class="bg-gray-100 leading-normal tracking-normal" style="font-family: 'Source Sans Pro', sans-serif;">
    <div class="max-w-5xl mx-auto">

        @include ('partials.navbar')

        @yield('content')

        @include ('partials.footer')
    </div>
</body>

</html>