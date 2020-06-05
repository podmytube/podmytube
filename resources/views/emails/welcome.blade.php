@extends('emails.layout')

@section('mailTitle', $subject )

@section ('content')

<h1> {{ $subject }}</h1>
<p> {{ __('emails.welcome_p_first_line') }} </p>
<p> {{ __('emails.welcome_p_register') }} </p>

<a href="{{ route('channel.create') }}" class="button bgsuccess"> {{  __('emails.welcome_a_add_one_channel') }} </a>
        
@endsection