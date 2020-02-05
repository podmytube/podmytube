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
    
    <div id="error-message"></div>

    <div class="container">
      <div class="row pricing">
        <div class="col border rounded m-1 p-3">
          <h3> {{ __('plans.pricing_li_weekly_youtuber') }} </h3>
          <ul class="pricing-plan list-unstyled selected">
            <li class="pricing-price">
                <span id="monthly_price">{{ __('plans.pricing_li_weekly_youtuber_monthly_price') }}</span> {{ __('plans.pricing_li_per_month') }}
            </li>
            <li class="pricing-desc"> {!! __('plans.pricing_li_weekly_youtuber_intro') !!} </li>                  
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_podcast_filtering') }}">
                    {{ __('plans.pricing_li_podcast_filtering') }}
                </span>
            </li>
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_custom_url') }}">
                    {!! __('plans.pricing_li_custom_url') !!}                            
                </span>
            </li>
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_no_hidden_cost') }}">
                    {{ __('plans.pricing_li_no_hidden_cost') }}
                </span>
            </li>
            <li> {!! __('plans.pricing_li_setup_once_and_forget') !!} </li>
            <li> {{ __('plans.pricing_li_reactive_support') }} </li>
          </ul>
          @include("partials.stripeButton", [
            "buttonId" => "weeklyCheckoutButton",
            "planToSubscribe" => $stripePlans[$weekly],
            "successUrl" => env("APP_URL")."/success",
            "cancelUrl" => env("APP_URL")."/canceled"
          ])
        </div> <!-- /col-weekly -->
        <div class="col border rounded m-1 p-3">
          <h3> {{ __('plans.pricing_li_daily_youtuber') }} </h3>
          <ul class="pricing-plan list-unstyled">
            <li class="pricing-price">
                <span id="monthly_price">{{ __('plans.pricing_li_daily_youtuber_monthly_price') }}</span> {{ __('plans.pricing_li_per_month') }}
            </li>
            <li class="pricing-desc"> {!! __('plans.pricing_li_daily_youtuber_intro') !!} </li>
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_podcast_filtering') }}">
                    {{ __('plans.pricing_li_podcast_filtering') }}
                </span>
            </li>
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_custom_url') }}">
                    {!! __('plans.pricing_li_custom_url') !!}                            
                </span>
            </li>
            <li>
                <span class="fd_tooltip" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="{{ __('plans.pricing_li_data_no_hidden_cost') }}">
                    {{ __('plans.pricing_li_no_hidden_cost') }}
                </span>
            </li>
            <li> {!! __('plans.pricing_li_setup_once_and_forget') !!} </li>
            <li> {{ __('plans.pricing_li_reactive_support') }} </li>
          </ul>
          @include("partials.stripeButton", [
            "buttonId" => "dailyCheckoutButton",
            "planToSubscribe" => $stripePlans[$daily],
            "successUrl" => env("APP_URL")."/success",
            "cancelUrl" => env("APP_URL")."/canceled"
          ])
      </div> <!-- /col-daily -->
      </div>        
    </div>    
  </div>

@endsection



