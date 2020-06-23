@extends('layouts.app')

@section('pageTitle', __('plans.title_upgrade_your_plan') )

@section('stripeJs')
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
@endsection

@section ('content')

<div class="container py-5">
  <div class="card-deck mb-3 text-center">
@foreach ($plans as $plan)
  <div class="card mb-4 shadow-sm">
    <div class="card-header">
      <h4 class="my-0 font-weight-normal">{!! __('plans.'.$plan->name) !!}</h4>
    </div>
    <div class="card-body">
      <h1 class="card-title pricing-card-title">{!! __('plans.price_monthly', ['price' => $plan->price]) !!}</h1>
      <ul class="list-unstyled my-4">
        {!! __('plans.episodes_per_month', ['nb' => $plan->nb_episodes_per_month]) !!}
        {!! __('plans.standard_paying_features') !!}
      </ul>
      
      @include("partials.stripeButton", [
      "buttonId" => $plan->name,
      "planToSubscribe" => $plan->stripe_id,
      "successUrl" => env("APP_URL")."/success",
      "cancelUrl" => env("APP_URL")."/cancelled"
      ])
    </div>
  </div>

@endforeach
  </div>
</div>

@endsection