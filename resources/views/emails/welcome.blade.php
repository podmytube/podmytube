@extends('emails.layout')

@section('mailTitle', 'Welcome/Bienvenue' )

@section ('content')

<h1> {{ __('emails.welcome_h1_welcome', ['name' => $user->name]) }}</h1>
<p> @lang('emails.welcome_p_first_line') </p>
<p> @lang('emails.welcome_p_register') </p>

<a href="{{ url('/') }}" class="button" style="color:white;"> @lang('emails.welcome_a_add_one_channel') </a>
        
@endsection