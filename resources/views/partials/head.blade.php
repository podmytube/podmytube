<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle') - {{ config('app.name', 'Dashboard') }} - PodMyTube</title>

    @yield('recaptcha')

    @yield('stripeJs')

    <script src="{{ asset('js/app.js') }}"></script>

    <!--Favicon-->
    <link rel="icon" type="image/png" href="/favicon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

    @if (App::environment('production'))
    <script async defer data-domain="podmytube.com" src="https://stats.podmytube.com/js/index.js"></script>
    @endif

</head>