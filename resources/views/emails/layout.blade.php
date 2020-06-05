<!DOCTYPE html>
<html>

    @include ('emails.head')

    <body>

        @include ('emails.logo')

        @yield('content')
             
        <p> {!! __('emails.common_if_you_have_any_questions') !!} </p>
    </body>
</html>