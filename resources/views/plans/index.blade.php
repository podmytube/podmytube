@extends('layouts.app')

@section('pageTitle', __('plans.title_upgrade_your_plan') )

@section('stripeJs')
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
@endsection

@section ('content')
<div class="container py-5">
  <div class="card-deck mb-3 text-center">
    <div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">{{ __('plans.pricing_li_weekly_youtuber') }}</h4>
      </div>
      <div class="card-body">
        <h1 class="card-title pricing-card-title">{{ __('plans.pricing_li_weekly_youtuber_monthly_price') }} <small class="text-muted"> {{ __('plans.pricing_li_per_month') }}</small></h1>
        <ul class="list-unstyled my-4">
          <li class="my-3">{!! __('plans.pricing_li_weekly_youtuber_intro') !!}</li>
          <li class="my-2">{!! __('plans.pricing_li_no_time_limit') !!} </li>
          <li class="my-2">{{ __('plans.pricing_li_podcast_filtering') }}</li>
          <li class="my-2">{!! __('plans.pricing_li_reactive_support') !!} </li>
          <li class="my-2">{!! __('plans.pricing_li_setup_once_and_forget') !!} </li>
        </ul>
        @include("partials.stripeButton", [
        "buttonId" => "weeklyCheckoutButton",
        "planToSubscribe" => $stripePlans[$weekly],
        "successUrl" => env("APP_URL")."/success",
        "cancelUrl" => env("APP_URL")."/canceled"
        ])
      </div>
    </div>
    <div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h4 class="my-0 font-weight-normal">{{ __('plans.pricing_li_daily_youtuber') }}</h4>
      </div>
      <div class="card-body">
        <h1 class="card-title pricing-card-title">{{ __('plans.pricing_li_daily_youtuber_monthly_price') }} <small class="text-muted"> {{ __('plans.pricing_li_per_month') }}</small></h1>
        <ul class="list-unstyled my-4">
          <li class="my-3">{!! __('plans.pricing_li_daily_youtuber_intro') !!}</li>
          <li class="my-2">{!! __('plans.pricing_li_no_time_limit') !!} </li>
          <li class="my-2">{{ __('plans.pricing_li_podcast_filtering') }}</li>
          <li class="my-2">{!! __('plans.pricing_li_reactive_support') !!} </li>
          <li class="my-2">{!! __('plans.pricing_li_setup_once_and_forget') !!} </li>
        </ul>
        @include("partials.stripeButton", [
        "buttonId" => "dailyCheckoutButton",
        "planToSubscribe" => $stripePlans[$daily],
        "successUrl" => env("APP_URL")."/success",
        "cancelUrl" => env("APP_URL")."/canceled"
        ])
      </div>
    </div>
  </div>


  @endsection