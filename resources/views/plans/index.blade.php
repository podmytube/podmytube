@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

    <div class="container text-center">

    <h1> {{ __('messages.plans_title_upgrade_your_plan') }} </h1>
    
      <div class="row">

        <div class="col">
          <h2> {{ __('messages.plans_weekly_youtuber') }} </h2>

          <form action="/subscribe" method="POST">

            {{ csrf_field() }}

            <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-label="{{ __('messages.stripe_button_subscribe_to_weekly') }}"
                    data-key="{{ config('services.stripe.key') }}"
                    data-amount="900"
                    data-name="Weekly youtuber 9€"
                    data-description="Widget"
                    data-image="https://www.podmytube.com/pics/pmt2.png"
                    data-locale="auto"
                    data-currency="eur"
                    data-zip-code="true"
                    data-panel-label="Subscribe for">
            </script>
          </form>

        </div>
        <div class="col">
            <h2> {{ __('messages.plans_daily_youtuber') }} </h2>
  
            <form action="/subscribe" method="POST">
  
              {{ csrf_field() }}
  
              <script src="https://checkout.stripe.com/checkout.js" class="stripe-button btn-success"
                      data-label="{{ __('messages.stripe_button_subscribe_to_daily') }}"
                      data-key="{{ config('services.stripe.key') }}"
                      data-amount="2900"
                      data-name="Daily youtuber 29€"
                      data-description="Widget"
                      data-image="https://www.podmytube.com/pics/pmt2.png"
                      data-locale="auto"
                      data-currency="eur"
                      data-panel-label="Subscribe for">
              </script>
            </form>
            
          </div>
      </div>

    </div>

@endsection



