<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body class="antialiased bg-gray-900" style="font-family: 'Roboto', sans-serif;">

    @include('partials.cockpit.header')

    @yield('content')

    @livewireScripts
</body>

</html>
@if (App::environment('testing'))
    @include ('partials.testing')
@endif
