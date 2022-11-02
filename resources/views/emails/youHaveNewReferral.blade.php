@extends('emails.layout')

@section('mailTitle', '')

@section('content')

    <h1> Congratulations {{ $referrer->firstname }}</h1>

    <p>
        Channel <b><a href="{{ $channel->youtubeUrl() }}">{{ $channel->title() }}</a></b> has been registered
        by one of your referral ({{ $channel->user->firstname }}).
    </p>

    <p>The chosen plan was {{ $channel->plan->name }} at {{ $channel->plan->price }}</p>

@endsection
