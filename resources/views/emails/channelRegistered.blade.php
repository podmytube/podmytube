@extends('emails.layout')

@section('mailTitle', 'chaton' )

@section ('content')


<h1> {{ __('emails.registered_h1_success', ['name' => $user->name]) }}</h1>

<p> {!! __('emails.registered_p_channel_is_now_registered', ['channel_id' => $channel->title()]) !!} </p>
<p> {!! __('emails.registered_p_in_a_few_minutes') !!} </p>


<p> {!! __('emails.registered_p_one_last_word', ['name' => $channel->channel_id]) !!} </p>

<p>
    <a href="{{ url('/') }}/channel/{{ $channel->channel_id }}/edit" class="button" style="color:white;"> 
        @lang('emails.registered_a_select_a_category') 
    </a>

    <a href="{{ url('/') }}/channel/{{ $channel->channel_id }}/thumbs/edit" class="button" style="color:white;"> 
        @lang('emails.registered_a_add_an_illustration') 
    </a>
</p>

@if ( App::getLocale() == 'fr')
<p>
    <a href="http://blog.podmytube.com/inscrire-son-podcast-sur-itunes/" class="button" style="color:white;"> 
        Inscrire son podcast sur iTunes
    </a>
</p>
@endif  

<p> {!! __('emails.registered_p_if_you_have_any_questions') !!} </p>



@endsection