@extends('emails.layout')

@section('mailTitle', $mailTitle )

@section ('content')

<p>
    {{ __('emails.common_hello', ['name' => 'You']) }}
</p>

<p>
    {{$mailTitle}}
</p>

<ul>
    @foreach($channelsInTrouble as $channel)
    <li>
        {{$channel->channel_name}} ({{$channel->channel_id}}) is in trouble.
    </li>
    @endforeach
</ul>


@endsection