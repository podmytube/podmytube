<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('pageTitle') - PodmyTube</title>
  <meta name="description" content="Hosting youtube podcasts">
  <meta name="keywords" content="youtube, podcast, podcats, hosting, converting">
  <meta name="author" content="Frederick Tyteca">

  <!--Favicon-->
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">

  @yield('recaptcha')

  @yield('stripeJs')

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @if (App::environment('production'))
  <script async defer data-domain="podmytube.com" src="https://stats.podmytube.com/js/index.js"></script>
  @endif

  <!-- Font Awesome if you need it
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
  -->

  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">
</head>