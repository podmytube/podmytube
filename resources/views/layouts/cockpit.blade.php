<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body class="antialiased bg-gray-900" style="font-family: 'Roboto', sans-serif;">

    @yield('content')

</body>

</html>
@if (App::environment('testing'))
    @include ('partials.testing')
@endif
