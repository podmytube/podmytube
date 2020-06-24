@extends('emails.layout')

@section('mailTitle', $subject )

@section ('content')

<p> Hello {{ $user->name }} </p>

@include('emails.newsletters.'.$newsletterBody)

@endsection