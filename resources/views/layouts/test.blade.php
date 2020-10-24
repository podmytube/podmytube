<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lorem</title>

    <script type="text/javascript" src="/js/app.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/app.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="bg-gray-900">

    @yield('content')

</body>

</html>