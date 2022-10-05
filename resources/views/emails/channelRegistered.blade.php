@extends('emails.layout')

@section('mailTitle', '')

@section('content')


    <h1> Congratulations {{ $channel->user->firstname }}</h1>

    <p> Channel <b>{{ $channel->title() }}</b> is now registered.</p>
    <p> In a few minutes, you channel will be validated then your podcast will include your last episodes. </p>


    <p>
        <b>One last word</b>. If you want to register your podcast on iTunes (You should !) you will have to :
    <ul>
        <li>Select your podcast category </li>
        <li>Add a podcast illustration (1400x1400 minimum 3000x3000 maximum)</li>
    </ul>
    </p>

    <p>
        <a href="{{ route('channel.edit', $channel) }}" class="button bgsuccess">
            Select your podcast category
        </a>

        <a href="{{ route('channel.cover.edit', $channel) }}" class="button bgsuccess">
            Add your illustration
        </a>
    </p>

@endsection
