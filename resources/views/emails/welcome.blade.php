@extends('emails.layout')

@section('mailTitle', $subject)

@section('content')

    <h1> {{ $subject }}</h1>
     
    <p>I'm delighted by your interest in my service !</p>

    <p>Now that you are registered, you should add the youtube channel you want to convert, in a magnificent podcast</p>
    
    <a href="{{ route('channel.step1') }}" class="button bgsuccess"> {{ __('emails.welcome_a_add_one_channel') }} </a>

@endsection
