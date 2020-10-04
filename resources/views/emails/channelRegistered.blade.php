@extends('emails.layout')

@section('mailTitle', '' )

@section ('content')


<h1> {{ __('emails.registered_h1_success', ['name' => $user->name]) }}</h1>

<p> {!! __('emails.registered_p_channel_is_now_registered', ['channel_id' => $channel->title()]) !!} </p>
<p> {!! __('emails.registered_p_in_a_few_minutes') !!} </p>


<p> {!! __('emails.registered_p_one_last_word', ['name' => $channel->channel_id]) !!} </p>

<p>
    <a href="{{ route ('channel.edit', $channel) }}" class="button bgsuccess"> 
        {{ __('emails.registered_a_select_a_category') }}
    </a>

    <a href="{{ route ('channel.thumbs.edit', $channel) }}" class="button bgsuccess">  
        @lang('emails.registered_a_add_an_illustration') 
    </a>
</p>

@endsection