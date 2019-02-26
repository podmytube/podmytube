@extends('layouts.app')

@section('pageTitle', __('subscription.title_failure') )

@section ('content')

  <div class="mb-5">
  </div>

  <div class="container text-center">

    <p>{{ __('subscription.problem_has_occur') }}</p>

    <a class="btn btn-primary" href="{{ route('home') }}" role="button">@lang("subscription.contact_support")</a>
    
  </div>

@endsection



