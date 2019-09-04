@extends('emails.layout')

@section('mailTitle', 'New Categories (2019)' )

@section ('content')


<h1> {{ __('emails.newCategories_hello', ['name' => $user->name]) }}</h1>

<p> {!! __('emails.newCategories_body') !!} </p>
<p> {!! __('emails.newCategories_explanations'!!} </p>

<p> {!! __('emails.registered_p_if_you_have_any_questions') !!} </p>



@endsection