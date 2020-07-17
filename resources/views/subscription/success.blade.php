@extends('layouts.app')

@section('pageTitle', __('subscription.title_success') )

@section ('content')

<div class="mb-5">
</div>

<div class="container text-center">

  <h2>{{ __('subscription.congratulations') }}</h2>

  <p>{{ __('subscription.thanks_you_for_your_trust') }}</p>

  <a class="btn btn-primary" href="{{ route('home') }}" role="button">@lang("subscription.back_to_home")</a>

</div>

@endsection