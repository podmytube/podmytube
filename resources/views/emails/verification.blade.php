@extends('emails.layout')

@section('mailTitle', '')

@section('content')

    <h1>Hello!</h1>

    <p>Please click the button below to verify your email address.</p>

    <p>
        <a href="{!! $url !!}" class="button bgsuccess">
            Verify Email Address
        </a>
    </p>

    If you did not create an account, no further action is required.

@endsection
