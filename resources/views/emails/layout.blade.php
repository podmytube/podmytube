<!DOCTYPE html>
<html>
    @include ('emails.head')
    <body>
        <div id="logo">
            <img src="{{ $message->embed($podmytubeLogo) }}">
        </div>

        @yield('content')
             
    </body>
</html>