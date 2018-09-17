@extends('emails.layout')

@section('mailTitle', 'chaton' )

@section ('content')


<h1> {{ __('emails.registered_h1_success', ['name' => $user->name]) }}</h1>

<p> {!! __('emails.registered_p_channel_is_now_registered', ['channel_id' => $channel->channel_id]) !!} </p>
<p> {!! __('emails.registered_p_in_a_few_minutes') !!} </p>


<p> {!! __('emails.registered_p_one_last_word', ['name' => $channel->channel_id]) !!} </p>

<a href="{{ url('/') }}/channel/{{ $channel->channel_id }}/edit" class="button" style="color:white;"> 
    @lang('emails.registered_a_select_a_category') 
</a>

<a href="{{ url('/') }}/channel/{{ $channel->channel_id }}/thumbs/edit" class="button" style="color:white;"> 
    @lang('emails.registered_a_add_an_illustration') 
</a>

<p> {!! __('emails.registered_p_if_you_have_any_questions') !!} </p>



@endsection