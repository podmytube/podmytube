@extends('layouts.app')

@section('pageTitle', __('plans.title_upgrade_your_plan') )

@section('stripeJs')
  <!-- Load Stripe.js on your website. -->
  <script src="https://js.stripe.com/v3"></script>
@endsection

@section ('content')

  <div class="mb-5">
  </div>

  <div class="container text-center">

    
    <div id="stripe-error-message"></div>

    <div class="row">
      <div class="col border rounded m-1 p-3">

        @include("layouts.partials.stripeButton", [
          "planTitle" => __('plans.title_weekly_plan'),
          "planFeatures" => __('plans.weekly_plan_features'),
          "planLimits" => __('plans.weekly_plan_limits_reminder'),
          "buttonId" => "weeklyCheckoutButton",
          "planToSubscribe" => "weekly",
          "planPrice" => "9€",
          "successUrl" => env("APP_URL")."/success",
          "cancelUrl" => env("APP_URL")."/canceled"
        ])

      </div> <!-- /col-weekly -->
      <div class="col border rounded m-1 p-3">

          @include("layouts.partials.stripeButton", [
            "planTitle" => __('plans.title_daily_plan'),
            "planFeatures" => __('plans.daily_plan_features'),
            "planLimits" => __('plans.daily_plan_limits_reminder'),
            "buttonId" => "dailyCheckoutButton",
            "planToSubscribe" => "daily",
            "planPrice" => "29€",
            "successUrl" => env("APP_URL")."/success",
            "cancelUrl" => env("APP_URL")."/canceled"
          ])

      </div> <!-- /col-daily -->
    </div> <!-- /row -->
  </div>

@endsection



