@extends('emails.layout')

@section('mailTitle', 'chaton' )

@section ('content')

Chat 

<h1> {{ __('emails.registered_h1_success', ['name' => $user->name]) }}</h1>
<p> @lang('emails.registered_p_first_line') </p>
<p> @lang('emails.registered_p_register') </p>

<a href="{{ url('/') }}" class="button" style="color:white;"> @lang('emails.registered_a_add_one_channel') </a>

@endsection