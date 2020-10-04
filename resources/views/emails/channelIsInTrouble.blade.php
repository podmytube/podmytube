@extends('emails.layout')

@section('mailTitle', $mailTitle )

@section ('content')

<p>
    {{ __('emails.common_hello', ['name' => 'You']) }}
</p>

<h1> {{$mailTitle}} </h1>

<ul>
    @foreach($channelInTroubleMessages as $channelInTroubleMessage)
    <li>
        {{ $channelInTroubleMessage }}
    </li>
    @endforeach
</ul>


@endsection