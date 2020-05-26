@extends('emails.layout')

@section('mailTitle', __('emails.limitsReached_subject', ['media_title' => $media->title]) )

@section ('content')

<h1> {{ __('emails.common_hello', ['name' => $user->name]) }}</h1>

<p>
    {!! __('emails.limitsReached_body',['media_title' => $media->title, 'channel_name' => $channel->channel_name]) !!}
</p>

<a href="{{ url('/') }}" class="button bgsuccess"> @lang('emails.common_upgrade_my_plan') </a>

<p> {!! __('emails.common_if_you_have_any_questions') !!} </p>



@endsection