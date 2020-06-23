@extends('emails.layout')

@section('mailTitle', $subject )

@section ('content')

<h1>
    {{ $subject }}
</h1>

<p>
    {{ __('emails.common_hello', ['name' => $user->name]) }}
</p>



--include ('emails.newsletters.2020-06-free-plan-update')--

@endsection