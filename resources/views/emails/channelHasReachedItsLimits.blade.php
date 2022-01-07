@extends('emails.layout')

@section('mailTitle', $mailTitle)

@section('content')

    <p>
        Hello {{ $userName }}, I've a bad news for you.
    </p>

    <h2>Quota exceeded</h2>
    <p>
        The plan you have chosen ({{ $channel->subscription->plan->name }})) includes
        {{ $channel->subscription->plan->nb_episodes_per_month }} episodes per month and
        per channel and I am sorry to tell that, for this month, you have consumed all the available quota 😞.
    </p>

    <h3>What to do now?</h3>
    <ul>
        <li>You <a href="{{ route('plans.index', $channel) }}">may upgrade</a> to a higher plan (easy/fast) 🤑 or </li>
        <li>Add <a href="{{ route('channel.edit', $channel) }}">a filtering rule</a> to include only the best videos of
            the month in your podcast 👈.</li>
    </ul>

    <p>Please note: the exclusive episodes you add manually also count towards the quota.</p>

    And I wish you a great day 🌅.
@endsection
